<?php
/**
 * Theme bootstrap helpers for optional module loading.
 *
 * @package Theme
 */

if ( ! function_exists( 'ferma_require_if_exists' ) ) {
	/**
	 * Require module file from theme directory if it exists.
	 *
	 * @param string $relative_path Relative path from theme root.
	 */
	function ferma_require_if_exists( $relative_path ) {
		$file = trailingslashit( get_template_directory() ) . ltrim( (string) $relative_path, '/' );
		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
}

if ( ! function_exists( 'ferma_require_module_list' ) ) {
	/**
	 * Require a list of optional modules in provided order.
	 *
	 * @param array $modules Relative paths from theme root.
	 */
	function ferma_require_module_list( $modules ) {
		foreach ( (array) $modules as $module ) {
			ferma_require_if_exists( $module );
		}
	}
}

if ( ! function_exists( 'ferma_load_custom_modules' ) ) {
	/**
	 * Load custom business modules kept under theme includes/.
	 */
	function ferma_load_custom_modules() {
		$custom_modules = array(
			'includes/sort/ferma_sort_products_by_balance.php',
			'includes/delivery/ferma_delivery_price.php',
			'includes/delivery/order_show_delivery_price.php',
			'includes/promocode/ferma_promocode.php',
			'includes/emails/ferma_client_last_login.php',
			'includes/add2cart/ferma_validate_add_cart_item.php',
			'includes/unisender/ferma_save_client_unisender.php',
			'includes/buyoneclick/ferma_buyoneclick.php',
			'moysklad.php',
			'includes/shortcode/ferma_shortcodes.php',
			'includes/complect/ferma_complect.php',
		);

		ferma_require_module_list( $custom_modules );
	}
}

if ( ! function_exists( 'ferma_load_core_modules' ) ) {
	/**
	 * Load early core modules used at theme bootstrap.
	 */
	function ferma_load_core_modules() {
		$core_modules = array(
			'inc/core/theme-admin.php',
			'inc/core/theme-setup.php',
			'inc/core/pingback-headers.php',
			'inc/core/content-helpers.php',
			'inc/core/admin-options-pages.php',
			'inc/frontend/assets.php',
			'inc/frontend/product-cart-ui.php',
			'inc/cache/catalog-cache.php',
			'inc/auth/phone-account.php',
			'inc/account/market-endpoint.php',
			'inc/shortcodes/banner.php',
			'inc/integration/moysklad-attributes.php',
			'inc/integration/konfety-razbivka.php',
			'inc/cart/display-pricing.php',
			'inc/cart/fragments.php',
			'inc/catalog/weight-helpers.php',
			'inc/catalog/weight-formatting.php',
			'inc/catalog/cart-pricing.php',
			'inc/catalog/price-display.php',
			'inc/catalog/nutrition-display.php',
			'inc/catalog/product-attributes-summary.php',
			'inc/catalog/stock-visibility.php',
			'inc/catalog/category-redirect.php',
			'inc/catalog/category-query.php',
			'inc/discount/runtime-context.php',
			'inc/woocommerce/catalog-query-limits.php',
			'inc/woocommerce/catalog-infinite-scroll.php',
			'inc/woocommerce/email-controls.php',
			'inc/checkout/delivery-addressing.php',
			'inc/checkout/checkout-fields.php',
			'inc/checkout/green-prices.php',
			'inc/checkout/validation.php',
			'inc/checkout/delivery-session.php',
			'inc/bonus/checkout-bonus-state.php',
			'inc/bonus/kilbil-bonuses.php',
			'inc/promocode/post-type.php',
			'inc/promocode/meta-boxes.php',
			'inc/promocode/save-meta.php',
			'inc/promocode/session-api.php',
			'inc/promocode/cart-actions.php',
			'inc/promocode/gift-pricing.php',
			'inc/promocode/apply-flow.php',
			'inc/promocode/core.php',
			'inc/promocode/usage-limits.php',
			'inc/promocode/assets.php',
			'inc/promocode/ajax-errors.php',
			'inc/promocode/order-meta.php',
			'inc/promocode/cart-session-sync.php',
			'inc/promocode/cookie-sync.php',
			'inc/promocode/admin-columns.php',
		);

		ferma_require_module_list( $core_modules );
	}
}

if ( ! function_exists( 'ferma_load_theme_compat_modules' ) ) {
	/**
	 * Load theme compatibility and WooCommerce integration modules.
	 */
	function ferma_load_theme_compat_modules() {
		$compat_modules = array(
			'wc-functions.php',
			'inc/custom-header.php',
			'inc/template-tags.php',
			'inc/template-functions.php',
			'inc/customizer.php',
		);

		ferma_require_module_list( $compat_modules );

		if ( defined( 'JETPACK__VERSION' ) ) {
			ferma_require_if_exists( 'inc/jetpack.php' );
		}
	}
}
