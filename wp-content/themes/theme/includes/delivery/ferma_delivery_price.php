<?php
date_default_timezone_set("Asia/Vladivostok");
require_once(get_template_directory() . "/includes/delivery/vendor/autoload.php");
use Location\Coordinate;
use Location\Polygon;

if(!function_exists('ferma_get_shops_by_coords')) {

	function ferma_get_cached_delivery_geojson() {
		static $geojson_cache = null;

		if ($geojson_cache !== null) {
			return $geojson_cache;
		}

		$geojson_cache = [];
		$file_geojson = get_field('delivery_geofile', 'option');
		if(!$file_geojson || !isset($file_geojson['url'])) {
			return $geojson_cache;
		}

		$json = file_get_contents($file_geojson['url']);
		$zones = json_decode($json, true);
		if (is_array($zones)) {
			$geojson_cache = $zones;
		}

		return $geojson_cache;
	}

	function ferma_get_cached_delivery_zone_rows() {
		static $zone_rows = null;

		if ($zone_rows !== null) {
			return $zone_rows;
		}

		$zone_rows = get_field('delivery_zones', 'option');
		if (!is_array($zone_rows)) {
			$zone_rows = [];
		}

		return $zone_rows;
	}

	function ferma_get_cached_delivery_zone_shops_map() {
		static $shops_map = null;

		if ($shops_map !== null) {
			return $shops_map;
		}

		$shops_map = [];
		foreach (ferma_get_cached_delivery_zone_rows() as $row) {
			if (!empty($row['delivery_zones_name'])) {
				$shops_map[$row['delivery_zones_name']] = $row['delivery_zones_shops'] ?? [];
			}
		}

		return $shops_map;
	}

	function ferma_get_shops_by_coords($coords) {
		static $shops_cache = [];

		if ( ! is_string( $coords ) || $coords === '' ) {
			return array();
		}

		if (isset($shops_cache[$coords])) {
			$cached = $shops_cache[ $coords ];
			return is_array( $cached ) ? $cached : array();
		}

		$coords = explode(",", $coords);
		
		$shops = false;
		
		if(isset($coords[0]) && isset($coords[1])) {
			$zones = ferma_get_cached_delivery_geojson();
			$shops_map = ferma_get_cached_delivery_zone_shops_map();
			
			$point = new Coordinate($coords[0], $coords[1]);
			
			foreach (($zones['features'] ?? []) as $feature) {
				$polygon = new Polygon();

				foreach ($feature['geometry']['coordinates'][0] as $coord) {
					$polygon->addPoint(new Coordinate($coord[1], $coord[0]));
				}
				
				if($polygon->contains($point)) {
					$zone = $feature['properties']['description'];
					$shops = $shops_map[$zone] ?? false;
					break;
				}
			}
		}

		if ( ! is_array( $shops ) ) {
			$shops = array();
		}

		$shops_cache[implode(",", $coords)] = $shops;

		return $shops;
	}
	
}

if(!function_exists('ferma_check_coords')) {

	function ferma_check_coords($coords) {
		static $check_cache = [];

		if (isset($check_cache[$coords])) {
			return $check_cache[$coords];
		}

		$coords = explode(",", $coords);
		
		$check = false;
		
		if(isset($coords[0]) && isset($coords[1])) {
			$zones = ferma_get_cached_delivery_geojson();
			
			$point = new Coordinate($coords[0], $coords[1]);
			
			foreach (($zones['features'] ?? []) as $feature) {
				$polygon = new Polygon();

				foreach ($feature['geometry']['coordinates'][0] as $coord) {
					$polygon->addPoint(new Coordinate($coord[1], $coord[0]));
				}
				
				if($polygon->contains($point)) {
					$check = true;
					break;
				}
			}
		}
		
		$check_cache[implode(",", $coords)] = $check;

		return $check;
	}
	
}

