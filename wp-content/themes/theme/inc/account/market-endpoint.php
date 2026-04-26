<?php
/**
 * WooCommerce account endpoint for nearest market selection.
 *
 * @package Theme
 */

add_filter( 'woocommerce_account_menu_items', 'truemisha_log_history_link', 25 );
function truemisha_log_history_link( $menu_links ) {
	$menu_links = array_slice( $menu_links, 0, 4, true )
		+ array( 'user-market' => 'Выбор ближайшего магазина' )
		+ array_slice( $menu_links, 4, null, true );
	return $menu_links;
}

add_action( 'init', 'truemisha_add_endpoint', 25 );
function truemisha_add_endpoint() {
	add_rewrite_endpoint( 'user-market', EP_PAGES );
}

add_action( 'woocommerce_account_user-market_endpoint', 'truemisha_content', 25 );
function truemisha_content() {
	echo 'В последний раз вы входили вчера через браузер Safari.';
}
