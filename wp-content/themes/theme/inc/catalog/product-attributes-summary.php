<?php
/**
 * Single product attributes summary output.
 *
 * @package Theme
 */

// Удалил старый фильтр.
remove_filter( 'the_content', 'display_attributes_after_product_description', 10 );

// Добавил вывод атрибутов в .summary.
add_action( 'woocommerce_single_product_summary', 'custom_display_product_attributes_in_summary', 35 );
function custom_display_product_attributes_in_summary() {
	if ( ! function_exists( 'is_product' ) || ! is_product() ) {
		return;
	}

	global $product;

	$country = $product->get_attribute( 'pa_strana' );
	$energy  = $product->get_attribute( 'pa_energeticheskaya-cen' );
	$volume  = $product->get_attribute( 'pa_obyom-ml' );
	$weight  = $product->get_attribute( 'pa_ves-g' );
	$sostav  = $product->get_attribute( 'pa_sostav' );
	$usl     = $product->get_attribute( 'pa_usloviya-hraneniya' );
	$srok    = $product->get_attribute( 'pa_srok-godnosti' );
	$mesto   = $product->get_attribute( 'pa_mesto-proishojdeniya' );

	if ( ! $country && ! $energy && ! $volume && ! $weight && ! $sostav && ! $usl && ! $srok && ! $mesto ) {
		return;
	}

	echo '<div class="shop-ferma__attributes">';

	if ( $country ) {
		echo '<div class="product-attribute"><span class="product-attribute__text">Страна происхождения: </span>' . esc_html( $country ) . '</div>';
	}
	if ( $mesto ) {
		echo '<div class="product-attribute"><span class="product-attribute__text">Место происхождения: </span>' . esc_html( $mesto ) . '</div>';
	}
	if ( $energy ) {
		echo '<div class="product-attribute"><span class="product-attribute__text">Энергетическая ценность на 100 г, кКал: </span>' . esc_html( $energy ) . '</div>';
	}
	if ( $volume ) {
		echo '<div class="product-attribute"><span class="product-attribute__text">Объём, мл: </span>' . esc_html( $volume ) . '</div>';
	}
	if ( $weight ) {
		echo '<div class="product-attribute"><span class="product-attribute__text">Вес, гр: </span>' . esc_html( $weight ) . '</div>';
	}
	if ( $sostav ) {
		echo '<div class="product-attribute"><span class="product-attribute__text">Состав: </span>' . esc_html( $sostav ) . '</div>';
	}
	if ( $usl ) {
		echo '<div class="product-attribute"><span class="product-attribute__text">Условия хранения: </span>' . esc_html( $usl ) . '</div>';
	}
	if ( $srok ) {
		echo '<div class="product-attribute"><span class="product-attribute__text">Срок годности: </span>' . esc_html( $srok ) . '</div>';
	}

	echo '</div>';
}