if(!function_exists('ferma_get_delivery_prices')) {
	add_action( 'wp_ajax_nopriv_get_delivery_prices', 'ferma_get_delivery_prices' );
	add_action( 'wp_ajax_get_delivery_prices', 'ferma_get_delivery_prices' );
	
	function ferma_get_delivery_prices() {
		//$coords = '43.111787507251414,131.88327396290603';
		if(isset($_POST['coords']) && is_array($_POST['coords'])) {
			$coords = $_POST['coords'][0] . "," . $_POST['coords'][1];
		} else {
			if ( is_user_logged_in() ) {
				$user_id = get_current_user_id();
				$row = get_user_meta( $user_id, 'delivery', true );
				$coords = get_user_meta( $user_id, 'coords', true );
			} else {
				if(isset($_COOKIE['billing_coords']) && $_COOKIE['billing_coords'] != '') {
					$coords = $_COOKIE['billing_coords'];
				} else if(isset($_COOKIE['coords']) && $_COOKIE['coords'] != '' && (!isset($_COOKIE['billing_coords']) || $_COOKIE['billing_coords'] == '')) {
					$coords = $_COOKIE['coords'];
				}
			}
		}
		
		if(!$coords) {
			wp_send_json_error(['error' => "Введите верный адрес доставки."]);
		}
		
		$coords = explode(",", $coords);
		
		$error = false;
		
		
		// Получение файла geojson
		$file_geojson = get_field('delivery_geofile', 'option');
		
		if(!$file_geojson || !isset($file_geojson['url'])) {
			$error = 'Файл доставки не найден';
		}
		
		$json = file_get_contents($file_geojson['url']);
		
		$zones = json_decode($json, true);
		
		if(!is_array($zones)) {
			$error = 'Ошибка обработки файла доставки';
		}
		
		// Текущая точка на карте
		$point = new Coordinate($coords[0], $coords[1]);
		
		// Текущая стоимость товаров в корзине
		$order_total = WC()->cart->get_cart_contents_total();
		
		// Время окончания приема доставки
		$times = [
			'morning' => [
				'end_time' => strtotime(date("Y-m-d 09:00:00")),
				'description' => '10—12'
			],
			'day' => [
				'end_time' => strtotime(date("Y-m-d 13:00:00")),
				'description' => '15—17'
			],
			'evening' => [
				'end_time' => strtotime(date("Y-m-d 17:00:00")),
				'description' => '19—22'
			]
		];
		
		$point2 = new Coordinate(43.3528568177245,132.17849396436128);
		
		// Текущее время
		$current_time = strtotime(date("Y-m-d H:i:s"));
		
		$prices = [];
		
		foreach ($zones['features'] as $feature) {
			$polygon = new Polygon();

			foreach ($feature['geometry']['coordinates'][0] as $coord) {
				$polygon->addPoint(new Coordinate($coord[1], $coord[0]));
			}
			
			if($polygon->contains($point2)) {
				//echo $feature['properties']['description'];
			}
			
			if($polygon->contains($point)) {
				//print_r($point);
				$zone = $feature['properties']['description'];
				//echo $zone;
				
				$prices['market'] = $zone;
				
				while( have_rows('delivery_zones', 'option') ) {
					the_row();
					
					if(get_sub_field('delivery_zones_name') == $zone) {
						
						$shops = get_sub_field('delivery_zones_shops');
						$prices['id'] = $shops[0];
						$prices['current'] = ($_COOKIE['delivery_day'] && $_COOKIE['delivery_time']) ? $_COOKIE['delivery_day'] . '_' . $_COOKIE['delivery_time'] : '';
						$prices['express'] = get_sub_field('delivery_zones_express');
						
						// Получаем стоимость утренней доставки
						while( have_rows('delivery_morning_prices') ) {
							the_row();
							
							$morning_sum_from = get_sub_field('delivery_morning_sum_from');
							$morning_sum_to = get_sub_field('delivery_morning_sum_to');
							$morning_price = get_sub_field('delivery_morning_price');
							
							if($order_total >= $morning_sum_from && $order_total <= $morning_sum_to) {
								if($current_time <= $times['morning']['end_time']) {
									$prices['today']['morning'] = [
										'price' => $morning_price,
										'description' => $times['morning']['description']
									];
								}
								
								$prices['tomorrow']['morning'] = [
									'price' => $morning_price,
									'description' => $times['morning']['description']
								];
							}
						}
						
						// Получаем стоимость дневной доставки
						while( have_rows('delivery_day_prices') ) {
							the_row();
							
							$day_sum_from = get_sub_field('delivery_day_sum_from');
							$day_sum_to = get_sub_field('delivery_day_sum_to');
							$day_price = get_sub_field('delivery_day_price');
							
							if($order_total >= $day_sum_from && $order_total <= $day_sum_to) {
								if($current_time <= $times['day']['end_time']) {
									$prices['today']['day'] = [
										'price' => $day_price,
										'description' => $times['day']['description']
									];
								}
								
								$prices['tomorrow']['day'] = [
									'price' => $day_price,
									'description' => $times['day']['description']
								];
							}
						}
						
						// Получаем стоимость вечерней доставки
						while( have_rows('delivery_evening_prices') ) {
							the_row();
							
							$evening_sum_from = get_sub_field('delivery_evening_sum_from');
							$evening_sum_to = get_sub_field('delivery_evening_sum_to');
							$evening_price = get_sub_field('delivery_evening_price');
							
							if($order_total >= $evening_sum_from && $order_total <= $evening_sum_to) {
								if($current_time <= $times['evening']['end_time']) {
									$prices['today']['evening'] = [
										'price' => $evening_price,
										'description' => $times['evening']['description']
									];
								}
								
								$prices['tomorrow']['evening'] = [
									'price' => $evening_price,
									'description' => $times['evening']['description']
								];
							}
						}
						
					}
				}
			}
		}
		
		if(count($prices) == 0) {
			$error = 'К сожалению, доставка не работает в выбранном месте.';
		}
		
		if($error) {
			wp_send_json_error(['error' => $error]);
		}
		
		wp_send_json_success($prices);
	}
}

