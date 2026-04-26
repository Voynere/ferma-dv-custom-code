<?php
/**
 * Q promocode usage limits helpers.
 *
 * @package Theme
 */

function q_mark_promo_used_for_user( array $promo ): void {
	$usage_limit = (int) ( $promo['usage_limit'] ?? 0 );
	if ( $usage_limit <= 0 ) {
		// Лимит не задан — ничего не считаем.
		return;
	}

	if ( ! is_user_logged_in() ) {
		// Для гостей сейчас не считаем (как и в q_can_use_promo_for_user).
		return;
	}

	$phone = $promo['phone'] ?? '';
	if ( ! $phone ) {
		// Если нет телефона, не к чему привязаться.
		return;
	}

	$user_id  = get_current_user_id();
	$meta_key = '_q_promo_used_' . $promo['id'] . '_' . $phone;
	$used     = (int) get_user_meta( $user_id, $meta_key, true );

	update_user_meta( $user_id, $meta_key, $used + 1 );
}

function q_can_use_promo_for_user( array $promo ): bool {
	$usage_limit = (int) ( $promo['usage_limit'] ?? 0 );
	if ( $usage_limit <= 0 ) {
		// Лимит не задан — без ограничений.
		return true;
	}

	if ( ! is_user_logged_in() ) {
		// Если гостей тоже надо считать — дописать отдельную схему, сейчас пропускаем.
		return true;
	}

	$phone = $promo['phone'] ?? '';
	if ( ! $phone ) {
		return true;
	}

	$user_id  = get_current_user_id();
	$meta_key = '_q_promo_used_' . $promo['id'] . '_' . $phone;
	$used     = (int) get_user_meta( $user_id, $meta_key, true );

	return $used < $usage_limit;
}
