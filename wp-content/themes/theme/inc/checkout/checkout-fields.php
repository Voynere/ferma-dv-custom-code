<?php
/**
 * Checkout field shaping and update-totals behavior.
 *
 * @package Theme
 */

add_filter( 'woocommerce_checkout_fields', 'custom_checkout_fields' );
function custom_checkout_fields( $fields ) {
	// Получаем значение адреса из сессии или cookie
	if ( is_user_logged_in() ) {
		$user_id             = get_current_user_id();
		$address2            = get_user_meta( $user_id, 'billing_delivery', true );
		$coment_address      = get_user_meta( $user_id, 'billing_comment', true );
		$coment_samoviziv    = get_user_meta( $user_id, 'billing_samoviziv', true );
		$coment_address_type = isset( $_COOKIE['time_type'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['time_type'] ) ) : '';
	} else {
		$address2            = isset( $_COOKIE['billing_delivery'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['billing_delivery'] ) ) : '';
		$coment_address      = isset( $_COOKIE['billing_comment'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['billing_comment'] ) ) : '';
		$coment_samoviziv    = isset( $_COOKIE['billing_samoviziv'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['billing_samoviziv'] ) ) : '';
		$coment_address_type = isset( $_COOKIE['time_type'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['time_type'] ) ) : '';
	}
	if ( $coment_address_type == 1 ) {
		$result_mes = '15:00-17:00';
	} elseif ( $coment_address_type == 2 ) {
		$result_mes = '19:00-21:00';
	}
	if ( $_COOKIE['time'] == 1 ) {
		$message = 'Сегодня';
	} elseif ( $_COOKIE['time'] == 2 ) {
		$message = 'Завтра';
	}
	if ( $_COOKIE['time_type'] = 1 ) {
		$time_of_type = '15:00-17:00';
	}
	if ( $_COOKIE['time_type'] = 2 ) {
		$time_of_type = '19:00-21:00';
	}

	// $delivery_price = WC()->cart->cart_contents_total

	// Добавляем значение адреса в поле billing_address_2
	$fields['billing']['billing_delivery']['default'] = $address2;
	$current_time                                     = current_time( 'H:i' );
	// Сравнение строк HH:MM; раньше при ровно 14:00 и 20:00 ни одна ветка не срабатывала — поле могло быть без нашего class.
	if ( $current_time >= '20:00' ) {
		$fields['billing']['billing_asdx1'] = array(
			'label'    => __( 'Время доставки', 'woocommerce' ),
			'type'     => 'select',
			'class'    => array( 'update_totals_on_change' ),
			'options'  => array(
				'Завтра, 15:00-17:00' => __( 'Завтра, 15:00-17:00', 'woocommerce' ),
				'Завтра, 19:00-21:00' => __( 'Завтра, 19:00-21:00', 'woocommerce' ),
			),
			'default'  => $message . ', ' . $result_mes,
			'required' => true,
		);
	} elseif ( $current_time >= '14:00' ) {
		$fields['billing']['billing_asdx1'] = array(
			'label'    => __( 'Время доставки', 'woocommerce' ),
			'type'     => 'select',
			'class'    => array( 'update_totals_on_change' ),
			'options'  => array(
				'Сегодня, 19:00-21:00' => __( 'Сегодня, 19:00-21:00' ),
				'Завтра, 15:00-17:00'  => __( 'Завтра, 15:00-17:00', 'woocommerce' ),
				'Завтра, 19:00-21:00'  => __( 'Завтра, 19:00-21:00', 'woocommerce' ),
			),
			'default'  => $message . ', ' . $result_mes,
			'required' => true,
		);
	} else {
		$fields['billing']['billing_asdx1'] = array(
			'label'    => __( 'Время доставки', 'woocommerce' ),
			'type'     => 'select',
			'class'    => array( 'update_totals_on_change' ),
			'options'  => array(
				'Сегодня, 15:00-17:00' => __( 'Сегодня, 15:00-17:00', 'woocommerce' ),
				'Сегодня, 19:00-21:00' => __( 'Сегодня, 19:00-21:00' ),
				'Завтра, 15:00-17:00'  => __( 'Завтра, 15:00-17:00', 'woocommerce' ),
				'Завтра, 19:00-21:00'  => __( 'Завтра, 19:00-21:00', 'woocommerce' ),
			),
			'default'  => $message . ', ' . $result_mes,
			'required' => true,
		);
	}

	// $fields['billing']['billing_asdx1']['options'] = [];

	$fields['billing']['billing_comment']['default']      = $coment_address;
	$fields['billing']['billing_samoviziv']['default']    = $coment_samoviziv;
	$fields['billing']['billing_comment_zakaz']['default'] = $_COOKIE['data_check'];

	$current_time = current_time( 'H:i' ); // Получаем текущее местное время в формате часы:минуты
	$start_time   = strtotime( '10:00' ); // Устанавливаем начальное время
	$end_time     = strtotime( '21:00' ); // Устанавливаем конечное время
	$interval     = 2 * 60 * 60; // Устанавливаем интервал в 2 часа

	$additional_options = array(
		'Завтра, 10:00-12:00' => 'Завтра, 10:00-12:00',
		'Завтра, 12:00-14:00' => 'Завтра, 12:00-14:00',
		'Завтра, 14:00-16:00' => 'Завтра, 14:00-16:00',
		'Завтра, 16:00-18:00' => 'Завтра, 16:00-18:00',
		'Завтра, 18:00-20:00' => 'Завтра, 18:00-20:00',
		'Завтра, 20:00-21:00' => 'Завтра, 20:00-21:00',
	);
	$options            = array();
	for ( $time = $start_time; $time <= $end_time; $time += $interval ) {
		$start = date( 'H:i', $time ); // Преобразуем начальное время в формат часы:минуты
		$end   = date( 'H:i', $time + $interval ); // Преобразуем конечное время в формат часы:минуты
		if ( $end > '21:00' ) { // Проверяем, если конечное время больше, чем 21:00
			$end = '21:00'; // Устанавливаем конечное время на 21:00
		}
		$option_time = 'Сегодня, ' . $start . '-' . $end; // Формируем строку вида "часы:минуты-часы:минуты"
		if ( $option_time == $_COOKIE['data_of_samoviviz'] ) {
			// echo 1;
		}
		if ( $start > $current_time ) { // Проверяем, если начальное время больше, чем текущее время
			$options[ $option_time ] = $option_time;
		}
	}
	$options                                      = array_merge( $options, $additional_options );
	$fields['billing']['billing_type_delivery_sam'] = array(
		'label'    => __( 'Время самовывоза', 'woocommerce' ),
		'type'     => 'select',
		'class'    => array( 'update_totals_on_change' ),
		'options'  => $options,
		'required' => true,
		'default'  => urldecode( $_COOKIE['data_of_samoviviz'] ),
	);

	if ( ! is_user_logged_in() && empty( $_COOKIE['delivery'] ) ) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
			unset( $fields['billing']['billing_samoviziv'] );
		}
		if ( isset( $fields['billing']['billing_type_delivery_sam'] ) ) {
			unset( $fields['billing']['billing_type_delivery_sam'] );
		}
	}
	if ( is_user_logged_in() && get_user_meta( get_current_user_id(), 'delivery', true ) == '1' ) {
		foreach ( array( 'billing_delivery', 'billing_comment', 'billing_asdx1', 'billing_dev_1', 'billing_dev_2', 'billing_dev_3', 'billing_dev_4', 'billing_type_delivery', 'billing_comment_zakaz' ) as $key ) {
			if ( isset( $fields['billing'][ $key ] ) ) {
				unset( $fields['billing'][ $key ] );
			}
		}
	}
	if ( ( is_user_logged_in() && get_user_meta( get_current_user_id(), 'delivery', true ) == '0' ) || ( is_user_logged_in() && empty( get_user_meta( get_current_user_id(), 'delivery', true ) ) ) ) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
			unset( $fields['billing']['billing_samoviziv'] );
		}
		if ( isset( $fields['billing']['billing_type_delivery_sam'] ) ) {
			unset( $fields['billing']['billing_type_delivery_sam'] );
		}
	}
	if ( ! is_user_logged_in() && $_COOKIE['delivery'] == '1' ) {
		foreach ( array( 'billing_delivery', 'billing_comment', 'billing_asdx1', 'billing_dev_1', 'billing_dev_2', 'billing_dev_3', 'billing_dev_4', 'billing_type_delivery', 'billing_comment_zakaz' ) as $key ) {
			if ( isset( $fields['billing'][ $key ] ) ) {
				unset( $fields['billing'][ $key ] );
			}
		}
	}
	if ( ! is_user_logged_in() && $_COOKIE['delivery'] == '0' ) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
			unset( $fields['billing']['billing_samoviziv'] );
		}
		if ( isset( $fields['billing']['billing_type_delivery_sam'] ) ) {
			unset( $fields['billing']['billing_type_delivery_sam'] );
		}
	}

	// Syncs with delivery window select + cookies for WC AJAX recalculation (ferma_add_delivery_fee).
	$fields['billing']['ferma_ctx_delivery_time'] = array(
		'type'              => 'hidden',
		'required'          => false,
		'class'             => array( 'ferma-ctx-delivery' ),
		'default'           => isset( $_COOKIE['delivery_time'] ) ? sanitize_text_field( wp_unslash( (string) $_COOKIE['delivery_time'] ) ) : '',
		'custom_attributes' => array(
			'data-ferma-ctx' => 'delivery_time',
		),
	);
	$fields['billing']['ferma_ctx_delivery_day']  = array(
		'type'              => 'hidden',
		'required'          => false,
		'class'             => array( 'ferma-ctx-delivery' ),
		'default'           => isset( $_COOKIE['delivery_day'] ) ? sanitize_text_field( wp_unslash( (string) $_COOKIE['delivery_day'] ) ) : '',
		'custom_attributes' => array(
			'data-ferma-ctx' => 'delivery_day',
		),
	);

	return $fields;
}