if(!function_exists('ferma_get_delivery_price')) {
	
	function ferma_get_delivery_price($coords, $time) {
		// Получение файла geojson
		$file_geojson = get_field('delivery_geofile', 'option');
		$json = file_get_contents($file_geojson['url']);
		$zones = json_decode($json, true);
		
		// Текущая точка на карте
		$coords = explode(",", $coords);
		$point = new Coordinate($coords[0], $coords[1]);
		
		// Текущая стоимость товаров в корзине
		$order_total = WC()->cart->get_cart_contents_total();
		
		$price = 0;
		
		foreach ($zones['features'] as $feature) {
			$polygon = new Polygon();

			foreach ($feature['geometry']['coordinates'][0] as $coord) {
				$polygon->addPoint(new Coordinate($coord[1], $coord[0]));
			}
			
			if($polygon->contains($point)) {
				$zone = $feature['properties']['description'];
				
				while( have_rows('delivery_zones', 'option') ) {
					the_row();
					
					if(get_sub_field('delivery_zones_name') == $zone) {
						
						if($time == "today_express" || $time == "express") {
							return (int) get_sub_field('delivery_zones_express');
						}
						
						// Получаем стоимость доставки
						while( have_rows('delivery_'.$time.'_prices') ) {
							the_row();
								
							$delivery_sum_from = get_sub_field('delivery_'.$time.'_sum_from');
							$delivery_sum_to = get_sub_field('delivery_'.$time.'_sum_to');
							$delivery_price = get_sub_field('delivery_'.$time.'_price');
							
							if($order_total >= $delivery_sum_from && $order_total <= $delivery_sum_to) {
								$price = $delivery_price;
							}
						}
					}
				}
			}
		}
		
		return $price;
	}
}

