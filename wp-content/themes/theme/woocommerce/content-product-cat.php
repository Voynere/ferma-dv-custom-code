<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.7.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<?

$thumbnail_id = get_woocommerce_term_meta( $category->term_id, 'thumbnail_id', true );
$image = wp_get_attachment_url( $thumbnail_id );
?>
<li class="product-category product last pb-0" style="margin-right: 2.554%;">
	<a href="https://ferma-dv.ru/product-category/<?php echo $category->slug?>/<?
	$cur_user_id = get_current_user_id();
	$user_info = get_userdata($cur_user_id);
if($user_info->user_market == "ГринМаркет ТЦ Море" or $_COOKIE["market"] == "ГринМаркет ТЦ Море") {
		echo "?wms-addon-store-filter-form%5B0%5D=cab1caa9-da10-11eb-0a80-07410026c356";
	}
	if($user_info->user_market == "Жигура" or $_COOKIE["market"] == "Жигура") {
		echo "?wms-addon-store-filter-form%5B0%5D=8cc659e5-4bfb-11ec-0a80-075000080e54";
	}
	if($user_info->user_market == "Реми-Сити" or $_COOKIE["market"] == "Реми-Сити") {
		echo "?wms-addon-store-filter-form%5B0%5D=b24e4c35-9609-11eb-0a80-0d0d008550c2";
	}
	if($user_info->user_market == "Эгершельд" or $_COOKIE["market"] == "Эгершельд") {
		echo "?wms-addon-store-filter-form%5B0%5D=7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93";
	}
	if($user_info->user_market == "Космос" or $_COOKIE["market"] == "Космос") {
		echo "?wms-addon-store-filter-form%5B0%5D=a99d6fdf-0970-11ed-0a80-0ed600075845";
	}
	if($user_info->user_market == "Уссурийск" or $_COOKIE["vibor"] == "2") {
		echo "?wms-addon-store-filter-form%5B0%5D=9c9dfcc4-733f-11ec-0a80-0da1013a560d";
	}

	?>"><img src="<?php echo $image?>" alt=""><h2 class="woocommerce-loop-category__title">
			<?php echo $category->name?> <mark class="count">(36)</mark>		</h2>
		</a></li>