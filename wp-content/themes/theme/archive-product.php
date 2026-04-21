<?php
/**
 * Архив типа «Товар» (главный магазин): делегируем общему шаблону woocommerce/archive-product.php.
 *
 * @package WooCommerce\Templates
 */

defined( 'ABSPATH' ) || exit;

$ferma_ap = locate_template( array( 'woocommerce/archive-product.php' ), false, false );
if ( $ferma_ap ) {
	require $ferma_ap;
}
