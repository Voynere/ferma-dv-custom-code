<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.1
 */

defined( 'ABSPATH' ) || exit;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
$post_thumbnail_id = $product->get_image_id();
$wrapper_classes   = apply_filters(
	'woocommerce_single_product_image_gallery_classes',
	array(
		'woocommerce-product-gallery',
		'woocommerce-product-gallery--' . ( $post_thumbnail_id ? 'with-images' : 'without-images' ),
		'woocommerce-product-gallery--columns-' . absint( $columns ),
		'images',
	)
);

$green_friday_products = get_green_friday_products();

$is_green_friday = false;

foreach($green_friday_products['good_ids_with_discount'] as $percent => $green_friday_product) {
	if(in_array($product->id, $green_friday_product)) {
		$discount = $percent;
		$is_green_friday = true;
	}
}

$is_new = $product->get_attribute('pa_novinka');
$is_cooling = $product->get_attribute('pa_ohlazhdyonka');
$is_freezing = $product->get_attribute('pa_zamorozka');

$price_tovar = $product->get_price();
$real_price = $product->get_regular_price();
if($price_tovar != $real_price) {
	$wrapper_classes[] = 'sale_class';
}

$price_date = get_field('pricedate', 'option');
$price_date = date("Y-m-d 23:59:59", strtotime($price_date));
	
$end_date = strtotime($price_date);

?>
<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" style="opacity: 0; transition: opacity .25s ease-in-out;">
	<?php
	if($price_tovar != $real_price) {
		echo '<div class="date-label">Действует до ' . date("d.m", $end_date) . '</div>';
		echo '<div class="sale-label">' . $discount . '%</div>';
	}
	if(!empty($is_cooling) || !empty($is_new) || !empty($is_freezing) || $is_green_friday) {
		echo '<div class="product-labels">';
		if(!empty($is_green_friday)) {
			echo '<span class="product-label green-friday-label">Зеленая пятница</span><br>';
		}
		if(!empty($is_new)) {
			echo '<span class="product-label new-label">Новинка</span><br>';
		}
		if(!empty($is_cooling)) {
			echo '<span class="product-label cooling-label">Охлажденка</span><br>';
		}
		if(!empty($is_freezing)) {
			echo '<span class="product-label freezing-label">Заморозка</span><br>';
		}
	
		echo '</div>';
	}
	?>
	<figure class="woocommerce-product-gallery__wrapper">
		<?php
		if ( $post_thumbnail_id ) {
			$html = wc_get_gallery_image_html( $post_thumbnail_id, true );
		} else {
			$html  = '<div class="woocommerce-product-gallery__image--placeholder">';
			$html .= sprintf( '<img src="%s" alt="%s" class="wp-post-image" />', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			$html .= '</div>';
		}

		echo apply_filters( 'woocommerce_single_product_image_thumbnail_html', $html, $post_thumbnail_id ); // phpcs:disable WordPress.XSS.EscapeOutput.OutputNotEscaped

		do_action( 'woocommerce_product_thumbnails' );
		?>
	</figure>
</div>