if(!function_exists('ferma_get_delivery_express')) {
	
	function ferma_get_delivery_express($coords) {
		// Получение файла geojson
		$file_geojson = get_field('delivery_geofile', 'option');
		$json = file_get_contents($file_geojson['url']);
		$zones = json_decode($json, true);
		
		// Текущая точка на карте
		$coords = explode(",", $coords);
		$point = new Coordinate($coords[0], $coords[1]);
		
		// Текущая стоимость товаров в корзине
		$order_total = WC()->cart->get_cart_contents_total();
		
		$price = 0;
		
		foreach ($zones['features'] as $feature) {
			$polygon = new Polygon();

			foreach ($feature['geometry']['coordinates'][0] as $coord) {
				$polygon->addPoint(new Coordinate($coord[1], $coord[0]));
			}
			
			if($polygon->contains($point)) {
				$zone = $feature['properties']['description'];
				
				while( have_rows('delivery_zones', 'option') ) {
					the_row();
					
					if(get_sub_field('delivery_zones_name') == $zone) {
						$price = (int) get_sub_field('delivery_zones_express');
						$morning_start = strtotime(date("Y-m-d 08:00:00"));
						$morning_end = strtotime(date("Y-m-d 12:00:00"));
						$day_start = strtotime(date("Y-m-d 12:00:00"));
						$day_end = strtotime(date("Y-m-d 17:00:00"));
						$evening_start = strtotime(date("Y-m-d 19:00:00"));
						$evening_end = strtotime(date("Y-m-d 20:00:00"));
						
						$current_time = time();
						
						$price_add = 0;
					}
				}
			}
		}
		
		return $price;
	}
}

if(!function_exists('ferma_update_delivery')) {
	add_action( 'wp_ajax_nopriv_update_delivery_type', 'ferma_update_delivery_type' );
	add_action( 'wp_ajax_update_delivery_type', 'ferma_update_delivery_type' );
	function ferma_update_delivery_type()
	{
		$delivery_type = isset( $_POST['delivery_type'] ) ? sanitize_text_field( wp_unslash( $_POST['delivery_type'] ) ) : '';
		$delivery = explode("_", $delivery_type);
		
		if(isset($delivery[1]) && $delivery[1] != '' && isset($delivery[0]) && $delivery[0] != '') {
			$delivery_time = $delivery[1];
			$delivery_day  = $delivery[0];
			setcookie( 'delivery_time', $delivery_time, time() + 3600 * 24 * 7, '/' );
			setcookie( 'delivery_day', $delivery_day, time() + 3600 * 24 * 7, '/' );
			$_COOKIE['delivery_time'] = $delivery_time;
			$_COOKIE['delivery_day']  = $delivery_day;

			if ( function_exists( 'WC' ) && WC()->session ) {
				$allowed_time = array( 'morning', 'day', 'evening', 'express' );
				$t = sanitize_key( (string) $delivery_time );
				if ( in_array( $t, $allowed_time, true ) ) {
					WC()->session->set( 'ferma_ctx_delivery_time', $t );
				}
				WC()->session->set( 'ferma_ctx_delivery_day', sanitize_text_field( (string) $delivery_day ) );
			}

			wp_send_json_success(
				array(
					'delivery_day'  => $delivery_day,
					'delivery_time' => $delivery_time,
				)
			);
		}

		wp_send_json_error(
			array(
				'message' => 'Invalid delivery_type',
			),
			400
		);
	}
}

if ( ! function_exists( 'ferma_get_checkout_posted_array' ) ) {
	/**
	 * Parse WooCommerce `post_data` from update_order_review AJAX (reliable for fee calc vs cookies only).
	 */
	function ferma_get_checkout_posted_array() {
		$out = array();
		if ( isset( $_POST['post_data'] ) && is_string( $_POST['post_data'] ) ) {
			parse_str( wp_unslash( $_POST['post_data'] ), $out );
		}
		return is_array( $out ) ? $out : array();
	}
}

