<?php
/**
 * Single Product Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;
$youtube = $product->get_attribute( 'pa_ssylka-na-youtube' );
if(empty($youtube)) {
	$youtube = $product->get_attribute( 'pa_video' );
}
?>
<!-- Вывод заголовка и артикула карточки товара в wc-functions.php -->
<!-- <h1 class="prod_tit"><?php // the_title(); ?></h1> -->
<!-- <span class="sku_wrapper" ><?php // esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php // echo ( $sku = $product->get_sku() ) ? $sku : esc_html__( 'N/A', 'woocommerce' ); ?></span></span> -->

<?php if(!empty($youtube)) : ?>
<a href="<?php echo $youtube; ?>" class="yt__link" target="_blank">Видео о товаре</a>
<?php endif; ?>

<!-- <p class="<?php // echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>" style="font-weight:700; color:black;"><?php // echo $product->get_price_html(); ?></p> -->
