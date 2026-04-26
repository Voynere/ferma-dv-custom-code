<?php
/**
 * Kilbil bonuses: checkout fees and order bonus synchronization.
 *
 * @package Theme
 */

if ( ! function_exists( 'ferma_theme_kilbil_debug_log' ) ) {
	/**
	 * Пишет отладочную строку в файл; не вызывает fwrite при недоступном пути (без фатала).
	 * Сначала тема/kilbil.txt, при ошибке — wp-uploads/ferma-kilbil-debug.log.
	 */
	function ferma_theme_kilbil_debug_log( $chunk ) {
		if ( ! is_string( $chunk ) ) {
			$chunk = (string) $chunk;
		}
		$paths = array( trailingslashit( get_stylesheet_directory() ) . 'kilbil.txt' );
		if ( function_exists( 'wp_upload_dir' ) ) {
			$upload = wp_upload_dir();
			if ( empty( $upload['error'] ) && ! empty( $upload['basedir'] ) ) {
				$paths[] = trailingslashit( $upload['basedir'] ) . 'ferma-kilbil-debug.log';
			}
		}
		foreach ( $paths as $path ) {
			$fp = @fopen( $path, 'ab' );
			if ( $fp !== false && is_resource( $fp ) ) {
				@fwrite( $fp, $chunk );
				@fclose( $fp );
				return;
			}
		}
	}
}

if ( ! function_exists( 'ferma_checkout_get_user_bonus_balance' ) ) {
	/**
	 * Returns current Kilbil balance for a user.
	 *
	 * Uses user_login as primary identifier and billing_phone as fallback.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return int
	 */
	function ferma_checkout_get_user_bonus_balance( $user_id = 0 ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 ) {
			$user_id = (int) get_current_user_id();
		}
		if ( $user_id <= 0 ) {
			return 0;
		}

		$user_info = get_userdata( $user_id );
		if ( ! $user_info ) {
			return 0;
		}

		$search_value = preg_replace( '/[^0-9]/', '', (string) $user_info->user_login );
		if ( strlen( $search_value ) < 10 ) {
			$billing_phone = get_user_meta( $user_id, 'billing_phone', true );
			$search_value  = preg_replace( '/[^0-9]/', '', (string) $billing_phone );
		}
		if ( strlen( $search_value ) < 10 ) {
			return 0;
		}

		$arr  = array( 'search_mode' => 0, 'search_value' => $search_value );
		$url  = 'https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7';
		$curl = curl_init( $url );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, wp_json_encode( $arr ) );
		$json_response = curl_exec( $curl );
		curl_close( $curl );

		$obj = json_decode( $json_response );
		if ( isset( $obj->balance ) ) {
			return (int) $obj->balance;
		}

		return 0;
	}
}

if ( ! function_exists( 'ferma_checkout_get_kilbil_client_id' ) ) {
	/**
	 * Returns Kilbil client_id for a user.
	 *
	 * @param int $user_id WordPress user ID.
	 * @return int
	 */
	function ferma_checkout_get_kilbil_client_id( $user_id = 0 ) {
		$user_id = (int) $user_id;
		if ( $user_id <= 0 ) {
			$user_id = (int) get_current_user_id();
		}
		if ( $user_id <= 0 ) {
			return 0;
		}

		$user_info = get_userdata( $user_id );
		if ( ! $user_info ) {
			return 0;
		}

		$search_value = preg_replace( '/[^0-9]/', '', (string) $user_info->user_login );
		if ( strlen( $search_value ) < 10 ) {
			$billing_phone = get_user_meta( $user_id, 'billing_phone', true );
			$search_value  = preg_replace( '/[^0-9]/', '', (string) $billing_phone );
		}
		if ( strlen( $search_value ) < 10 ) {
			return 0;
		}

		$arr  = array( 'search_mode' => 0, 'search_value' => $search_value );
		$url  = 'https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7';
		$curl = curl_init( $url );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, wp_json_encode( $arr ) );
		$json_response = curl_exec( $curl );
		curl_close( $curl );

		$obj = json_decode( $json_response );
		if ( isset( $obj->client_id ) ) {
			return (int) $obj->client_id;
		}

		return 0;
	}
}