if ( ! function_exists( 'ferma_normalize_delivery_time_code' ) ) {
	/**
	 * Convert different delivery slot representations to morning|day|evening|express.
	 */
	function ferma_normalize_delivery_time_code( $raw ) {
		$v = is_string( $raw ) ? sanitize_text_field( (string) $raw ) : '';
		if ( $v === '' ) {
			return null;
		}
		$k = sanitize_key( $v );
		$allowed = array( 'morning', 'day', 'evening', 'express' );
		if ( in_array( $k, $allowed, true ) ) {
			return $k;
		}
		// today_day / tomorrow_evening / today_express
		if ( strpos( $k, '_' ) !== false ) {
			$parts = explode( '_', $k );
			$last  = end( $parts );
			if ( $last && in_array( $last, $allowed, true ) ) {
				return $last;
			}
		}
		// Human labels from billing_asdx1 fallback.
		$lower = function_exists( 'mb_strtolower' ) ? mb_strtolower( $v, 'UTF-8' ) : strtolower( $v );
		if ( strpos( $lower, 'экспресс' ) !== false ) {
			return 'express';
		}
		if ( strpos( $lower, '10' ) !== false && strpos( $lower, '12' ) !== false ) {
			return 'morning';
		}
		if ( strpos( $lower, '15' ) !== false && strpos( $lower, '17' ) !== false ) {
			return 'day';
		}
		if ( strpos( $lower, '19' ) !== false && strpos( $lower, '22' ) !== false ) {
			return 'evening';
		}
		if ( strpos( $lower, '19' ) !== false && strpos( $lower, '21' ) !== false ) {
			return 'evening';
		}
		return null;
	}
}

if ( ! function_exists( 'ferma_rehydrate_delivery_session' ) ) {
	/**
	 * On update_order_review: keep WC session in sync for fee recalculation.
	 * post_data (billing) when present, else request cookies (updated by update_delivery + JS before update_checkout).
	 * No early exit when post_data is missing: cookie-only requests still get the right slot.
	 */
	add_action( 'woocommerce_checkout_update_order_review', 'ferma_rehydrate_delivery_session', 1, 1 );
	function ferma_rehydrate_delivery_session( $post_data = '' ) {
		if ( ! function_exists( 'WC' ) || ! WC()->session ) {
			return;
		}
		$allowed = array( 'morning', 'day', 'evening', 'express' );
		$raw     = is_string( $post_data ) ? $post_data : '';
		if ( $raw === '' && ! empty( $_POST['post_data'] ) && is_string( $_POST['post_data'] ) ) {
			$raw = wp_unslash( $_POST['post_data'] );
		}
		$p = array();
		if ( is_string( $raw ) && $raw !== '' ) {
			parse_str( $raw, $p );
		}
		if ( ! is_array( $p ) ) {
			$p = array();
		}
		$cand = null;
		if ( isset( $p['billing']['ferma_ctx_delivery_time'] ) && (string) $p['billing']['ferma_ctx_delivery_time'] !== '' ) {
			$cand = ferma_normalize_delivery_time_code( (string) $p['billing']['ferma_ctx_delivery_time'] );
		} elseif ( ! empty( $p['ferma_ctx_delivery_time'] ) ) {
			$cand = ferma_normalize_delivery_time_code( (string) $p['ferma_ctx_delivery_time'] );
		} elseif ( isset( $p['billing']['billing_asdx1'] ) && (string) $p['billing']['billing_asdx1'] !== '' ) {
			$cand = ferma_normalize_delivery_time_code( (string) $p['billing']['billing_asdx1'] );
		} elseif ( ! empty( $p['billing_asdx1'] ) ) {
			$cand = ferma_normalize_delivery_time_code( (string) $p['billing_asdx1'] );
		} elseif ( ! empty( $_POST['delivery_type'] ) ) {
			$cand = ferma_normalize_delivery_time_code( (string) wp_unslash( $_POST['delivery_type'] ) );
		}
		if ( $cand && in_array( $cand, $allowed, true ) ) {
			WC()->session->set( 'ferma_ctx_delivery_time', $cand );
		} elseif ( isset( $_COOKIE['delivery_time'] ) && (string) $_COOKIE['delivery_time'] !== '' ) {
			$c2 = ferma_normalize_delivery_time_code( (string) wp_unslash( (string) $_COOKIE['delivery_time'] ) );
			if ( in_array( $c2, $allowed, true ) ) {
				WC()->session->set( 'ferma_ctx_delivery_time', $c2 );
			}
		}
		if ( isset( $p['billing']['ferma_ctx_delivery_day'] ) && (string) $p['billing']['ferma_ctx_delivery_day'] !== '' ) {
			WC()->session->set( 'ferma_ctx_delivery_day', sanitize_text_field( (string) $p['billing']['ferma_ctx_delivery_day'] ) );
		} elseif ( ! empty( $p['ferma_ctx_delivery_day'] ) ) {
			WC()->session->set( 'ferma_ctx_delivery_day', sanitize_text_field( (string) $p['ferma_ctx_delivery_day'] ) );
		} elseif ( isset( $_COOKIE['delivery_day'] ) && (string) $_COOKIE['delivery_day'] !== '' ) {
			WC()->session->set( 'ferma_ctx_delivery_day', sanitize_text_field( (string) wp_unslash( (string) $_COOKIE['delivery_day'] ) ) );
		}
	}
}

