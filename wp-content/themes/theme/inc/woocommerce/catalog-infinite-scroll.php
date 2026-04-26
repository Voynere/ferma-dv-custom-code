<?php
/**
 * WooCommerce catalog infinite scroll and pagination URL helpers.
 *
 * @package Theme
 */

/**
 * GET-параметры, которые нужно сохранять в ссылках пагинации и при подгрузке каталога (WMS, сортировка и т.д.).
 *
 * @return array<string, string|array>
 */
function ferma_catalog_get_preserved_query_args() {
	$deny_keys = array(
		'paged',
		'page',
		'add-to-cart',
		'added-to-cart',
		'remove_item',
		'preview',
		'preview_id',
		'preview_nonce',
		'doing_wp_cron',
	);

	$add_args = array();
	foreach ( $_GET as $key => $value ) {
		$key_l = strtolower( (string) $key );
		if ( in_array( $key_l, $deny_keys, true ) ) {
			continue;
		}
		if ( is_array( $value ) ) {
			$add_args[ $key ] = array_map( 'sanitize_text_field', wp_unslash( $value ) );
		} else {
			$add_args[ $key ] = sanitize_text_field( wp_unslash( $value ) );
		}
	}

	return $add_args;
}

/**
 * URL страницы каталога с теми же фильтрами, что и у текущего запроса (для fetch и пагинации).
 *
 * @param int $page_num Номер страницы (1 = первая).
 */
function ferma_catalog_build_page_url( $page_num ) {
	$page_num = max( 1, (int) $page_num );
	$url      = get_pagenum_link( $page_num, false );
	$url      = remove_query_arg( array( 'add-to-cart', 'added-to-cart' ), $url );
	$add_args = ferma_catalog_get_preserved_query_args();
	if ( ! empty( $add_args ) ) {
		$url = add_query_arg( $add_args, $url );
	}
	return $url;
}

/**
 * Сохраняем GET-параметры (фильтр складов, сортировка, фильтры цен) в ссылках пагинации каталога.
 */
add_filter( 'woocommerce_pagination_args', 'ferma_catalog_pagination_preserve_query_args', 10, 1 );
function ferma_catalog_pagination_preserve_query_args( $args ) {
	if ( ! is_array( $args ) ) {
		return $args;
	}

	$add_args = ferma_catalog_get_preserved_query_args();
	if ( ! empty( $add_args ) ) {
		if ( isset( $args['add_args'] ) && is_array( $args['add_args'] ) ) {
			$args['add_args'] = array_merge( $args['add_args'], $add_args );
		} else {
			$args['add_args'] = $add_args;
		}
	}

	return $args;
}

/**
 * На первой странице витрины скрываем нумерованную пагинацию: товары догружаются по скроллу.
 * На 2+ странице пагинация остаётся (прямые ссылки, SEO).
 */
add_action( 'template_redirect', 'ferma_catalog_infinite_remove_pagination', 99 );
function ferma_catalog_infinite_remove_pagination() {
	if ( is_admin() || ! function_exists( 'is_shop' ) ) {
		return;
	}
	if ( ! apply_filters( 'ferma_catalog_infinite_scroll_enabled', true ) ) {
		return;
	}
	if (
		! ( function_exists( 'is_shop' ) && is_shop() )
		&& ! ( function_exists( 'is_product_category' ) && is_product_category() )
		&& ! ( function_exists( 'is_product_tag' ) && is_product_tag() )
	) {
		return;
	}
	$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	if ( $paged !== 1 ) {
		return;
	}
	global $wp_query;
	if ( empty( $wp_query->max_num_pages ) || (int) $wp_query->max_num_pages <= 1 ) {
		return;
	}
	$max_infinite = (int) apply_filters( 'ferma_catalog_infinite_max_pages', 300 );
	if ( (int) $wp_query->max_num_pages > $max_infinite ) {
		return;
	}
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination', 10 );
}

add_action( 'wp_enqueue_scripts', 'ferma_catalog_infinite_scroll_assets', 30 );
function ferma_catalog_infinite_scroll_assets() {
	if ( is_admin() || ! apply_filters( 'ferma_catalog_infinite_scroll_enabled', true ) ) {
		return;
	}
	if (
		! function_exists( 'is_shop' )
		|| (
			! ( function_exists( 'is_shop' ) && is_shop() )
			&& ! ( function_exists( 'is_product_category' ) && is_product_category() )
			&& ! ( function_exists( 'is_product_tag' ) && is_product_tag() )
		)
	) {
		return;
	}
	$paged = max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) );
	if ( $paged !== 1 ) {
		return;
	}
	global $wp_query;
	$max_pages = isset( $wp_query->max_num_pages ) ? (int) $wp_query->max_num_pages : 1;
	if ( $max_pages <= 1 ) {
		return;
	}
	$max_infinite = (int) apply_filters( 'ferma_catalog_infinite_max_pages', 300 );
	if ( $max_pages > $max_infinite ) {
		return;
	}
	$max_urls = (int) apply_filters( 'ferma_catalog_infinite_max_urls', 40 );
	if ( $max_urls < 1 ) {
		$max_urls = 1;
	}
	$last_page_for_urls = min( $max_pages, $max_urls + 1 );

	$theme_ver = wp_get_theme()->get( 'Version' );
	if ( ! $theme_ver ) {
		$theme_ver = '1.0';
	}

	wp_enqueue_script(
		'ferma-catalog-infinite',
		get_template_directory_uri() . '/assets/js/catalog-infinite-scroll.js',
		array(),
		$theme_ver,
		true
	);

	$page_urls = array();
	for ( $p = 2; $p <= $last_page_for_urls; $p++ ) {
		$page_urls[] = ferma_catalog_build_page_url( $p );
	}

	wp_localize_script(
		'ferma-catalog-infinite',
		'fermaCatalogInfinite',
		array(
			'pageUrls'    => $page_urls,
			'totalPages'  => count( $page_urls ) + 1,
			'i18nLoading' => __( 'Загрузка товаров…', 'theme' ),
			'i18nDone'    => __( 'Все товары загружены', 'theme' ),
			'i18nError'   => __( 'Не удалось загрузить. Обновите страницу.', 'theme' ),
		)
	);
}

add_action( 'wp_footer', 'ferma_catalog_infinite_scroll_inline_styles', 20 );
function ferma_catalog_infinite_scroll_inline_styles() {
	if ( ! wp_script_is( 'ferma-catalog-infinite', 'enqueued' ) ) {
		return;
	}
	echo '<style>
		.ferma-catalog-infinite-spinner{display:inline-block;width:22px;height:22px;border:2px solid #e0e0e0;border-top-color:#6ba802;border-radius:50%;animation:ferma-inf-spin .65s linear infinite;vertical-align:middle;margin-right:8px;}
		@keyframes ferma-inf-spin{to{transform:rotate(360deg)}}
	</style>';
}