add_action( 'woocommerce_order_status_on-hold', 'callback_check_bonus' );
function callback_check_bonus( $order_id ) {
	date_default_timezone_set( 'Asia/Vladivostok' );

	$bonus = get_post_meta( $order_id, 'billing_bonus', true );
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}
	$total   = $order->get_total();
	$percent = $total / 100 * 30;

	if ( $bonus > $percent ) {
		update_post_meta( $order_id, 'billing_bonus', $percent );
		$bonus = $percent;
	}

	ferma_theme_kilbil_debug_log(
		"\n---INPUT DATA---\n" . date( 'Y-m-d H:i:s' ) . "\n" . $order_id . ' - ' . $percent . ' - ' . $total . "\n"
	);

	if ( (int) $bonus > 0 ) {
		$real_bonus = get_real_kilbil_bonus();

		if ( $real_bonus == 0 ) {
			update_post_meta( $order_id, 'billing_bonus', 0 );
			unset( $_COOKIE['balik'] );
			setcookie( 'balik', null, -1, '/' );
		} elseif ( $real_bonus < $bonus ) {
			update_post_meta( $order_id, 'billing_bonus', $real_bonus );
			unset( $_COOKIE['balik'] );
			setcookie( 'balik', null, -1, '/' );
		} elseif ( $real_bonus >= $bonus ) {
			update_post_meta( $order_id, 'billing_bonus', $bonus );
			unset( $_COOKIE['balik'] );
			setcookie( 'balik', null, -1, '/' );
		} else {
			update_post_meta( $order_id, 'billing_bonus', 0 );
			unset( $_COOKIE['balik'] );
			setcookie( 'balik', null, -1, '/' );
		}
	}
}

add_action( 'woocommerce_order_status_completed', 'callback_order_bonus' );
function callback_order_bonus( $order_id ) {
	$bonus_added = get_field( 'order_bonus_added', $order_id );
	$samovivoz   = get_post_meta( $order_id, 'billing_samoviziv', true );
	if ( ! $bonus_added && $samovivoz == '' ) {
		$order      = wc_get_order( $order_id );
		$user_id    = $order->get_user_id();
		$total      = $order->get_total();
		$percent    = 5;
		$fulltotal  = $total * ( $percent / 100 );
		$userbonus  = ferma_checkout_get_kilbil_client_id( $user_id );
		if ( $userbonus <= 0 ) {
			return;
		}

		$arr     = array( 'client_id' => $userbonus, 'bonus_in' => $fulltotal );
		$url     = 'https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7';
		$content = json_encode( $arr );
		$curl    = curl_init( $url );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $content );
		$json_response = curl_exec( $curl );
		$status        = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		$obj           = json_decode( $json_response );
		curl_close( $curl );

		$data  = $order->get_data();
		$bonus = get_post_meta( $order->get_id(), 'billing_bonus', true );
		if ( $bonus > 0 ) {
			ferma_theme_kilbil_debug_log(
				"\n---INPUT DATA---\n" . date( 'Y-m-d H:i:s' ) . "\n" . 'Начисление бонуса: ' . $bonus . "\n"
			);
			$arr = array( 'client_id' => $userbonus, 'bonus_out' => $bonus );

			$url     = 'https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7';
			$content = json_encode( $arr );
			$curl    = curl_init( $url );
			curl_setopt( $curl, CURLOPT_HEADER, false );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, $content );
			$json_response = curl_exec( $curl );
			$status        = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
			$obj           = json_decode( $json_response );
			curl_close( $curl );
			unset( $_COOKIE['balik'] );
			setcookie( 'balik', null, -1, '/' );
			echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
		}

		update_field( 'order_bonus_added', 1, $order_id );
	}
}

function get_real_kilbil_bonus() {
	return (int) ferma_checkout_get_user_bonus_balance( get_current_user_id() );
}

