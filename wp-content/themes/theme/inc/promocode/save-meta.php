<?php
/**
 * Q promocode meta save handler.
 *
 * @package Theme
 */

add_action(
	'save_post_q_promocode',
	function ( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		if ( isset( $_POST['q_code'] ) ) {
			$code = strtoupper( trim( $_POST['q_code'] ) );

			// любой код из 1–6 латинских букв/цифр.
			if ( preg_match( '/^[A-Z0-9]{1,9}$/', $code ) ) {
				update_post_meta( $post_id, '_q_code', $code );
			}
		}

		if ( isset( $_POST['q_gift_sku'] ) ) {
			update_post_meta( $post_id, '_q_gift_sku', sanitize_text_field( $_POST['q_gift_sku'] ) );
		}

		if ( isset( $_POST['q_discount_type'] ) ) {
			update_post_meta( $post_id, '_q_discount_type', $_POST['q_discount_type'] === 'absolute' ? 'absolute' : 'percent' );
		}

		if ( isset( $_POST['q_discount_val'] ) ) {
			update_post_meta( $post_id, '_q_discount_val', floatval( $_POST['q_discount_val'] ) );
		}

		if ( isset( $_POST['q_lifetime_hours'] ) ) {
			update_post_meta( $post_id, '_q_lifetime_hours', intval( $_POST['q_lifetime_hours'] ) );
		}

		if ( isset( $_POST['q_auto_add_gift'] ) ) {
			update_post_meta( $post_id, '_q_auto_add_gift', '1' );
		} else {
			delete_post_meta( $post_id, '_q_auto_add_gift' );
		}

		if ( isset( $_POST['q_usage_limit'] ) ) {
			update_post_meta( $post_id, '_q_usage_limit', intval( $_POST['q_usage_limit'] ) );
		}
	}
);
