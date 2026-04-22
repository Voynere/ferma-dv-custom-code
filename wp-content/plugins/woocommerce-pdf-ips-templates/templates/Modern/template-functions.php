<?php
/**
 * Use this file for all your template filters and actions.
 * Requires WooCommerce PDF Invoices & Packing Slips 1.4.13 or higher
 */
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

add_filter( 'wpo_wcpdf_template_editor_defaults', 'wpo_wcpdf_modern_template_defaults', 9, 3 );
add_filter( 'wpo_wcpdf_template_editor_settings', 'wpo_wcpdf_modern_template_defaults', 9, 3 );
function wpo_wcpdf_modern_template_defaults ( $settings, $template_type, $settings_name ) {
	$editor_settings = get_option('wpo_wcpdf_editor_settings');

	if (isset($editor_settings['settings_saved']) && !isset($_GET['load-defaults'])) {
		return $settings;
	}

	// only packing slip is different
	if ( $template_type == 'packing-slip' ) {
		switch ($settings_name) {
			case 'columns':
				$settings = array (
					1 => array (
						'type'			=> 'thumbnail',
					),
					2 => array (
						'type'			=> 'sku',
					),
					3 => array (
						'type'			=> 'description',
						'show_meta'		=> 1,
					),
					4 => array (
						'type'			=> 'quantity',
					),
				);
				break;
			case 'totals':
				$settings = array();
				break;
		}
	} else {
		switch ($settings_name) {
			case 'columns':
				$settings = array (
					1 => array (
						'type'			=> 'thumbnail',
					),
					2 => array (
						'type'			=> 'sku',
					),
					3 => array (
						'type'			=> 'description',
						'show_meta'		=> 1,
					),
					4 => array (
						'type'			=> 'quantity',
					),
					5 => array (
						'type'			=> 'price',
						'price_type'	=> 'single',
						'tax'			=> 'incl',
						'discount'		=> 'before',
					),
					6 => array (
						'type'			=> 'price',
						'price_type'	=> 'total',
						'tax'			=> 'incl',
						'discount'		=> 'before',
					),
				);
				break;
			case 'totals':
				$settings = array(
					1 => array (
						'type'			=> 'subtotal',
						'tax'			=> 'incl',
						'discount'		=> 'before',
					),
					2 => array (
						'type'			=> 'discount',
						'tax'			=> 'incl',
					),
					3 => array (
						'type'			=> 'shipping',
						'tax'			=> 'incl',
					),
					4 => array (
						'type'			=> 'fees',
						'tax'			=> 'incl',
					),
					5 => array (
						'type'			=> 'total',
						'tax'			=> 'incl',
					),
					6 => array (
						'type'			=> 'total',
						'tax'			=> 'excl',
					),					
					7 => array (
						'type'			=> 'vat',
					),
				);
				break;
		}
	}

	return $settings;
}