if ( ! function_exists( 'ferma_get_delivery_time_code_for_cart' ) ) {
	/**
	 * ACF / ferma_get_delivery_price expect morning|day|evening|express.
	 * 1) post_data (if parse_str has billing ferma) 2) delivery_time cookie 3) WC session 4) default evening.
	 * Cookie before session: JS/ update_delivery set cookies on the client; session can stay stale between visits.
	 */
	function ferma_get_delivery_time_code_for_cart() {
		$allowed = array( 'morning', 'day', 'evening', 'express' );
		$posted  = ferma_get_checkout_posted_array();
		$cand    = null;
		if ( isset( $posted['billing']['ferma_ctx_delivery_time'] ) && (string) $posted['billing']['ferma_ctx_delivery_time'] !== '' ) {
			$cand = ferma_normalize_delivery_time_code( (string) $posted['billing']['ferma_ctx_delivery_time'] );
		} elseif ( ! empty( $posted['ferma_ctx_delivery_time'] ) ) {
			$cand = ferma_normalize_delivery_time_code( (string) $posted['ferma_ctx_delivery_time'] );
		} elseif ( isset( $posted['billing']['billing_asdx1'] ) && (string) $posted['billing']['billing_asdx1'] !== '' ) {
			$cand = ferma_normalize_delivery_time_code( (string) $posted['billing']['billing_asdx1'] );
		} elseif ( ! empty( $posted['billing_asdx1'] ) ) {
			$cand = ferma_normalize_delivery_time_code( (string) $posted['billing_asdx1'] );
		} elseif ( ! empty( $_POST['delivery_type'] ) ) {
			$cand = ferma_normalize_delivery_time_code( (string) wp_unslash( $_POST['delivery_type'] ) );
		}
		if ( $cand && in_array( $cand, $allowed, true ) ) {
			return $cand;
		}
		if ( isset( $_COOKIE['delivery_time'] ) && (string) $_COOKIE['delivery_time'] !== '' ) {
			$c2 = ferma_normalize_delivery_time_code( (string) wp_unslash( (string) $_COOKIE['delivery_time'] ) );
			if ( in_array( $c2, $allowed, true ) ) {
				return $c2;
			}
		}
		if ( function_exists( 'WC' ) && WC()->session ) {
			$from = WC()->session->get( 'ferma_ctx_delivery_time' );
			if ( is_string( $from ) && $from !== '' ) {
				$c = sanitize_key( $from );
				if ( in_array( $c, $allowed, true ) ) {
					return $c;
				}
			}
		}
		return 'evening';
	}
}

if ( ! function_exists( 'ferma_get_coords_string_for_cart' ) ) {
	/**
	 * Coords for delivery fee: billing_coords first, then legacy coords, then default VVO point.
	 */
	function ferma_get_coords_string_for_cart() {
		if ( isset( $_COOKIE['billing_coords'] ) && (string) $_COOKIE['billing_coords'] !== '' ) {
			return sanitize_text_field( wp_unslash( (string) $_COOKIE['billing_coords'] ) );
		}
		if ( isset( $_COOKIE['coords'] ) && (string) $_COOKIE['coords'] !== '' ) {
			return sanitize_text_field( wp_unslash( (string) $_COOKIE['coords'] ) );
		}
		return '43.111787507251414,131.88327396290603';
	}
}

