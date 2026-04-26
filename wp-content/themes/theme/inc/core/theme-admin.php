<?php
/**
 * Theme admin and media integration helpers.
 *
 * @package Theme
 */

add_filter( 'site_transient_update_plugins', 'my_remove_update_nag' );
function my_remove_update_nag( $value ) {
	unset( $value->response['advanced-custom-fields-pro/acf.php'] );
	return $value;
}

function ferma_add_geojson_mime_type( $mimes ) {
	$mimes['geojson'] = 'application/json';
	$mimes['json']    = 'application/json';
	return $mimes;
}
add_filter( 'upload_mimes', 'ferma_add_geojson_mime_type' );

add_action( 'acf/init', 'ferma_acf_op_init' );
function ferma_acf_op_init() {
	if ( function_exists( 'acf_add_options_page' ) ) {
		acf_add_options_page(
			array(
				'page_title' => __( 'Доставка' ),
				'menu_title' => __( 'Доставка' ),
				'menu_slug'  => 'delivery-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
			)
		);

		acf_add_options_page(
			array(
				'page_title' => __( 'Выгрузка Купер' ),
				'menu_title' => __( 'Выгрузка Купер' ),
				'menu_slug'  => 'sberkuper-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
			)
		);

		acf_add_options_page(
			array(
				'page_title' => __( 'Комплекты' ),
				'menu_title' => __( 'Комплекты' ),
				'menu_slug'  => 'complect-settings',
				'capability' => 'edit_posts',
				'redirect'   => false,
			)
		);
	}
}