// add_action( 'woocommerce_order_status_completed', 'callback_function_name' );
function callback_function_name() {
	$url     = $_SERVER['REQUEST_URI'];
	$parts   = parse_url( $url );
	parse_str( $parts['query'], $query );
	$order_id = $query['post'][0];
	$order    = wc_get_order( $order_id );
	$user_id  = $order->get_user_id();
	$total    = $order->get_total();
	$percent  = 5;
	$fulltotal = $total * ( $percent / 100 );
	$user_info = get_userdata( $user_id );
	$userlogin = $user_info->user_login;
	$content   = preg_replace( '/[^0-9]/', '', $userlogin );

	if ( strlen( $content ) < 10 ) {
		return false;
	}

	$arr     = array( 'search_mode' => 0, 'search_value' => $content );
	$url     = 'https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7';
	$content = json_encode( $arr );
	$curl    = curl_init( $url );
	curl_setopt( $curl, CURLOPT_HEADER, false );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
	curl_setopt( $curl, CURLOPT_POST, true );
	curl_setopt( $curl, CURLOPT_POSTFIELDS, $content );
	$json_response = curl_exec( $curl );
	$status        = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
	$obj           = json_decode( $json_response );
	$userbonus     = $obj->{'client_id'};
	curl_close( $curl );

	$arr     = array( 'client_id' => $userbonus, 'bonus_in' => $fulltotal );
	$url     = 'https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7';
	$content = json_encode( $arr );
	$curl    = curl_init( $url );
	curl_setopt( $curl, CURLOPT_HEADER, false );
	curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
	curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
	curl_setopt( $curl, CURLOPT_POST, true );
	curl_setopt( $curl, CURLOPT_POSTFIELDS, $content );
	$json_response = curl_exec( $curl );
	$status        = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
	$obj           = json_decode( $json_response );
	curl_close( $curl );
	echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
	$data  = $order->get_data();
	$bonus = get_post_meta( $order->get_id(), 'billing_bonus', true );
	if ( $bonus > 0 ) {
		$arr = array( 'client_id' => $userbonus, 'bonus_out' => $bonus );

		$url     = 'https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7';
		$content = json_encode( $arr );
		$curl    = curl_init( $url );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $content );
		$json_response = curl_exec( $curl );
		$status        = curl_getinfo( $curl, CURLINFO_HTTP_CODE );
		$obj           = json_decode( $json_response );
		curl_close( $curl );
		unset( $_COOKIE['balik'] );
		setcookie( 'balik', null, -1, '/' );
		echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
	}
}

add_action( 'woocommerce_cart_calculate_fees', 'truemisha_add_fee_paypal', 15 );
function truemisha_add_fee_paypal( $cart ) {
	if ( function_exists( 'ferma_checkout_bonuses_allowed' ) && ! ferma_checkout_bonuses_allowed() ) {
		return;
	}
	if ( isset( $_COOKIE['balik'] ) ) {
		$user_id   = get_current_user_id();
		$userbonus = ferma_checkout_get_user_bonus_balance( $user_id );

		$real_balik = $cart->subtotal * 0.3;
		if ( $_COOKIE['balik'] != $real_balik ) {
			$_COOKIE['balik'] = $real_balik;
		}

		if ( (int) $userbonus >= $_COOKIE['balik'] ) {
			WC()->cart->add_fee( 'Бонусы', -$_COOKIE['balik'] );
		} else {
			if ( (int) $userbonus > 0 ) {
				WC()->cart->add_fee( 'Бонусы', -$userbonus );
			}
		}
	}
}

add_action( 'woocommerce_cart_calculate_fees', 'truemisha_add_fee_paypal1', 15 );
function truemisha_add_fee_paypal1() {
	if ( isset( $_COOKIE['discount'] ) ) {
		if ( $_COOKIE['discount'] == 0 ) {

		} else {
			WC()->cart->add_fee( 'Скидка', -$_COOKIE['discount'] );
		}
	}
}

add_action( 'woocommerce_cart_item_removed', 'remove_discount_cookie_on_cart_item_removed', 10, 2 );
function remove_discount_cookie_on_cart_item_removed( $cart_item_key, $cart ) {
	unset( $cart_item_key, $cart );
	// проверяем, есть ли установленная cookie
	if ( isset( $_COOKIE['discount'] ) ) {
		// удаляем cookie
		setcookie( 'discount', '', time() - 3600, '/' );
	}
}
