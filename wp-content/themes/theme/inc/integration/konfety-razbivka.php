<?php
/**
 * Admin-only utility to set razbivka_vesa for "konfety" products.
 *
 * @package Theme
 */

add_action( 'init', 'fdv_set_razbivka_for_konfety' );
function fdv_set_razbivka_for_konfety() {
	// Выполняем только для админа и только в админке, чтобы не ловить лишних запусков на фронте
	if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
		return;
	}

	// Чтобы скрипт не крутился вечно – запускаем один раз по GET-параметру
	if ( empty( $_GET['run_konfety_razbivka'] ) ) {
		return;
	}

	// ВАЖНО: тут укажи реальный slug категории "Конфеты"
	$category_slug = 'konfety';

	$args = array(
		'post_type'      => 'product',
		'posts_per_page' => -1,
		'post_status'    => 'publish',
		'tax_query'      => array(
			array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $category_slug,
			),
		),
		'fields'         => 'ids',
	);

	$products = get_posts( $args );

	if ( empty( $products ) ) {
		error_log( 'fdv_set_razbivka_for_konfety: товаров не найдено.' );
		return;
	}

	foreach ( $products as $product_id ) {
		// если поле ACF, лучше использовать update_field, но можно и update_post_meta
		update_field( 'razbivka_vesa', 'да', $product_id );
		// или:
		// update_post_meta( $product_id, 'razbivka_vesa', 'да' );
	}

	error_log( 'fdv_set_razbivka_for_konfety: обновлено товаров: ' . count( $products ) );
}