if(!function_exists('ferma_add_delivery_fee')) {
	add_action( 'woocommerce_cart_calculate_fees', 'ferma_add_delivery_fee', 10, 1 );
	function ferma_add_delivery_fee( $cart )
	{
		$coords = ferma_get_coords_string_for_cart();
		$time   = ferma_get_delivery_time_code_for_cart();

		$check_delivery = 0;
		
		if ( isset( $_COOKIE['delivery'] ) && (string) $_COOKIE['delivery'] === '0' ) {
			$check_delivery = 1;
		}
		
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$row = get_user_meta( $user_id, 'delivery', true );
			
			if ( $row == 0 ) {
				$check_delivery = 1;
			}
		}
		
		if ( $check_delivery == 1 ) {
			$delivery_price = ferma_get_delivery_price( $coords, $time );
			$cart->add_fee( __( "Доставка", "woocommerce" ), $delivery_price, false );
		}
	}
}

if(!function_exists('ferma_check_min_amount')) {
	add_action( 'woocommerce_check_cart_items', 'ferma_check_min_amount' );
	
	function ferma_check_min_amount() {
		$minimum_amount = 1000;

		$cart_subtotal = WC()->cart->subtotal;
		$cart_total = (float) WC()->cart->total;

		// Минимальная сумма только для доставки; самовывоз (cookie/user meta = 1) — без ограничения.
		if( $cart_subtotal < $minimum_amount && ferma_is_delivery() ) {
			
			$text_notice = sprintf(
				__("Минимальная сумма корзины %s, сейчас в вашем заказе всего %s.<br>Самовывоз без ограничений по минимальной сумме заказа.", "woocommerce"),
				wc_price( $minimum_amount ),
				wc_price( $cart_subtotal )
			);
			
			if ( is_cart() ) {
				wc_print_notice( $text_notice, 'error' );
			} else {
				wc_add_notice( $text_notice, 'error' );
			}
		}
	}
}

if(!function_exists('ferma_is_delivery')) {
	function ferma_is_delivery() {
		$delivery = false;
		
		if ( is_user_logged_in() ) {
			$user_id = get_current_user_id();
			$row = get_user_meta( $user_id, 'delivery', true );
			if($row == 0) {
				$delivery = true;
			}
			// Fallback: during checkout UX we can already have a delivery cookie before user meta catches up.
			if ( ! $delivery && isset( $_COOKIE['delivery'] ) && (string) $_COOKIE['delivery'] === '0' ) {
				$delivery = true;
			}
		} else {
			if(isset($_COOKIE['delivery']) && $_COOKIE['delivery'] == 0) {
				$delivery = true;
			}
		}
		
		return $delivery;
	}
}

/**
 * Бонусы на checkout: только доставка и только аккаунт с телефоном в логине (как в интеграции kilbil).
 */
if ( ! function_exists( 'ferma_checkout_bonuses_allowed' ) ) {
	function ferma_checkout_bonuses_allowed() {
		if ( ! is_user_logged_in() ) {
			return false;
		}
		if ( ! ferma_is_delivery() ) {
			return false;
		}
		$user = wp_get_current_user();
		if ( ! $user || ! $user->ID ) {
			return false;
		}
		// Legacy accounts can have non-phone user_login; billing_phone still identifies the Kilbil client.
		$digits = preg_replace( '/[^0-9]/', '', (string) $user->user_login );
		if ( strlen( $digits ) < 10 ) {
			$billing_phone = get_user_meta( (int) $user->ID, 'billing_phone', true );
			$digits        = preg_replace( '/[^0-9]/', '', (string) $billing_phone );
		}
		return strlen( $digits ) >= 10;
	}
}