add_filter( 'woocommerce_checkout_fields', 'ferma_checkout_fields_update_totals_class', 999 );
function ferma_checkout_fields_update_totals_class( $fields ) {
	if ( ! isset( $fields['billing'] ) || ! is_array( $fields['billing'] ) ) {
		return $fields;
	}
	foreach ( array( 'billing_asdx1', 'billing_type_delivery_sam' ) as $key ) {
		if ( ! isset( $fields['billing'][ $key ] ) ) {
			continue;
		}
		if ( ! isset( $fields['billing'][ $key ]['class'] ) ) {
			$fields['billing'][ $key ]['class'] = array();
		} elseif ( ! is_array( $fields['billing'][ $key ]['class'] ) ) {
			$fields['billing'][ $key ]['class'] = array( (string) $fields['billing'][ $key ]['class'] );
		}
		if ( ! in_array( 'update_totals_on_change', $fields['billing'][ $key ]['class'], true ) ) {
			$fields['billing'][ $key ]['class'][] = 'update_totals_on_change';
		}
	}
	return $fields;
}

add_filter( 'woocommerce_form_field_args', 'ferma_form_field_update_totals_class', 10, 3 );
function ferma_form_field_update_totals_class( $args, $key, $value ) {
	if ( ! in_array( $key, array( 'billing_asdx1', 'billing_type_delivery_sam' ), true ) ) {
		return $args;
	}
	if ( ! isset( $args['class'] ) ) {
		$args['class'] = array();
	} elseif ( ! is_array( $args['class'] ) ) {
		$args['class'] = array( (string) $args['class'] );
	}
	if ( ! in_array( 'update_totals_on_change', $args['class'], true ) ) {
		$args['class'][] = 'update_totals_on_change';
	}
	return $args;
}

add_action( 'wp_footer', 'ferma_checkout_delivery_totals_footer', 50 );
function ferma_checkout_delivery_totals_footer() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
		return;
	}
	if ( ! function_exists( 'WC' ) ) {
		return;
	}
	?>
<script>
(function() {
	if ( typeof jQuery === 'undefined' ) {
		return;
	}
	jQuery( function( $ ) {
		function fermaAddTotalsClass() {
			$( '#billing_asdx1_field, #billing_type_delivery_sam_field' ).addClass( 'update_totals_on_change' );
		}
		fermaAddTotalsClass();
		$( document.body ).on( 'updated_checkout', fermaAddTotalsClass );
	} );
})();
</script>
	<?php
}

add_action( 'woocommerce_checkout_update_order_review', 'custom_woocommerce_checkout_update_order_review', 10, 1 );
function custom_woocommerce_checkout_update_order_review( $post_data ) {
	unset( $post_data );
	WC()->session->set( 'custom_cache', false );
}
