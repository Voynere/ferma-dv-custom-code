<?php
/**
 * Frontend asset enqueue logic.
 *
 * @package Theme
 */

/**
 * Enqueue scripts and styles.
 */
function theme_scripts() {
	// Base legacy stylesheet is disabled globally to keep most templates
	// on the same visual baseline as homepage/product pages.
	// For single content pages it will be enqueued later (after new-style) so header rules are not overridden.

	wp_enqueue_style( 'complect-style', get_template_directory_uri() . '/css/complect.css', '', '1.0' );

	// Use WordPress core jQuery to preserve WooCommerce checkout dependencies (blockUI, wc-checkout).
	wp_enqueue_script( 'jquery' );

	wp_enqueue_script( 'datepicker', get_template_directory_uri() . '/js/datepicker.js', array(), '1.0', true );

	$style_path = get_template_directory() . '/assets/css/style.min.css';
	$style_uri  = get_template_directory_uri() . '/assets/css/style.min.css';
	$version    = file_exists( $style_path ) ? filemtime( $style_path ) : null;
	wp_enqueue_style( 'new-style', $style_uri, array(), $version );
	if ( is_single() && ! is_product() ) {
		$legacy_style_path = get_stylesheet_directory() . '/style.css';
		$legacy_style_ver  = file_exists( $legacy_style_path ) ? filemtime( $legacy_style_path ) : null;
		// Load after new-style: single blog/recipes/promotions need legacy header rules to win.
		wp_enqueue_style( 'theme-style-single', get_stylesheet_uri(), array( 'new-style' ), $legacy_style_ver );
	}
	// Ensure side cart stays above sticky follow-header while scrolling.
	wp_add_inline_style( 'new-style', '.cart{z-index:10020 !important;}' );

	if ( function_exists( 'is_checkout' ) && is_checkout() && function_exists( 'is_order_received_page' ) && ! is_order_received_page() ) {
		wp_add_inline_style(
			'new-style',
			'.ferma-checkout-submit-anchor{position:relative;display:block;width:100%;}' .
			'.ferma-checkout-inline-notices{visibility:hidden;opacity:0;pointer-events:none;position:absolute;left:0;right:0;top:100%;margin-top:10px;padding:14px 40px 14px 16px;border-radius:12px;border:1px solid #e74c3c;background:#fff6f6;color:#1a1a1a;font-size:15px;line-height:1.45;box-shadow:0 6px 24px rgba(0,0,0,.12);z-index:5;max-height:min(40vh,280px);overflow:auto;}' .
			'.ferma-checkout-inline-notices.is-visible{visibility:visible;opacity:1;pointer-events:auto;z-index:10050;}' .
			'.ferma-checkout-inline-notices__close{position:absolute;top:6px;right:6px;width:36px;height:36px;margin:0;padding:0;border:0;background:transparent;color:#333;font-size:26px;line-height:1;cursor:pointer;border-radius:8px;}' .
			'.ferma-checkout-inline-notices__close:hover{background:rgba(0,0,0,.06);}' .
			'.ferma-checkout-inline-notices__body:empty{display:none;}' .
			'.ferma-checkout-inline-notices ul{margin:0.35em 0 0;padding-left:1.2em;}' .
			'.ferma-checkout-min-order p{margin:0 0 10px;}' .
			'.ferma-checkout-min-order__actions{display:flex;gap:8px;flex-wrap:wrap;}' .
			'.ferma-checkout-min-order__link,.ferma-checkout-min-order__stay{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:8px 12px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;cursor:pointer;}' .
			'.ferma-checkout-min-order__link{background:#4fbd01;color:#fff;border:1px solid transparent;}' .
			'.ferma-checkout-min-order__stay{background:#fff;color:#333;border:1px solid rgba(21,21,21,.25);}' .
			'form.checkout .form-row{position:relative;margin-bottom:6px;}' .
			'form.checkout .form-row>label{position:absolute;top:-9px;left:16px;z-index:3;display:inline-block;font-size:12px;line-height:1.2;font-weight:400;color:#5f5f5f;margin:0;padding:0 6px;background:#fff;}' .
			'form.checkout .form-row label.checkbox{position:static;background:transparent;padding:0;font-weight:600;}' .
			'form.checkout .woocommerce-input-wrapper input,form.checkout .woocommerce-input-wrapper select,form.checkout .woocommerce-input-wrapper textarea{position:relative;z-index:1;min-height:40px;padding:9px 12px;font-size:14px;line-height:1.25;}' .
			'form.checkout .woocommerce-input-wrapper textarea{min-height:72px;}' .
			'form.checkout .form-row.ferma-inline-label>label{position:absolute!important;top:-9px!important;left:16px!important;display:inline-block!important;padding:0 6px!important;margin:0!important;background:#fff!important;border:0!important;font-weight:400!important;color:#5f5f5f!important;z-index:3!important;}' .
			'form.checkout #billing_asdx1_field{position:relative;z-index:1;}' .
			'form.checkout #billing_asdx1_field .ferma-delivery-time-error{position:static !important;clear:both;margin:6px 0 14px !important;padding:10px 12px;line-height:1.45;font-size:14px;color:#b32d2e !important;border:1px solid #f1aeb5;border-radius:8px;background:#fef7f7;}' .
			'form.checkout .ferma-delivery-address-field{position:relative;}' .
			'form.checkout .ferma-delivery-address-field .ferma-delivery-address-edit{position:absolute;right:10px;top:50%;transform:translateY(-50%);z-index:4;display:inline-flex;align-items:center;justify-content:center;height:30px;padding:0 12px;border-radius:999px;background:#f3f8ed;border:1px solid #9ee87f;color:#4e4e4e;text-decoration:none;font-size:12px;line-height:1;font-weight:500;}' .
			'form.checkout .ferma-delivery-address-field .ferma-delivery-address-edit:hover{background:#eaf6df;color:#3f3f3f;}' .
			'form.checkout .ferma-delivery-address-field .input-text{padding-right:145px !important;}' .
			'.ferma-stock-modal-overlay{position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:100000;display:flex;align-items:center;justify-content:center;padding:16px;}' .
			'.ferma-stock-modal{background:#fff;border-radius:16px;max-width:440px;width:100%;padding:24px;box-shadow:0 8px 32px rgba(0,0,0,.15);}' .
			'.ferma-stock-modal h3{margin:0 0 12px;font-size:18px;}' .
			'.ferma-stock-modal p{margin:0 0 16px;font-size:15px;line-height:1.45;}' .
			'.ferma-stock-modal-actions{display:flex;flex-wrap:wrap;gap:10px;justify-content:flex-end;}' .
			'.ferma-stock-modal-actions button{border-radius:12px;padding:12px 18px;font-weight:600;cursor:pointer;border:1px solid rgba(21,21,21,.25);background:#f5f5f5;}' .
			'.ferma-stock-modal-actions .ferma-stock-confirm{background:#4fbd01;color:#fff;border-color:transparent;}'
		);
	}

	wp_enqueue_script( 'slick', get_template_directory_uri() . '/js/slick.min.js', array(), '1.0', true );

	wp_enqueue_script( 'buyoneclick', get_template_directory_uri() . '/js/buyoneclick.js', array(), '1.1', true );

	wp_enqueue_script( 'complect', get_template_directory_uri() . '/js/complect.js', array(), '1.1', true );
	wp_localize_script( 'complect', 'complect', array( 'ajax_url' => admin_url( 'admin-ajax.php' ) ) );

	wp_enqueue_script( 'theme-navigation', get_template_directory_uri() . '/js/navigation.js', array(), '20151215', true );

	wp_enqueue_script( 'theme-skip-link-focus-fix', get_template_directory_uri() . '/js/skip-link-focus-fix.js', array(), '20151215', true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
	if ( function_exists( 'is_checkout' ) && is_checkout() && function_exists( 'is_order_received_page' ) && ! is_order_received_page() ) {
		wp_enqueue_script(
			'custom-checkout-js',
			get_stylesheet_directory_uri() . '/assets/js/checkout.js',
			array( 'jquery', 'wc-checkout' ),
			'3.1',
			true
		);
		wp_localize_script(
			'custom-checkout-js',
			'fermaCheckout',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'ferma_checkout_stock' ),
				'shopUrl' => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
			)
		);
	}

	wp_enqueue_script(
		'mini-cart-qty',
		get_template_directory_uri() . '/assets/js/mini-cart-qty.js',
		array( 'jquery', 'wc-cart-fragments' ),
		'1.8',
		true
	);
	$catalog_qty_path = get_template_directory() . '/assets/js/catalog-qty23.js';
	$catalog_qty_ver  = file_exists( $catalog_qty_path ) ? filemtime( $catalog_qty_path ) : null;
	wp_enqueue_script(
		'catalog-qty-js',
		get_template_directory_uri() . '/assets/js/catalog-qty23.js',
		array( 'jquery' ),
		$catalog_qty_ver,
		true
	);
	wp_localize_script(
		'mini-cart-qty',
		'CartQtyData',
		array(
			'ajax_url' => admin_url( 'admin-ajax.php' ),
			'nonce'    => wp_create_nonce( 'update_cart_qty' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'theme_scripts' );
