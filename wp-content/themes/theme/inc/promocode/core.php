<?php
/**
 * Q promocode core helpers.
 *
 * @package Theme
 */

function q_reset_promo_after_checkout( $order_id ) {
	// Сбрасываем активный промокод.
	WC()->session->__unset( 'q_active_promo' );

	// Убираем cookie промокода, чтобы он не применялся автоматически.
	if ( isset( $_COOKIE['ferma_promo_code'] ) ) {
		setcookie( 'ferma_promo_code', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
		if ( SITECOOKIEPATH !== COOKIEPATH ) {
			setcookie( 'ferma_promo_code', '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN );
		}
	}
}

function q_get_local_promocode( $code ) {
	$code = strtoupper( trim( $code ) );

	if ( ! preg_match( '/^[A-Z0-9]{1,9}$/', $code ) ) {
		return false;
	}

	$q = new WP_Query(
		array(
			'post_type'      => 'q_promocode',
			'posts_per_page' => 1,
			'meta_query'     => array(
				array(
					'key'   => '_q_code',
					'value' => $code,
				),
			),
			'post_status'    => 'publish',
		)
	);

	if ( ! $q->have_posts() ) {
		return false;
	}

	$post = $q->posts[0];
	$id   = $post->ID;

	$promo = array(
		'id'            => $id,
		'code'          => get_post_meta( $id, '_q_code', true ),
		'gift_sku'      => get_post_meta( $id, '_q_gift_sku', true ),
		'discount_type' => get_post_meta( $id, '_q_discount_type', true ),
		'discount_val'  => (float) get_post_meta( $id, '_q_discount_val', true ),
		'lifetime'      => (int) get_post_meta( $id, '_q_lifetime_hours', true ),
		'usage_limit'   => (int) get_post_meta( $id, '_q_usage_limit', true ),
		'created'       => get_post_time( 'U', true, $id ),
	);

	// срок действия.
	if ( $promo['lifetime'] > 0 && ( time() - $promo['created'] ) > $promo['lifetime'] * 3600 ) {
		return false;
	}

	// просто записываем телефон, не проверяя лимиты.
	$phone = '';
	if ( is_user_logged_in() ) {
		$customer = WC()->customer;
		if ( $customer ) {
			$phone = preg_replace( '/\D+/', '', $customer->get_billing_phone() );
		}
	}
	$promo['phone'] = $phone;

	return $promo;
}
