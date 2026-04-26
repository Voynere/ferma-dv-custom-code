<?php
/**
 * ACF admin options pages registration.
 *
 * @package Theme
 */

if ( function_exists( 'acf_add_options_page' ) ) {
	acf_add_options_page(
		array(
			'page_title' => 'Доп. настройки',
			'menu_title' => 'Доп. настройки',
			'menu_slug'  => 'theme-general-settings',
			'capability' => 'edit_posts',
			'redirect'   => false,
		)
	);

	acf_add_options_page(
		array(
			'page_title' => 'Уведомления',
			'menu_title' => 'Уведомления',
			'menu_slug'  => 'notice-settings',
			'capability' => 'edit_posts',
			'redirect'   => false,
		)
	);
}
