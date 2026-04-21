<?php
/**
 * Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Theme
 */
add_action('init', 'force_no_cache_for_checkout');

function force_no_cache_for_checkout() {
    if (is_checkout()) {
        // Заголовки для отключения кэширования
        header('Cache-Control: no-cache, no-store, must-revalidate');
        header('Pragma: no-cache');
        header('Expires: 0');
        header('X-Accel-Expires: 0');

        // Для Nginx FastCGI
        header('X-Accel-Buffering: no');

        // Для Cloudflare
        header('CF-Cache-Status: BYPASS');

        // Удаляем все заголовки кэширования
        if (function_exists('header_remove')) {
            $headers = headers_list();
            foreach ($headers as $header) {
                if (stripos($header, 'cache') !== false ||
                    stripos($header, 'expires') !== false) {
                    header_remove($header);
                }
            }
        }
    }
}

// Добавляем уникальный параметр к URL чекаута
add_filter('woocommerce_get_checkout_url', 'add_nocache_to_checkout_url');

function add_nocache_to_checkout_url($url) {
    // Добавляем временную метку если уже есть параметры
    if (strpos($url, '?') !== false) {
        $url .= '&nocache=' . time();
    } else {
        $url .= '?nocache=' . time();
    }
    return $url;
}

/**
 * Витрина: магазин, категории и метки (без корзины/чекаута/ЛК).
 */
if ( ! function_exists( 'ferma_is_catalog_cache_candidate' ) ) {
	function ferma_is_catalog_cache_candidate() {
		if ( ! function_exists( 'is_shop' ) ) {
			return false;
		}
		if ( is_cart() || is_checkout() || is_account_page() ) {
			return false;
		}
		return is_shop() || is_product_category() || is_product_tag();
	}
}

/**
 * Анонимный GET без WC action-параметров — единый SSR + публичные Cache-Control для CDN/прокси.
 */
if ( ! function_exists( 'ferma_is_public_catalog_cache_request' ) ) {
	function ferma_is_public_catalog_cache_request() {
		if ( ! ferma_is_catalog_cache_candidate() ) {
			return false;
		}
		if ( is_user_logged_in() ) {
			return false;
		}
		if ( isset( $_GET['add-to-cart'] ) || isset( $_GET['added-to-cart'] ) || isset( $_GET['remove_item'] ) ) {
			return false;
		}
		if ( is_preview() || ( function_exists( 'is_customize_preview' ) && is_customize_preview() ) ) {
			return false;
		}
		if ( wp_doing_ajax() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) ) {
			return false;
		}
		if ( isset( $_SERVER['REQUEST_METHOD'] ) && 'GET' !== $_SERVER['REQUEST_METHOD'] ) {
			return false;
		}
		return true;
	}
}

add_filter( 'wp_get_nocache_headers', 'ferma_strip_nocache_headers_for_public_catalog', 99, 1 );
function ferma_strip_nocache_headers_for_public_catalog( $headers ) {
	if ( ferma_is_public_catalog_cache_request() ) {
		return array();
	}
	return $headers;
}

add_filter( 'woocommerce_enable_nocache_headers', 'ferma_wc_disable_nocache_headers_public_catalog', 99 );
function ferma_wc_disable_nocache_headers_public_catalog( $enabled ) {
	if ( ferma_is_public_catalog_cache_request() ) {
		return false;
	}
	return $enabled;
}

add_action( 'send_headers', 'ferma_send_public_cache_headers_catalog', 999 );
function ferma_send_public_cache_headers_catalog() {
	if ( ! ferma_is_public_catalog_cache_request() || headers_sent() ) {
		return;
	}
	header( 'Cache-Control: public, max-age=120, s-maxage=600, stale-while-revalidate=60', true );
}

/**
 * ID рубрики «Фермерский блог» для выборок в шаблонах.
 * Раньше везде использовался slug `fermerskij-blog`; при смене ярлыка в админке WP_Query по category_name переставал находить посты.
 * В category.php для той же рубрики задано cat=200 — используем как запасной вариант.
 *
 * Фильтры: `ferma_farmer_blog_category_slug`, `ferma_farmer_blog_category_id_fallback`.
 */
function ferma_get_farmer_blog_category_id() {
	$slug = apply_filters( 'ferma_farmer_blog_category_slug', 'fermerskij-blog' );
	if ( is_string( $slug ) && $slug !== '' ) {
		$term = get_term_by( 'slug', $slug, 'category' );
		if ( $term && ! is_wp_error( $term ) ) {
			return (int) $term->term_id;
		}
	}
	return (int) apply_filters( 'ferma_farmer_blog_category_id_fallback', 200 );
}

/**
 * Одноразовый подписанный токен для сценария «смена телефона → snemanomera cookie → повторная установка сессии».
 * Заменяет небезопасную передачу сырого user ID в cookie (подделка ID).
 */
function ferma_snemanomera_handoff_secret_key() {
	return apply_filters(
		'ferma_snemanomera_handoff_secret_key',
		( function_exists( 'wp_salt' ) ? wp_salt( 'secure_auth' ) : '' ) . 'ferma_snemanomera_v1'
	);
}

/**
 * @param int $user_id ID пользователя, уже прошедшего проверку на сервере.
 * @return string Токен (base64url) или пустая строка.
 */
function ferma_snemanomera_handoff_issue( $user_id ) {
	$user_id = (int) $user_id;
	if ( $user_id < 1 ) {
		return '';
	}
	$expiry = time() + 120;
	$data   = $user_id . '|' . $expiry;
	$sig    = hash_hmac( 'sha256', $data, ferma_snemanomera_handoff_secret_key() );
	$payload = $data . '|' . $sig;
	return rtrim( strtr( base64_encode( $payload ), '+/', '-_' ), '=' );
}

/**
 * @param string $token Значение из cookie (возможно url-encoded).
 * @return int user_id или 0.
 */
function ferma_snemanomera_handoff_validate( $token ) {
	if ( ! is_string( $token ) || $token === '' ) {
		return 0;
	}
	$token = rawurldecode( $token );
	$b64   = strtr( $token, '-_', '+/' );
	$pad   = strlen( $b64 ) % 4;
	if ( $pad ) {
		$b64 .= str_repeat( '=', 4 - $pad );
	}
	$decoded = base64_decode( $b64, true );
	if ( ! $decoded || substr_count( $decoded, '|' ) !== 2 ) {
		return 0;
	}
	$parts = explode( '|', $decoded, 3 );
	if ( count( $parts ) !== 3 ) {
		return 0;
	}
	list( $uid, $expiry, $sig ) = $parts;
	$uid    = (int) $uid;
	$expiry = (int) $expiry;
	if ( $uid < 1 || $expiry < time() ) {
		return 0;
	}
	$data     = $uid . '|' . $expiry;
	$expected = hash_hmac( 'sha256', $data, ferma_snemanomera_handoff_secret_key() );
	if ( ! hash_equals( $expected, $sig ) ) {
		return 0;
	}
	// Одноразовое использование (защита от повторной подстановки cookie).
	$use_key = 'ferma_snem_' . md5( $sig );
	if ( get_transient( $use_key ) ) {
		return 0;
	}
	set_transient( $use_key, 1, 5 * MINUTE_IN_SECONDS );
	return $uid;
}

/**
 * Каталог: серверная пагинация (основной WC-запрос).
 * Снижает TTFB и размер HTML на «тяжёлых» категориях; совместимо с фильтром складов WMS и сортировкой.
 *
 * Число товаров на страницу: фильтр `ferma_catalog_products_per_page` (по умолчанию 12) или стандартный `loop_shop_per_page`.
 * На первой странице каталога при включённом infinite scroll следующие «страницы» подгружаются по скроллу (см. ferma_catalog_infinite_scroll_assets).
 */
function ferma_catalog_products_per_page_default() {
	return (int) apply_filters( 'ferma_catalog_products_per_page', 12 );
}

add_filter( 'loop_shop_per_page', 'ferma_loop_shop_per_page_catalog', 20, 1 );
function ferma_loop_shop_per_page_catalog( $per_page ) {
	if ( ! function_exists( 'WC' ) ) {
		return $per_page;
	}
	$default = ferma_catalog_products_per_page_default();
	if ( $default < 1 ) {
		return $per_page;
	}
	/**
	 * Размер порции в каталоге (пагинация + infinite scroll). По умолчанию 12.
	 * Второй аргумент — значение из WooCommerce (кастомайзер), если нужна особая логика.
	 */
	return (int) apply_filters( 'ferma_catalog_loop_posts_per_page', $default, $per_page );
}

/**
 * Лимит товаров на странице витрины: применяется и в WC (woocommerce_product_query), и в самом конце pre_get_posts.
 */
function ferma_catalog_apply_posts_per_page_limit( $q ) {
	$default = ferma_catalog_products_per_page_default();
	$per     = (int) apply_filters( 'ferma_catalog_loop_posts_per_page', $default, (int) $q->get( 'posts_per_page' ) );
	if ( $per < 1 ) {
		return;
	}
	// Иначе WP может игнорировать posts_per_page.
	$q->set( 'nopaging', false );
	$q->set( 'posts_per_page', $per );
}

/**
 * Основной хук WooCommerce для списка товаров — срабатывает надёжнее, чем один только pre_get_posts.
 * PHP_INT_MAX: после плагинов (WMS и др.), чтобы не перезаписали posts_per_page после нас.
 */
add_action( 'woocommerce_product_query', 'ferma_catalog_woocommerce_product_query_limit', PHP_INT_MAX, 2 );
function ferma_catalog_woocommerce_product_query_limit( $q, $wc_query_instance = null ) {
	if ( is_admin() || ! apply_filters( 'ferma_catalog_force_posts_per_page_enabled', true ) ) {
		return;
	}
	if ( ! $q instanceof WP_Query || ! $q->is_main_query() ) {
		return;
	}
	ferma_catalog_apply_posts_per_page_limit( $q );
}

/**
 * Резерв: самый поздний pre_get_posts на главном запросе (плагины с большим приоритетом).
 */
add_action( 'pre_get_posts', 'ferma_catalog_force_main_query_posts_per_page', 999999 );
function ferma_catalog_force_main_query_posts_per_page( $q ) {
	if ( is_admin() || ! $q instanceof WP_Query || ! $q->is_main_query() ) {
		return;
	}
	if ( ! apply_filters( 'ferma_catalog_force_posts_per_page_enabled', true ) ) {
		return;
	}
	if ( ! function_exists( 'WC' ) ) {
		return;
	}
	if ( ! ferma_catalog_is_main_product_listing_query( $q ) ) {
		return;
	}
	ferma_catalog_apply_posts_per_page_limit( $q );
}

/**
 * Самый поздний pre_get_posts: на части окружений другие плагины перезаписывают posts_per_page после приоритета 999999.
 */
add_action( 'pre_get_posts', 'ferma_catalog_force_main_query_posts_per_page_last', PHP_INT_MAX );
function ferma_catalog_force_main_query_posts_per_page_last( $q ) {
	ferma_catalog_force_main_query_posts_per_page( $q );
}

/**
 * Главный запрос списка товаров: магазин, категории/метки/атрибуты WooCommerce.
 *
 * @param WP_Query $q Query object.
 */
function ferma_catalog_is_main_product_listing_query( $q ) {
	if ( ! $q instanceof WP_Query ) {
		return false;
	}
	if ( 'product_query' === $q->get( 'wc_query' ) ) {
		return true;
	}

	$taxonomies = get_object_taxonomies( 'product', 'names' );
	if ( empty( $taxonomies ) ) {
		return false;
	}

	// Сначала таксономии: на части запросов post_type ещё не «product», а архив категории уже определён.
	if ( $q->is_tax( $taxonomies ) ) {
		return true;
	}
	$qv_tax = $q->get( 'taxonomy' );
	if ( $qv_tax && taxonomy_exists( $qv_tax ) && in_array( $qv_tax, $taxonomies, true ) ) {
		return true;
	}

	$pt = $q->get( 'post_type' );
	if ( $pt !== 'product' && ! ( is_array( $pt ) && in_array( 'product', $pt, true ) ) ) {
		return false;
	}
	if ( $q->is_post_type_archive( 'product' ) ) {
		return true;
	}

	return false;
}

/**
 * Последний рубеж: подставляем LIMIT в SQL, если другие хуки/плагины оставили выдачу «всех» постов.
 * (Иначе при posts_per_page=-1 или nopaging фрагмент LIMIT в SQL пустой.)
 */
add_filter( 'post_limits', 'ferma_catalog_post_limits_sql', PHP_INT_MAX - 10, 2 );
function ferma_catalog_post_limits_sql( $limits, $query ) {
	if ( is_admin() || ! apply_filters( 'ferma_catalog_force_posts_per_page_enabled', true ) ) {
		return $limits;
	}
	if ( ! $query instanceof WP_Query || ! $query->is_main_query() ) {
		return $limits;
	}
	if ( ! function_exists( 'WC' ) ) {
		return $limits;
	}
	if ( ! ferma_catalog_is_main_product_listing_query( $query ) ) {
		return $limits;
	}

	$default = ferma_catalog_products_per_page_default();
	$per     = (int) apply_filters( 'ferma_catalog_loop_posts_per_page', $default, (int) $query->get( 'posts_per_page' ) );
	if ( $per < 1 ) {
		return $limits;
	}

	$paged  = max( 1, (int) $query->get( 'paged' ), (int) $query->get( 'page' ) );
	$offset = ( $paged - 1 ) * $per;

	return sprintf( 'LIMIT %d, %d', $offset, $per );
}

/**
 * Если LIMIT в SQL всё равно не сработал — обрезаем массив постов после выборки (страховка для продакшена).
 */
add_filter( 'posts_results', 'ferma_catalog_posts_results_cap', PHP_INT_MAX - 9, 2 );
function ferma_catalog_posts_results_cap( $posts, $query ) {
	if ( is_admin() || ! $query instanceof WP_Query || ! $query->is_main_query() ) {
		return $posts;
	}
	if ( ! apply_filters( 'ferma_catalog_force_posts_per_page_enabled', true ) || ! function_exists( 'WC' ) ) {
		return $posts;
	}
	if ( ! ferma_catalog_is_main_product_listing_query( $query ) ) {
		return $posts;
	}
	$per = (int) apply_filters( 'ferma_catalog_loop_posts_per_page', ferma_catalog_products_per_page_default(), (int) $query->get( 'posts_per_page' ) );
	if ( $per < 1 ) {
		return $posts;
	}
	$n = count( $posts );
	if ( $n <= $per ) {
		return $posts;
	}
	$paged  = max( 1, (int) $query->get( 'paged' ), (int) $query->get( 'page' ) );
	$offset = ( $paged - 1 ) * $per;
	return array_slice( $posts, $offset, $per );
}

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
	if ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
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
	if ( ! function_exists( 'is_shop' ) || ( ! is_shop() && ! is_product_category() && ! is_product_tag() ) ) {
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
	for ( $p = 2; $p <= $max_pages; $p++ ) {
		$page_urls[] = ferma_catalog_build_page_url( $p );
	}

	wp_localize_script(
		'ferma-catalog-infinite',
		'fermaCatalogInfinite',
		array(
			'pageUrls'    => $page_urls,
			'totalPages'  => $max_pages,
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

// Include ferma functions
if(file_exists(get_template_directory() . "/includes/sort/ferma_sort_products_by_balance.php")) {
	require_once(get_template_directory() . "/includes/sort/ferma_sort_products_by_balance.php");
}
if(file_exists(get_template_directory() . "/includes/delivery/ferma_delivery_price.php")) {
	require_once(get_template_directory() . "/includes/delivery/ferma_delivery_price.php");
}
if(file_exists(get_template_directory() . "/includes/delivery/order_show_delivery_price.php")) {
	require_once(get_template_directory() . "/includes/delivery/order_show_delivery_price.php");
}
if(file_exists(get_template_directory() . "/includes/promocode/ferma_promocode.php")) {
	require_once(get_template_directory() . "/includes/promocode/ferma_promocode.php");
}

if(file_exists(get_template_directory() . "/includes/emails/ferma_client_last_login.php")) {
	require_once(get_template_directory() . "/includes/emails/ferma_client_last_login.php");
}

if(file_exists(get_template_directory() . "/includes/add2cart/ferma_validate_add_cart_item.php")) {
	require_once(get_template_directory() . "/includes/add2cart/ferma_validate_add_cart_item.php");
}

if(file_exists(get_template_directory() . "/includes/unisender/ferma_save_client_unisender.php")) {
	require_once(get_template_directory() . "/includes/unisender/ferma_save_client_unisender.php");
}

if(file_exists(get_template_directory() . "/includes/buyoneclick/ferma_buyoneclick.php")) {
	require_once(get_template_directory() . "/includes/buyoneclick/ferma_buyoneclick.php");
}

if(file_exists(get_template_directory() . "/moysklad.php")) {
	require_once(get_template_directory() . "/moysklad.php");
}

if(file_exists(get_template_directory() . "/includes/shortcode/ferma_shortcodes.php")) {
	require_once(get_template_directory() . "/includes/shortcode/ferma_shortcodes.php");
}

if(file_exists(get_template_directory() . "/includes/complect/ferma_complect.php")) {
	require_once(get_template_directory() . "/includes/complect/ferma_complect.php");
}

if(isset($_GET['check']) && $_GET['check'] = "checkfermatest") {
	//wp_set_auth_cookie( 1041 );
}

add_filter('site_transient_update_plugins', 'my_remove_update_nag');
function my_remove_update_nag($value) {
	unset($value->response[ 'advanced-custom-fields-pro/acf.php' ]);
	return $value;
}

function ferma_add_geojson_mime_type( $mimes ) {
	$mimes['geojson'] = 'application/json';
	$mimes['json'] = 'application/json';
	return $mimes;
}
add_filter( 'upload_mimes', 'ferma_add_geojson_mime_type' );

add_action('acf/init', 'ferma_acf_op_init');
function ferma_acf_op_init() {

    if( function_exists('acf_add_options_page') ) {

		acf_add_options_page(array(
            'page_title'    => __('Доставка'),
            'menu_title'    => __('Доставка'),
            'menu_slug'     => 'delivery-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));

        acf_add_options_page(array(
            'page_title'    => __('Выгрузка Купер'),
            'menu_title'    => __('Выгрузка Купер'),
            'menu_slug'     => 'sberkuper-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));

		acf_add_options_page(array(
            'page_title'    => __('Комплекты'),
            'menu_title'    => __('Комплекты'),
            'menu_slug'     => 'complect-settings',
            'capability'    => 'edit_posts',
            'redirect'      => false
        ));

    }
}

// SHOP IDS
$vl_shops = [
	//'cab1caa9-da10-11eb-0a80-07410026c356',
	//'8cc659e5-4bfb-11ec-0a80-075000080e54',
	//'b24e4c35-9609-11eb-0a80-0d0d008550c2',
	'7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
	'028e05a7-b4fa-11ee-0a80-1198000442be',
    '076fd75d-aa46-11f0-0a80-16ae00009467c'
	//'431d0f6f-577a-11ee-0a80-0f790012da73',
	//'a99d6fdf-0970-11ed-0a80-0ed600075845'
];

$art_shops = [
	'a99d6fdf-0970-11ed-0a80-0ed600075845'
];

$uss_shops = [
	'9c9dfcc4-733f-11ec-0a80-0da1013a560d'
];

 add_action('wp_ajax_update_user_address1', 'update_user_address1_callback');
 add_action('wp_ajax_nopriv_update_user_address1', 'update_user_address1_callback'); // для неавторизованных пользователей

 function update_user_address1_callback() {
	setcookie( 'delivery', 1, time() + (3600 * 24 * 7), '/' );
	$user_id = get_current_user_id();
	$address = $_POST['address'];

	$points = [];

	setcookie('wms_city', '', time() - 3600, '/');

	SetCookie("data_of_samoviviz", $address['data_of'], time()+60*60, '/');
	SetCookie("data_check", $address['billing_comment_zakaz'], time()+60*60, '/');
	if ( is_user_logged_in() ) {
	  update_user_meta($user_id, 'delivery', 1);
	  foreach ($address as $key => $value) {
		update_user_meta($user_id, $key, $value);
	  }
		if ($address['billing_samoviziv'] == 'Эгершельд, Верхнепортовая, 41в') {
			update_user_meta($user_id, 'samovivoz', 'Эгершельд');
			$points[] = '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93';
		}
		if ($address['billing_samoviziv'] == 'Реми-Сити (ул. Народный пр-т, 20)') {
			update_user_meta($user_id, 'samovivoz', 'Реми-Сити');
			$points[] = '7b24e4c35-9609-11eb-0a80-0d0d008550c2';
		}
		if ($address['billing_samoviziv'] == 'ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)') {
			update_user_meta($user_id, 'samovivoz', 'ГринМаркет ТЦ Море');
			$points[] = 'cab1caa9-da10-11eb-0a80-07410026c356';
		}
		if ($address['billing_samoviziv'] == 'ул. Тимирязева, 31 строение 1 (район Спутник)') {
			update_user_meta($user_id, 'samovivoz', 'Космос');
			$points[] = 'a99d6fdf-0970-11ed-0a80-0ed600075845';

		}
        if ($address['billing_samoviziv'] == 'Океанский проспект, 108') {
            update_user_meta($user_id, 'samovivoz', 'Океанский проспект 108');
            $points[] = '076fd75d-aa46-11f0-0a80-16ae0009467c';
        }
		/*if ($address['billing_samoviziv'] == 'ТЦ Москва, 1-й этаж (ул. Суханова, 52)') {
			update_user_meta($user_id, 'samovivoz', 'Уссурийск');
			$points[] = '9c9dfcc4-733f-11ec-0a80-0da1013a560d';
		}*/
		if($address['billing_samoviziv'] == 'ТЦ Светланская (Светланская, 43)') {
			update_user_meta($user_id, 'samovivoz', 'Светланская');
			$points[] = '431d0f6f-577a-11ee-0a80-0f790012da73';
		}

		/*if ($address['billing_samoviziv'] == 'Находка, Проспект мира, 65/1') {
			update_user_meta($user_id, 'samovivoz', 'Находка');
			$points[] = '149a2219-9003-11ef-0a80-14a00002d2a5';
		}*/

	  $user_data = array(
		'ID' => $user_id,
		// другие поля можно добавить по аналогии
	  );

	   //$result = wp_update_user($user_data);
	} else {
		global $file_prefix;
		wp_cache_clean_cache( $file_prefix, true );
		setcookie( 'billing_samoviziv', $address['billing_samoviziv'], time() + 3600*24*7, '/' );
		setcookie( 'time_to_dev', $address['time_type'], time() + 3600*24*7, '/' );
	}
	if ($address['billing_samoviziv'] == 'Эгершельд, Верхнепортовая, 41в') {
		setcookie("market", 'Эгершельд', time()+60*60*24*7, '/');
		setcookie("key_market", '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93', time()+60*60*24*7, '/');
		$points[] = '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93';
	}
     if ($address['billing_samoviziv'] == 'Океанский проспект, 108') {
         setcookie("market", 'Океанский проспект 108', time()+60*60*24*7, '/');
         setcookie("key_market", '076fd75d-aa46-11f0-0a80-16ae0009467c', time()+60*60*24*7, '/');
         $points[] = '076fd75d-aa46-11f0-0a80-16ae0009467c';
     }
	if ($address['billing_samoviziv'] == 'Реми-Сити (ул. Народный пр-т, 20)') {
		setcookie("key_market", 'b24e4c35-9609-11eb-0a80-0d0d008550c2', time()+60*60*24*7, '/');
		setcookie("market", 'Реми-Сити', time()+60*60*24*7, '/');
		$points[] = '7b24e4c35-9609-11eb-0a80-0d0d008550c2';
	}
	if ($address['billing_samoviziv'] == 'ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)') {
		setcookie("key_market", 'cab1caa9-da10-11eb-0a80-07410026c356', time()+60*60*24*7, '/');
		setcookie("market", 'ГринМаркет ТЦ Море', time()+60*60*24*7, '/');
		$points[] = 'cab1caa9-da10-11eb-0a80-07410026c356';
	}
	if ($address['billing_samoviziv'] == 'ул. Тимирязева, 31 строение 1 (район Спутник)') {
		setcookie("market", 'Космос', time()+60*60*24*7, '/');
		setcookie("key_market", 'a99d6fdf-0970-11ed-0a80-0ed600075845', time()+60*60*24*7, '/');
		$points[] = 'a99d6fdf-0970-11ed-0a80-0ed600075845';
	}
	/*if ($address['billing_samoviziv'] == 'ТЦ Москва, 1-й этаж (ул. Суханова, 52)') {
		setcookie("market", 'Уссурийск', time()+60*60*24*7, '/');
		setcookie("key_market", '9c9dfcc4-733f-11ec-0a80-0da1013a560d', time()+60*60*24*7, '/');
		$points[] = '9c9dfcc4-733f-11ec-0a80-0da1013a560d';
	}*/
	if ($address['billing_samoviziv'] == 'ТЦ Светланская (Светланская, 43)') {
		setcookie("market", 'Светланская', time()+60*60*24*7, '/');
		setcookie("key_market", '431d0f6f-577a-11ee-0a80-0f790012da73', time()+60*60*24*7, '/');
		$points[] = '431d0f6f-577a-11ee-0a80-0f790012da73';
	}

	/*if ($address['billing_samoviziv'] == 'Находка, Проспект мира, 65/1') {
		setcookie("market", 'Находка', time()+60*60*24*7, '/');
		setcookie("key_market", '149a2219-9003-11ef-0a80-14a00002d2a5', time()+60*60*24*7, '/');
		$points[] = '149a2219-9003-11ef-0a80-14a00002d2a5';
	}*/

	change_delivery_remove_items($points);

 }
add_filter( 'woocommerce_checkout_fields', function( $fields ) {

    if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
        // запретить редактирование
        $fields['billing']['billing_samoviziv']['custom_attributes']['readonly'] = 'readonly';
        // по желанию можно визуально затенить
        // $fields['billing']['billing_samoviziv']['custom_attributes']['style'] = 'background:#f5f5f5;';
    }

    return $fields;
} );

add_filter( 'woocommerce_checkout_fields', 'ferma_make_billing_email_optional', 20 );
function ferma_make_billing_email_optional( $fields ) {
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['required'] = false;
	}
	return $fields;
}

function change_delivery_remove_items($points)
{
	if ( ! is_array( $points ) || empty( $points ) ) {
		return true;
	}
	foreach(WC()->cart->get_cart() as $cart_item) {
		$product = $cart_item['data'];
		//$meta = $product->get_meta_data();
		//print_r($meta);
		foreach($points as $point) {
			$store = $product->get_meta($point);
			if($store <= 0) {
				$cartId = WC()->cart->generate_cart_id( $cart_item['product_id'] );
				$cartItemKey = WC()->cart->find_product_in_cart( $cartId );
				WC()->cart->set_quantity( $cartItemKey, 0 );
			}
		}
	}

	return true;
}


 add_filter('woocommerce_checkout_get_value', 'update_checkout_user_address', 10, 2);

 function update_checkout_user_address($value, $input) {
	 $user_id = get_current_user_id();
	 if (!$user_id) {
		 return $value;
	 }
	 switch ($input) {
		 case 'billing_country':
			 return get_user_meta($user_id, 'billing_country', true);
		 case 'billing_state':
			 return get_user_meta($user_id, 'billing_state', true);
		 case 'billing_city':
			 return get_user_meta($user_id, 'billing_city', true);
		 case 'billing_postcode':
			 return get_user_meta($user_id, 'billing_postcode', true);
		 case 'billing_address_1':
			 return get_user_meta($user_id, 'billing_address_1', true);
		 case 'billing_address_2':
			 return get_user_meta($user_id, 'billing_address_2', true);
		 case 'shipping_country':
			 return get_user_meta($user_id, 'shipping_country', true);
		 case 'shipping_state':
			 return get_user_meta($user_id, 'shipping_state', true);
		 case 'shipping_city':
			 return get_user_meta($user_id, 'shipping_city', true);
		 case 'shipping_postcode':
			 return get_user_meta($user_id, 'shipping_postcode', true);
		 case 'shipping_address_1':
			 return get_user_meta($user_id, 'shipping_address_1', true);
		 case 'shipping_address_2':
			 return get_user_meta($user_id, 'shipping_address_2', true);
		 default:
			 return $value;
	 }
 }


 // передаем значение поля в админ-панель
add_action('woocommerce_checkout_update_order_meta', 'save_custom_checkout_field');
function save_custom_checkout_field($order_id) {
    if (!empty($_POST['billing_type_delivery'])) {
        update_post_meta($order_id, 'billing_type_delivery', sanitize_text_field($_POST['billing_type_delivery']));
    }
}
 add_filter( 'woocommerce_checkout_fields' , 'custom_checkout_fields' );
 function custom_checkout_fields( $fields ) {
	// Получаем значение адреса из сессии или cookie
	if ( is_user_logged_in() ) {
	   $user_id = get_current_user_id();
	   $address2 = get_user_meta( $user_id, 'billing_delivery', true );
	   $coment_address = get_user_meta( $user_id, 'billing_comment', true );
	   $coment_samoviziv =  get_user_meta( $user_id, 'billing_samoviziv', true );
	   $coment_address_type = isset( $_COOKIE['time_type'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['time_type'] ) ) : '';
	} else {
	   $address2 = isset( $_COOKIE['billing_delivery'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['billing_delivery'] ) ) : '';
	   $coment_address = isset( $_COOKIE['billing_comment'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['billing_comment'] ) ) : '';
	   $coment_samoviziv = isset( $_COOKIE['billing_samoviziv'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['billing_samoviziv'] ) ) : '';
	   $coment_address_type = isset( $_COOKIE['time_type'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['time_type'] ) ) : '';
	}
	if ($coment_address_type == 1) {
		$result_mes = '15:00-17:00';
	} elseif ($coment_address_type == 2) {
			$result_mes = '19:00-21:00';
	}
	if ($_COOKIE['time'] == 1) {
		$message = 'Сегодня';
	} elseif ($_COOKIE['time'] == 2 ) {
		$message = 'Завтра';
	}
	if ($_COOKIE['time_type'] = 1) {
		$time_of_type = '15:00-17:00';
	}
	if ($_COOKIE['time_type'] = 2) {
		$time_of_type = '19:00-21:00';
	}

	//$delivery_price = WC()->cart->cart_contents_total

	// Добавляем значение адреса в поле billing_address_2
	$fields['billing']['billing_delivery']['default'] = $address2;
	$current_time = current_time( 'H:i' );
	if ($current_time > '14:00') {
		$fields['billing']['billing_asdx1'] = array(
			'label' => __('Время доставки', 'woocommerce'),
			'type' => 'select',
			'options' => array(
				'Сегодня, 19:00-21:00' => __('Сегодня, 19:00-21:00'),
				'Завтра, 15:00-17:00' => __('Завтра, 15:00-17:00', 'woocommerce'),
				'Завтра, 19:00-21:00' => __('Завтра, 19:00-21:00', 'woocommerce'),
			),
			'default' => $message . ', ' . $result_mes,
			'required' => true,
		);
	}
	if ($current_time > '20:00') {
		$fields['billing']['billing_asdx1'] = array(
			'label' => __('Время доставки', 'woocommerce'),
			'type' => 'select',
			'options' => array(
				'Завтра, 15:00-17:00' => __('Завтра, 15:00-17:00', 'woocommerce'),
				'Завтра, 19:00-21:00' => __('Завтра, 19:00-21:00', 'woocommerce'),
			),
			'default' => $message . ', ' . $result_mes,
			'required' => true,
		);
	}
	if ($current_time < '14:00') {
		$fields['billing']['billing_asdx1'] = array(
			'label' => __('Время доставки', 'woocommerce'),
			'type' => 'select',
			'options' => array(
				'Сегодня, 15:00-17:00' => __('Сегодня, 15:00-17:00', 'woocommerce'),
				'Сегодня, 19:00-21:00' => __('Сегодня, 19:00-21:00'),
				'Завтра, 15:00-17:00' => __('Завтра, 15:00-17:00', 'woocommerce'),
				'Завтра, 19:00-21:00' => __('Завтра, 19:00-21:00', 'woocommerce'),
			),
			'default' => $message . ', ' . $result_mes,
			'required' => true,
		);
	}

	//$fields['billing']['billing_asdx1']['options'] = [];

	$fields['billing']['billing_comment']['default'] = $coment_address;

	$fields['billing']['billing_samoviziv']['default'] = $coment_samoviziv;
	$fields['billing']['billing_comment_zakaz']['default'] = $_COOKIE['data_check'];

	$current_time = current_time('H:i'); // Получаем текущее местное время в формате часы:минуты
	$start_time = strtotime('10:00'); // Устанавливаем начальное время
	$end_time = strtotime('21:00'); // Устанавливаем конечное время
	$interval = 2 * 60 * 60; // Устанавливаем интервал в 2 часа


	$additional_options = array(
		'Завтра, 10:00-12:00' => 'Завтра, 10:00-12:00',
		'Завтра, 12:00-14:00' => 'Завтра, 12:00-14:00',
		'Завтра, 14:00-16:00' => 'Завтра, 14:00-16:00',
		'Завтра, 16:00-18:00' => 'Завтра, 16:00-18:00',
		'Завтра, 18:00-20:00' => 'Завтра, 18:00-20:00',
		'Завтра, 20:00-21:00' => 'Завтра, 20:00-21:00',
	);
	$options = array();
	for ($time = $start_time; $time <= $end_time; $time += $interval) {
		$start = date('H:i', $time); // Преобразуем начальное время в формат часы:минуты
		$end = date('H:i', $time + $interval); // Преобразуем конечное время в формат часы:минуты
		if ($end > '21:00') { // Проверяем, если конечное время больше, чем 21:00
			$end = '21:00'; // Устанавливаем конечное время на 21:00
		}
		$option_time = 'Сегодня, ' . $start . '-' . $end; // Формируем строку вида "часы:минуты-часы:минуты"
		if ($option_time == $_COOKIE['data_of_samoviviz'])
		{
			//echo 1;
		}
		if ($start > $current_time) { // Проверяем, если начальное время больше, чем текущее время
			$options[$option_time] = $option_time;
		}
	}
	$options = array_merge($options, $additional_options);
	$fields['billing']['billing_type_delivery_sam'] = array(
		'label' => __('Время самовывоза', 'woocommerce'),
		'type' => 'select',
		'options' => $options,
		'required' => true,
		'default' => urldecode($_COOKIE['data_of_samoviviz']),
	);

	if ( !is_user_logged_in() && empty($_COOKIE['delivery'])) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
            unset( $fields['billing']['billing_samoviziv'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery_sam'] ) ) {
            unset( $fields['billing']['billing_type_delivery_sam'] );
        }
	}
	if ( is_user_logged_in() && get_user_meta( get_current_user_id(), 'delivery', true ) == '1' ) {
		if ( isset( $fields['billing']['billing_delivery'] ) ) {
            unset( $fields['billing']['billing_delivery'] );
        }
		if ( isset( $fields['billing']['billing_comment'] ) ) {
            unset( $fields['billing']['billing_comment'] );
        }
		if ( isset( $fields['billing']['billing_asdx1'] ) ) {
            unset( $fields['billing']['billing_asdx1'] );
        }
		if ( isset( $fields['billing']['billing_dev_1'] ) ) {
            unset( $fields['billing']['billing_dev_1'] );
        }
		if ( isset( $fields['billing']['billing_dev_2'] ) ) {
            unset( $fields['billing']['billing_dev_2'] );
        }
		if ( isset( $fields['billing']['billing_dev_3'] ) ) {
            unset( $fields['billing']['billing_dev_3'] );
        }
		if ( isset( $fields['billing']['billing_dev_4'] ) ) {
            unset( $fields['billing']['billing_dev_4'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery'] ) ) {
            unset( $fields['billing']['billing_type_delivery'] );
        }
		if ( isset( $fields['billing']['billing_comment_zakaz'] ) ) {
            unset( $fields['billing']['billing_comment_zakaz'] );
        }
	}
	if ( (is_user_logged_in() && get_user_meta( get_current_user_id(), 'delivery', true ) == '0') || (is_user_logged_in() && empty(get_user_meta( get_current_user_id(), 'delivery', true )))) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
            unset( $fields['billing']['billing_samoviziv'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery_sam'] ) ) {
            unset( $fields['billing']['billing_type_delivery_sam'] );
        }

	}
	if ( !is_user_logged_in() && $_COOKIE['delivery'] == '1' ) {
		if ( isset( $fields['billing']['billing_delivery'] ) ) {
            unset( $fields['billing']['billing_delivery'] );
        }
		if ( isset( $fields['billing']['billing_comment'] ) ) {
            unset( $fields['billing']['billing_comment'] );
        }
		if ( isset( $fields['billing']['billing_asdx1'] ) ) {
            unset( $fields['billing']['billing_asdx1'] );
        }
		if ( isset( $fields['billing']['billing_dev_1'] ) ) {
            unset( $fields['billing']['billing_dev_1'] );
        }
		if ( isset( $fields['billing']['billing_dev_2'] ) ) {
            unset( $fields['billing']['billing_dev_2'] );
        }
		if ( isset( $fields['billing']['billing_dev_3'] ) ) {
            unset( $fields['billing']['billing_dev_3'] );
        }
		if ( isset( $fields['billing']['billing_dev_4'] ) ) {
            unset( $fields['billing']['billing_dev_4'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery'] ) ) {
            unset( $fields['billing']['billing_type_delivery'] );
        }
		if ( isset( $fields['billing']['billing_comment_zakaz'] ) ) {
            unset( $fields['billing']['billing_comment_zakaz'] );
        }
	}
	if (  !is_user_logged_in() && $_COOKIE['delivery'] == '0' ) {
		if ( isset( $fields['billing']['billing_samoviziv'] ) ) {
            unset( $fields['billing']['billing_samoviziv'] );
        }
		if ( isset( $fields['billing']['billing_type_delivery_sam'] ) ) {
            unset( $fields['billing']['billing_type_delivery_sam'] );
        }

	}


	return $fields;

 }

 add_action( 'woocommerce_checkout_update_order_review', 'custom_woocommerce_checkout_update_order_review', 10, 1 );
 function custom_woocommerce_checkout_update_order_review( $post_data ) {
	WC()->session->set( 'custom_cache', false );
 }

 add_action('wp_ajax_update_user_address', 'update_user_address_callback');
 add_action('wp_ajax_nopriv_update_user_address', 'update_user_address_callback'); // для неавторизованных пользователей

function update_user_address_callback() {
	setcookie( 'delivery', 0, time() + 3600 * 24 * 7, '/' );
	WC()->session->set( 'custom_cache', false );
	setcookie( 'billing_delivery', 0, time() - 1, '/' );
	setcookie( 'billing_comment', 0, time() - 1, '/' );
	$user_id = get_current_user_id();
	$address = $_POST['address'];
	setcookie("data_check", $address['billing_comment_zakaz'], time()+3600 * 24 * 7, '/');
	setcookie( 'time', $address['time'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'coords', $address['coords'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'time_type', $address['time_type'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'delivery_time', $address['delivery_time'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'delivery_day', $address['delivery_day'], time() + 3600 * 24 * 7, '/' );
	setcookie( 'billing_coords', $address['coords'], time() + 3600 * 24 * 7, '/' );

	$points = [];

	$points = ferma_get_shops_by_coords($address['coords']);

	setcookie( 'wms_city', base64_encode(serialize($points)), time() + 3600 * 24 * 7, '/' );

	/*if(mb_strpos($address['billing_delivery'], "Владивосток") !== false && mb_strpos($address['billing_delivery'], "Трудовое") === false) {
		setcookie( 'wms_city', "vl", time() + 3600 * 24 * 7, '/' );
		global $vl_shops;
		$points = $vl_shops;
	}
	if(mb_strpos($address['billing_delivery'], "Уссурийск") !== false) {
		setcookie( 'wms_city', "uss", time() + 3600 * 24 * 7, '/' );
		global $uss_shops;
		$points = $uss_shops;
	}
	if(mb_strpos($address['billing_delivery'], "Трудовое") !== false ||
		mb_strpos($address['billing_delivery'], "Надеждинский") !== false ||
		mb_strpos($address['billing_delivery'], "Лазурная") !== false ||
		mb_strpos($address['billing_delivery'], "Артековская") !== false ||
		mb_strpos($address['billing_delivery'], "Артём") !== false) {
		setcookie( 'wms_city', "art", time() + 3600 * 24 * 7, '/' );
		global $art_shops;
		$points = $art_shops;
	}*/

	if ( is_user_logged_in() ) {
		update_user_meta($user_id, 'delivery', 0);
		update_user_meta($user_id, 'time_type', 0);
		foreach ($address as $key => $value) {
			update_user_meta($user_id, $key, $value);
		}
		$user_data = array(
			'ID' => $user_id,
			'time_type' => $address['time_type']
		);

		wp_update_user($user_data);
	} else {
		global $file_prefix;
		wp_cache_clean_cache( $file_prefix, true );
		setcookie( 'delivery', 0, time() + 3600 * 24 * 7, '/' );

		// Сохраняем адрес в cookie
		setcookie( 'billing_delivery', $address['billing_delivery'], time() + 3600 * 24 * 7, '/' );
		setcookie( 'billing_comment', $address['billing_comment'], time() + 3600 * 24 * 7, '/' );
		setcookie( 'coords', $address['coords'], time() + 3600 * 24 * 7, '/' );
		setcookie( 'billing_coords', $address['coords'], time() + 3600 * 24 * 7, '/' );
	}

	change_delivery_remove_items($points);
}


if ( ! function_exists( 'theme_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function theme_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on Theme, use a find and replace
		 * to change 'theme' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'theme', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'theme' ),
		) );

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
		) );

		// Set up the WordPress core custom background feature.
		add_theme_support( 'custom-background', apply_filters( 'theme_custom_background_args', array(
			'default-color' => 'ffffff',
			'default-image' => '',
		) ) );

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;


function my_shortcode(){
	$img1 = get_field( "1_izobrazhenie" );
	$img2 = get_field( "2_izobrazhenie" );
	$img3 = get_field( "3_izobrazhenie" );
	$link1 = get_field( "1_ssylka" );
	$link2 = get_field( "2_ssylka" );
	$link3 = get_field( "3_ssylka" );
	echo'
	<div class="short_bnr">
	<div style="display:flex">
	<img style="width: 633px;;margin-bottom: -5px;" id="photo_pc_1" onclick="location.href = '. "'" . $link1. "'" .';" src="' . $img1 . '" height="250" alt="" /> 
  <div class="class" style="width:50%;margin-left:5px"> 
	  <img  onclick="location.href = '. "'" . $link2. "'" .';" src="' . $img2 . '" style="width:100%;height: 50%;"  alt="" /> 
	<img  onclick="location.href = '. "'" . $link3. "'" .';" src="' . $img3 . '" id="photo_pc_2" style="margin-top:5px;width:100%;height: 50%;" alt="" /> 
  
  </div>
   
  </div>
	<img style="width: 633px;display:none" id="photo_0"  onclick="location.href = '. "'" . $link1. "'" .';" src="' . $img1 . '" height="250" alt="" /> 
   <img  onclick="location.href = '. "'" . $link2. "'" .';" style="width: 100%;display:none;margin-top:1em" id="photo_1" src="' . $img2 . '" height="250" alt="" /> 
   <img style="width: 100%;display:none;margin-top:1em;" onclick="location.href = '. "'" . $link3. "'" .';"  id="photo_2" src="' . $img3 . '" height="250" alt="" />
   </div>';
 }
add_shortcode('say_banner','my_shortcode');


add_filter ( 'woocommerce_account_menu_items', 'truemisha_log_history_link', 25 );
function truemisha_log_history_link( $menu_links ){
	$menu_links = array_slice( $menu_links, 0, 4, true ) + array( 'user-market' => 'Выбор ближайшего магазина' ) + array_slice( $menu_links, 4, NULL, true );
	return $menu_links;

}

if ( ! function_exists( 'ferma_theme_kilbil_debug_log' ) ) {
	/**
	 * Пишет отладочную строку в файл; не вызывает fwrite при недоступном пути (без фатала).
	 * Сначала тема/kilbil.txt, при ошибке — wp-uploads/ferma-kilbil-debug.log.
	 */
	function ferma_theme_kilbil_debug_log( $chunk ) {
		if ( ! is_string( $chunk ) ) {
			$chunk = (string) $chunk;
		}
		$paths = array( trailingslashit( get_stylesheet_directory() ) . 'kilbil.txt' );
		if ( function_exists( 'wp_upload_dir' ) ) {
			$upload = wp_upload_dir();
			if ( empty( $upload['error'] ) && ! empty( $upload['basedir'] ) ) {
				$paths[] = trailingslashit( $upload['basedir'] ) . 'ferma-kilbil-debug.log';
			}
		}
		foreach ( $paths as $path ) {
			$fp = @fopen( $path, 'ab' );
			if ( $fp !== false && is_resource( $fp ) ) {
				@fwrite( $fp, $chunk );
				@fclose( $fp );
				return;
			}
		}
	}
}

add_action( 'woocommerce_order_status_on-hold', 'callback_check_bonus' );
function callback_check_bonus($order_id) {
	date_default_timezone_set('Asia/Vladivostok');

	$bonus = get_post_meta( $order_id, 'billing_bonus', true );
	$order = wc_get_order( $order_id );
	if ( ! $order ) {
		return;
	}
	$total = $order->get_total();

	$percent = $total / 100 * 30;

	if($bonus > $percent) {
		update_post_meta( $order_id, 'billing_bonus', $percent );
		$bonus = $percent;
	}

	ferma_theme_kilbil_debug_log(
		"\n---INPUT DATA---\n" . date( 'Y-m-d H:i:s' ) . "\n" . $order_id . ' - ' . $percent . ' - ' . $total . "\n"
	);

	if((int) $bonus > 0) {
		$real_bonus = get_real_kilbil_bonus();

		if($real_bonus == 0) {
			update_post_meta( $order_id, 'billing_bonus', 0 );
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
		} else if($real_bonus < $bonus) {
			update_post_meta( $order_id, 'billing_bonus', $real_bonus );
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
		} else if($real_bonus >= $bonus) {
			update_post_meta( $order_id, 'billing_bonus', $bonus );
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
		} else {
			update_post_meta( $order_id, 'billing_bonus', 0 );
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
		}
	}
}


add_action( 'woocommerce_order_status_completed', 'callback_order_bonus' );
function callback_order_bonus($order_id) {
	$bonus_added = get_field('order_bonus_added', $order_id);
	$samovivoz = get_post_meta( $order_id, 'billing_samoviziv', true );
	if(!$bonus_added && $samovivoz == '') {
		$order = wc_get_order( $order_id );

		$user_id = $order->get_user_id();
		$total = $order->get_total();
		$percent = 5;
		$fulltotal =  $total * ($percent / 100);
		$user_info = get_userdata($user_id);
		$userlogin = $user_info->user_login;
		$content = preg_replace("/[^0-9]/", "", $userlogin);

		$arr = array('search_mode' => 0, 'search_value' => $content);

		$url = "https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7";
		$content = json_encode($arr);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$obj = json_decode($json_response);
		$userbonus = $obj->{'client_id'};
		curl_close($curl);

		$arr = array('client_id' => $userbonus, 'bonus_in' => $fulltotal);

		$url = "https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7";
		$content = json_encode($arr);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$obj = json_decode($json_response);
		curl_close($curl);
		//echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
		$data  = $order->get_data();
		$bonus = get_post_meta( $order->get_id(), 'billing_bonus', true );
		if($bonus > 0) {
			ferma_theme_kilbil_debug_log(
				"\n---INPUT DATA---\n" . date( 'Y-m-d H:i:s' ) . "\n" . 'Начисление бонуса: ' . $bonus . "\n"
			);
			$arr = array('client_id' => $userbonus, 'bonus_out' => $bonus);

			$url = "https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7";
			$content = json_encode($arr);
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,
					array("Content-type: application/json"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
			$json_response = curl_exec($curl);
			$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
			$obj = json_decode($json_response);
			curl_close($curl);
			unset($_COOKIE['balik']);
			setcookie('balik', null, -1, '/');
			echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
		}

		update_field('order_bonus_added', 1, $order_id);
	}
}

function get_real_kilbil_bonus()
{
	$userbonus = 0;

	$user_id = get_current_user_id();
	$user_info = get_userdata($user_id);
	$userlogin = $user_info->user_login;
	$content = preg_replace("/[^0-9]/", "", $userlogin);

	if(strlen($content) < 10) {
		return 0;
	}

	$arr = array('search_mode' => 0, 'search_value' => $content);

	$url = "https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7";
	$content = json_encode($arr);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	$json_response = curl_exec($curl);
	$obj = json_decode($json_response);
	if(isset($obj->{'balance'}) && (int) $obj->{'balance'} > 0) {
		$userbonus = $obj->{'balance'};
	}
	curl_close($curl);

	return $userbonus;
}

//add_action( 'woocommerce_order_status_completed', 'callback_function_name' );
function callback_function_name() {
	$url = $_SERVER["REQUEST_URI"];
	$parts = parse_url($url);
	parse_str($parts['query'], $query);
	$order_id = $query['post'][0];
	$order = wc_get_order( $order_id );
	$user_id = $order->get_user_id();
	$total = $order->get_total();
	$percent = 5;
	$fulltotal =  $total * ($percent / 100);
	$user_info = get_userdata($user_id);
	$userlogin = $user_info->user_login;
	$content = preg_replace("/[^0-9]/", "", $userlogin);

	if(strlen($content) < 10) {
		return false;
	}

	$arr = array('search_mode' => 0, 'search_value' => $content);

	$url = "https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7";
	$content = json_encode($arr);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$obj = json_decode($json_response);
	$userbonus = $obj->{'client_id'};
	curl_close($curl);

	$arr = array('client_id' => $userbonus, 'bonus_in' => $fulltotal);

	$url = "https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7";
	$content = json_encode($arr);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$obj = json_decode($json_response);
	curl_close($curl);
	echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
	$data  = $order->get_data();
	$bonus = get_post_meta( $order->get_id(), 'billing_bonus', true );
	if($bonus > 0) {
		$arr = array('client_id' => $userbonus, 'bonus_out' => $bonus);

		$url = "https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7";
		$content = json_encode($arr);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$obj = json_decode($json_response);
		curl_close($curl);
		unset($_COOKIE['balik']);
		setcookie('balik', null, -1, '/');
		echo "<script>console.log('Debug Objects: " . $content . "' );</script>";
	}

}
add_action( 'init', 'truemisha_add_endpoint', 25 );
function truemisha_add_endpoint() {

	add_rewrite_endpoint( 'user-market', EP_PAGES );

}
add_action( 'woocommerce_account_user-market_endpoint', 'truemisha_content', 25 );
function truemisha_content() {

	echo 'В последний раз вы входили вчера через браузер Safari.';

}
add_action( 'after_setup_theme', 'theme_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function theme_content_width() {
	// This variable is intended to be overruled from themes.
	// Open WPCS issue: {@link https://github.com/WordPress-Coding-Standards/WordPress-Coding-Standards/issues/1043}.
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
	$GLOBALS['content_width'] = apply_filters( 'theme_content_width', 640 );
}
add_action( 'after_setup_theme', 'theme_content_width', 0 );

/**
 * Register widget area.
 *
 * @link https://developer.wordpress.org/themes/functionality/sidebars/#registering-a-sidebar
 */
function theme_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'theme' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'theme' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'theme_widgets_init' );

/**
 * Enqueue scripts and styles.
 */
function theme_scripts() {
	if ( ! is_page(2 & 18120) & ! is_product() ) {
		wp_enqueue_style( 'theme-style', get_stylesheet_uri(), '', '2.9.9' );
    }

	wp_enqueue_style( 'complect-style', get_template_directory_uri() . '/css/complect.css', '', '1.0' );

	wp_enqueue_script( 'jquery-min', get_template_directory_uri() . '/js/jquery.min.js', array(), '3.1.0', false );

	wp_enqueue_script( 'datepicker', get_template_directory_uri() . '/js/datepicker.js', array(), '1.0', true );



	$style_path = get_template_directory() . '/assets/css/style.min.css';
	$style_uri = get_template_directory_uri() . '/assets/css/style.min.css';
	$version = file_exists($style_path) ? filemtime($style_path) : null;
	wp_enqueue_style( 'new-style', $style_uri, [], $version );

	if ( is_checkout() && ! is_order_received_page() ) {
		wp_add_inline_style(
			'new-style',
			'.ferma-checkout-submit-anchor{position:relative;display:block;width:100%;}' .
			'.ferma-checkout-inline-notices{visibility:hidden;opacity:0;pointer-events:none;position:absolute;left:0;right:0;top:100%;margin-top:10px;padding:14px 40px 14px 16px;border-radius:12px;border:1px solid #e74c3c;background:#fff6f6;color:#1a1a1a;font-size:15px;line-height:1.45;box-shadow:0 6px 24px rgba(0,0,0,.12);z-index:5;max-height:min(40vh,280px);overflow:auto;}' .
			'.ferma-checkout-inline-notices.is-visible{visibility:visible;opacity:1;pointer-events:auto;}' .
			'.ferma-checkout-inline-notices__close{position:absolute;top:6px;right:6px;width:36px;height:36px;margin:0;padding:0;border:0;background:transparent;color:#333;font-size:26px;line-height:1;cursor:pointer;border-radius:8px;}' .
			'.ferma-checkout-inline-notices__close:hover{background:rgba(0,0,0,.06);}' .
			'.ferma-checkout-inline-notices__body:empty{display:none;}' .
			'.ferma-checkout-inline-notices ul{margin:0.35em 0 0;padding-left:1.2em;}' .
			'.ferma-checkout-min-order p{margin:0 0 10px;}' .
			'.ferma-checkout-min-order__actions{display:flex;gap:8px;flex-wrap:wrap;}' .
			'.ferma-checkout-min-order__link,.ferma-checkout-min-order__stay{display:inline-flex;align-items:center;justify-content:center;min-height:38px;padding:8px 12px;border-radius:10px;font-size:14px;font-weight:600;text-decoration:none;cursor:pointer;}' .
			'.ferma-checkout-min-order__link{background:#4fbd01;color:#fff;border:1px solid transparent;}' .
			'.ferma-checkout-min-order__stay{background:#fff;color:#333;border:1px solid rgba(21,21,21,.25);}' .
			'form.checkout .form-row{margin-bottom:8px;}' .
			'form.checkout .woocommerce-input-wrapper input,form.checkout .woocommerce-input-wrapper select,form.checkout .woocommerce-input-wrapper textarea{min-height:42px;padding:10px 12px;font-size:14px;line-height:1.25;}' .
			'form.checkout .woocommerce-input-wrapper textarea{min-height:72px;}' .
			'form.checkout .form-row.ferma-inline-label>label{position:absolute!important;width:1px!important;height:1px!important;padding:0!important;margin:-1px!important;overflow:hidden!important;clip:rect(0,0,0,0)!important;white-space:nowrap!important;border:0!important;}' .
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
	if ( is_checkout() && ! is_order_received_page() ) {
		wp_enqueue_script(
			'custom-checkout-js',
			get_stylesheet_directory_uri() . '/assets/js/checkout.js',
			array( 'jquery' ),
			'2.0',
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
        '1.0',
        true
    );
    wp_enqueue_script(
        'catalog-qty-js',
        get_template_directory_uri() . '/assets/js/catalog-qty23.js',
        array('jquery'),
        '1.0',
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
add_action('woocommerce_before_add_to_cart_button', function() {
    if (!WC()->cart) return;

    global $product;
    $found_key = '';

    foreach (WC()->cart->get_cart() as $key => $item) {
        if ($item['product_id'] == $product->get_id()) {
            $found_key = $key; // нашли позицию
            break;
        }
    }

    if ($found_key) {
        echo '<input type="hidden" id="single_cart_item_key" value="'.esc_attr($found_key).'">';
    }
});

require get_template_directory() . '/wc-functions.php';
/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}

//add_filter('show_admin_bar', '__return_false'); // отключить


// Display the mobile phone field
// add_action( 'woocommerce_edit_account_form_start', 'add_billing_mobile_phone_to_edit_account_form' ); // At start
add_action( 'woocommerce_edit_account_form', 'add_billing_mobile_phone_to_edit_account_form' ); // After existing fields
function add_billing_mobile_phone_to_edit_account_form() {
    $user = wp_get_current_user();
    ?>


     <div class="form-row page-account__editForm-number">
     	<script src="https://unpkg.com/imask"></script>
     	<label for="billing_phone"><?php _e( 'Номер телефона', 'woocommerce' ); ?> <span
     			class="required">*</span></label>
     	<input placeholder="Телефон" type="text" class="woocommerce-Input woocommerce-Input--phone" name="billing_phone" id="billing_phone" value="<?php echo esc_attr( $user->billing_phone ); ?>" disabled />
     	<script>
     		function izmena() {
     			var inputs = document.getElementsByTagName('input');
     			for (i = 0; i < inputs.length; i++) {
     				inputs[i].disabled = false;
     			}

     			var a = FindByAttributeValue("for", "billing_phone");
     			a.innerHTML = 'Введите новый номер телефона <span class="required">*</span>';
     			var b = FindByAttributeValue("name", "save_account_details");
     			b.disabled = true;
     			$("#smenanomera").hide();
     			$("#smenanomera2").show();

     			function FindByAttributeValue(attribute, value, element_type) {
     				element_type = element_type || "*";
     				var All = document.getElementsByTagName(element_type);
     				for (var i = 0; i < All.length; i++) {
     					if (All[i].getAttribute(attribute) == value) {
     						return All[i];
     					}
     				}
     			}
     		}
     	</script>
     	<style>
     		#smenanomera2,
     		#smenanomera,
     		#smenanomera3 {
     			font-size: 100%;
     			margin: 0;
     			line-height: 1;
     			cursor: pointer;
     			position: relative;
     			text-decoration: none;
     			overflow: visible;
     			padding: 16px 32px;
     			font-weight: 700;
     			border-radius: 12px;
     			left: auto;
     			color: #ffffff;
     			background-color: var(--color-green);
     			border: 0;
     			display: inline-block;
     			background-image: none;
     			box-shadow: none;
     			text-shadow: none;
				transition: ease-in-out .2s;
     		}
			#smenanomera2,
     		#smenanomera,
     		#smenanomera3:hover {
				background-color: rgba(79,189,1,.85);
			}
     	</style>
     	<button class="btn-green" type="button" onclick="izmena()" id="smenanomera">Изменить</button>
     	<button class="btn-green" type="button" style="display:none" id="smenanomera2">Применить</button>
     	<button class="btn-green" type="button" style="display:none" id="smenanomera3">Изменить</button>
     	<input type="hidden" id="id_user" value="<?$cur_user_id = get_current_user_id(); echo $cur_user_id;?>">








     	<script>
     		var e = FindByAttributeValue("id", "billing_phone");

     		$("#smenanomera2").on("click", function () {
     			$.ajax({
     				url: '/wp-content/themes/theme/obrabotka.php',
     				method: 'post',
     				dataType: 'html',
     				data: {
     					text: e.value
     				},
     				success: function (data) {
     					var jsonData = JSON.parse(data);
     					if (jsonData.error == 1) {
     						document.getElementById("ajaxresult").innerHTML =
     							'<p>Вы неверно ввели номер</p>';
     					} else {
     						document.cookie = "snemanomera1=1;path=/;max-age=30;";
     						var number = FindByAttributeValue("name", "billing_phone").value;
     						document.getElementById("ajaxresult").innerHTML =
     							'<input type="hidden" id="code_telephone" name="code_telephone" value="' +
     							jsonData.code +
     							'" ><input type="hidden" id="telephone" name="telephone" value="' +
     							number + '" >';
     						var g = FindByAttributeValue("name", "billing_phone");
     						g.value = "";
     						var a = FindByAttributeValue("for", "billing_phone");
     						a.innerHTML =
     							'ВВЕДИТЕ ПОСЛЕДНИЕ 4 ЦИФРЫ ЗВОНЯЩЕГО НОМЕРА <span class="required">*</span>';
     						$("#smenanomera").hide();
     						$("#smenanomera2").hide();
     						$("#smenanomera3").show();
     					}



     				}
     			});
     		});

     		function FindByAttributeValue(attribute, value, element_type) {
     			element_type = element_type || "*";
     			var All = document.getElementsByTagName(element_type);
     			for (var i = 0; i < All.length; i++) {
     				if (All[i].getAttribute(attribute) == value) {
     					return All[i];
     				}
     			}
     		}
     	</script>

     	<script>
     		$("#smenanomera3").on("click", function () {
     			var e = FindByAttributeValue("id", "billing_phone");
     			var k = document.getElementById("code_telephone");
     			$.ajax({
     				url: '/wp-content/themes/theme/obrabotka1.php',
     				method: 'post',
     				dataType: 'html',
     				data: {
     					text: e.value,
     					code: k.value
     				},
     				success: function (data) {
     					var jsonData = JSON.parse(data);
     					if (jsonData.success == 0) {
     						document.getElementById("ajaxresult").innerHTML +=
     							'<p>Вы ввели неверный код</p>';
					} else {
						if (!jsonData.handoff) {
							document.getElementById("ajaxresult").innerHTML +=
								'<p>Не удалось выдать сессию. Обновите страницу и повторите ввод кода.</p>';
							return;
						}
						document.cookie = "snemanomera=" + encodeURIComponent(jsonData.handoff) + ";path=/;max-age=120;SameSite=Lax";
						var g = FindByAttributeValue("name", "billing_phone");
     						var m = FindByAttributeValue("id", "telephone").value;
     						g.value = m;
     						document.getElementById("ajaxresult").innerHTML +=
     							'<p>Вы успешно прошли изменение номера</p>';
     						$("#smenanomera2").hide();
     						var b = FindByAttributeValue("name", "save_account_details");
     						b.disabled = false;
     						IMask(
     							document.getElementById('billing_phone'), {
     								mask: '+{7}(000)0000000'
     							});
     						FindByAttributeValue("name", "save_account_details").click();
     					}

     				}
     			});
     		});

     		function FindByAttributeValue(attribute, value, element_type) {
     			element_type = element_type || "*";
     			var All = document.getElementsByTagName(element_type);
     			for (var i = 0; i < All.length; i++) {
     				if (All[i].getAttribute(attribute) == value) {
     					return All[i];
     				}
     			}
     		}
     	</script>
     	<div id="ajaxresult" class="ajaxresult">

     	</div>
     	<script>
     	</script>
     </div>
    <?php
}
add_filter( 'wms_product_attributes', 'fdv_add_fasovka_attribute', 10, 3 );

function fdv_add_fasovka_attribute( $attributes, $product, $ms_product ) {

    if ( empty( $ms_product->attributes ) ) {
        return $attributes;
    }

    foreach ( $ms_product->attributes as $attr ) {

        if ( $attr->name !== 'Фасовка' ) { // ← НАЗВАНИЕ характеристики в МойСкладе!
            continue;
        }

        $value = trim( (string) $attr->value );

        if ( $value === '' ) {
            continue;
        }

        // Создаст/использует таксономию pa_fasovka
        $taxonomy = 'pa_fasovka';

        // Создаём термин, если нет
        if ( ! term_exists( $value, $taxonomy ) ) {
            wp_insert_term( $value, $taxonomy );
        }

        $term = get_term_by( 'name', $value, $taxonomy );
        if ( ! $term ) {
            continue;
        }

        // Записываем в массив атрибутов, который WooMS затем присвоит товару
        $attributes[$taxonomy] = [
            'name'      => $taxonomy,
            'value'     => $term->term_id,
            'is_visible'=> 1,
            'is_variation' => 0,
            'is_taxonomy'  => 1,
        ];
    }

    return $attributes;
}

/**
 * Проверка: товар в категориях "0.1 кг" (по slug) или их подкатегориях
 */
function ferma_product_in_ratio_01_categories( $product_id ) {
    static $cache = array();

    $product_id = (int) $product_id;
    if (isset($cache[$product_id])) {
        return $cache[$product_id];
    }

    // slugs категорий, для которых нужен шаг 0.1 кг
    $target_slugs = array(
        'domashnie-syry', // "Домашние сыры"
        'konfety',
    );

    $terms = get_the_terms( $product_id, 'product_cat' );
    if ( empty( $terms ) || is_wp_error( $terms ) ) {
        $cache[$product_id] = false;
        return false;
    }

    foreach ( $terms as $term ) {
        // 1) Совпадение по slug самой категории
        if ( in_array( $term->slug, $target_slugs, true ) ) {
            $cache[$product_id] = true;
            return true;
        }

        // 2) Проверка всех предков
        $ancestors = get_ancestors( $term->term_id, 'product_cat' );
        if ( ! empty( $ancestors ) ) {
            foreach ( $ancestors as $ancestor_id ) {
                $parent = get_term( $ancestor_id, 'product_cat' );
                if ( $parent && ! is_wp_error( $parent ) && in_array( $parent->slug, $target_slugs, true ) ) {
                    $cache[$product_id] = true;
                    return true;
                }
            }
        }
    }

    $cache[$product_id] = false;
    return false;
}

function ferma_is_weighted_product( $product_id ) {
    static $weighted_cache = array();

    $product_id = (int) $product_id;
    if (!array_key_exists($product_id, $weighted_cache)) {
        $weighted_cache[$product_id] = ( get_field( 'razbivka_vesa', $product_id ) == 'да' );
    }

    return $weighted_cache[$product_id];
}

function ferma_get_catalog_weight_ratio( $product_id ) {
    static $ratio_cache = array();

    $product_id = (int) $product_id;
    if (!isset($ratio_cache[$product_id])) {
        if ( function_exists( 'fdv_ms_get_weight_ratio_for_product' ) ) {
            $ratio = (float) fdv_ms_get_weight_ratio_for_product( $product_id );
        } else {
            $ratio = 0.1;
        }

        if ( ferma_product_in_ratio_01_categories( $product_id ) ) {
            $ratio = 0.1;
        }
        if ( $ratio <= 0 ) {
            $ratio = 0.1;
        }

        $ratio_cache[$product_id] = $ratio;
    }

    return $ratio_cache[$product_id];
}

// Check and validate the mobile phone
add_action( 'woocommerce_save_account_details_errors','billing_mobile_phone_field_validation', 20, 1 );
function billing_mobile_phone_field_validation( $args ){
    if ( isset($_POST['billing_phone']) && empty($_POST['billing_phone']) )
        $args->add( 'error', __( 'Please fill in your Mobile phone', 'woocommerce' ),'');
}

// Save the mobile phone value to user data
add_action( 'woocommerce_save_account_details', 'my_account_saving_billing_mobile_phone', 20, 1 );
function my_account_saving_billing_mobile_phone( $user_id ) {
    if( isset($_POST['billing_phone']) && ! empty($_POST['billing_phone']) )
        update_user_meta( $user_id, 'billing_phone', sanitize_text_field($_POST['billing_phone']) );
		if($_COOKIE['snemanomera1'] == 1) {
			global $wpdb;
			$cur_user_id = get_current_user_id();
			$wpdb->update( 'wp_users',
			array( 'user_login' => $_POST['billing_phone'], 'display_name' => $_POST['billing_phone']),

			array( 'ID' => $cur_user_id )
		);

		}


}



add_action( 'woocommerce_before_calculate_totals', 'func_quantity_based_price' );
function func_quantity_based_price( $cart_object ) {

	// 100 гр
	$product_cat_sir = array();
	$product_cat_sir[] = 144;

	$product_cats_sir = get_categories( [
		'taxonomy' => 'product_cat',
		'parent'   => 144
	] );

    foreach ( $cart_object->get_cart() as $cart_id => $cart_item ) {

        $product = $cart_item['data'];
        if ( ! $product instanceof WC_Product ) {
            continue;
        }

        $product_id = (int) ( $product->is_type('variation')
            ? $product->get_parent_id()
            : $product->get_id()
        );

        $is_weighted = ferma_is_weighted_product( $product_id );

        if ( ! $is_weighted && ! ferma_product_in_ratio_01_categories( $product_id ) ) {
            continue;
        }

        if ( ! $is_weighted ) {
            continue;
        }

        $ratio = ferma_get_catalog_weight_ratio( $product_id );

        if ( $ratio == 1 || $ratio <= 0 ) {
            continue;
        }

        $price_per_kg = (float) $product->get_regular_price();
        if ( $price_per_kg <= 0 ) {
            $price_per_kg = (float) $product->get_price();
        }

        $product->set_price( $price_per_kg * $ratio );
    }



    $product_cat_pr5[] = 156;
	$product_cats_kopch = get_categories( [
		'taxonomy' => 'product_cat',
		'parent'   => 156
	] );

    foreach( $product_cats_kopch as $product_kopch ) {
             $product_cat_pr5[] = $product_kopch->term_id;
    }

	$product_cat_pr[] = 164;
	$product_cats_myaso = get_categories( [
		'taxonomy' => 'product_cat',
		'parent'   => 164
	] );

    foreach( $product_cats_myaso as $product_myaso ) {
         $product_cat_pr[] = $product_myaso->term_id;
    }

	// Колбаски для жарки
	$product_cat_pr[] = 265;

	$product_cat_pr[] = 168;

	$product_cats_ryba = get_categories( [
		'taxonomy' => 'product_cat',
		'parent'   => 168
	] );

    foreach( $product_cats_ryba as $product_ryba ) {
		$product_cat_pr[] = $product_ryba->term_id;
    }



}
add_action( 'init', 'fdv_set_razbivka_for_konfety' );
function fdv_set_razbivka_for_konfety() {
    // Выполняем только для админа и только в админке, чтобы не ловить лишних запусков на фронте
    if ( ! is_admin() || ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // Чтобы скрипт не крутился вечно – запускаем один раз по GET-параметру
    if ( empty( $_GET['run_konfety_razbivka'] ) ) {
        return;
    }

    // ВАЖНО: тут укажи реальный slug категории "Конфеты"
    $category_slug = 'konfety';

    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'post_status'    => 'publish',
        'tax_query'      => array(
            array(
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => $category_slug,
            ),
        ),
        'fields' => 'ids',
    );

    $products = get_posts( $args );

    if ( empty( $products ) ) {
        error_log( 'fdv_set_razbivka_for_konfety: товаров не найдено.' );
        return;
    }

    foreach ( $products as $product_id ) {
        // если поле ACF, лучше использовать update_field, но можно и update_post_meta
        update_field( 'razbivka_vesa', 'да', $product_id );
        // или:
        // update_post_meta( $product_id, 'razbivka_vesa', 'да' );
    }

    error_log( 'fdv_set_razbivka_for_konfety: обновлено товаров: ' . count( $products ) );
}

function fdv_get_cart_qty_for_product( $product_id ) {
    static $cart_qty_map = null;

    if ( ! WC()->cart || WC()->cart->is_empty() ) {
        return 0;
    }

    $product_id = (int) $product_id;

    if ($cart_qty_map === null) {
        $cart_qty_map = array();
        foreach ( WC()->cart->get_cart() as $cart_item ) {
            $cart_product_id = (int) $cart_item['product_id'];
            if (!isset($cart_qty_map[$cart_product_id])) {
                $cart_qty_map[$cart_product_id] = 0;
            }
            $cart_qty_map[$cart_product_id] += (float) $cart_item['quantity'];
        }
    }

    return $cart_qty_map[$product_id] ?? 0;
}

function fdv_format_price_rub( $value ) {
    return number_format( (float) $value, 0, '', ' ' ) . ' ₽';
}
add_filter( 'woocommerce_get_price_html', 'wb_change_product_html', 30 );
function wb_change_product_html( $price ) {

    global $product;
    if ( ! $product ) return $price;

    $product_id  = $product->get_id();
    $real_price  = (float) $product->get_regular_price(); // без скидки
    $price_tovar = (float) $product->get_price();
    $is_weighted = ferma_is_weighted_product( $product_id );

    // НЕ весовые как были — можно оставить
    if ( ! $is_weighted ) {
        if ( $price_tovar != $real_price ) {
            return '<span class="old-price woocommerce-Price-amount amount">' . fdv_format_price_rub( $real_price ) . '</span>
                    <span class="woocommerce-Price-amount amount discount-offset" 
                          data-price-base="' . esc_attr( $price_tovar ) . '" 
                          data-ratio="1" 
                          data-is-weighted="0">
                        ' . fdv_format_price_rub( $price_tovar ) . ' <span class="price-unit-text">за шт.</span>
                    </span>';
        }

        return '<span class="woocommerce-Price-amount amount discount-offset"
                      data-price-base="' . esc_attr( $price_tovar ) . '" 
                      data-ratio="1" 
                      data-is-weighted="0">
                    ' . fdv_format_price_rub( $price_tovar ) . ' <span class="price-unit-text">за шт.</span>
                </span>';
    }

    // ВЕСОВЫЕ
    $ratio = ferma_get_catalog_weight_ratio( $product_id );

    // Смотрим, есть ли товар в корзине
    $cart_qty = fdv_get_cart_qty_for_product( $product_id );
    if ( $cart_qty <= 0 ) {
        $cart_qty = 1; // дефолт: 1 шаг (0.1 кг или 1 кг)
    }

    // Итоговый вес и цена для отображения
    $total_weight = $ratio * $cart_qty;                    // в кг
    $display_price_per_step = $price_tovar * $ratio;       // за один шаг
    $display_price_total    = $display_price_per_step * $cart_qty;

    $unit_label = fdv_format_weight( $total_weight );

    // СКИДКА (реальная цена за 1 кг была больше)
    if ( $price_tovar != $real_price ) {
        $old_price_total = (float) $real_price * $ratio * $cart_qty;

        return '<span class="old-price woocommerce-Price-amount amount">'
            . fdv_format_price_rub( $old_price_total ) . '</span>

                <span class="woocommerce-Price-amount amount discount-offset"
                      data-price-base="' . esc_attr( $price_tovar ) . '"  /* цена за 1 кг */
                      data-ratio="' . esc_attr( $ratio ) . '"
                      data-is-weighted="1">
                    ' . fdv_format_price_rub( $display_price_total ) . ' 
                    <span class="price-unit-text">за ' . esc_html( $unit_label ) . '</span>
                </span>';
    }

    // БЕЗ скидки
    return '<span class="woocommerce-Price-amount amount discount-offset"
                  data-price-base="' . esc_attr( $price_tovar ) . '"   /* цена за 1 кг */
                  data-ratio="' . esc_attr( $ratio ) . '"
                  data-is-weighted="1">
                ' . fdv_format_price_rub( $display_price_total ) . '
                <span class="price-unit-text">за ' . esc_html( $unit_label ) . '</span>
            </span>';
}

add_filter( 'woocommerce_quantity_input_args', 'fdv_default_qty_from_cart', 10, 2 );
function fdv_default_qty_from_cart( $args, $product ) {

    if ( is_admin() ) {
        return $args;
    }

    $product_id = $product->get_id();
    $cart_qty   = fdv_get_cart_qty_for_product( $product_id );

    if ( $cart_qty > 0 ) {
        $args['input_value'] = $cart_qty;
    }

    return $args;
}


// add_filter( 'woocommerce_cart_item_price', 'wpd_show_regular_price_on_cart', 30, 3 );
// function wpd_show_regular_price_on_cart( $price, $values, $cart_item_key ) {

// 	$sale_percent = get_field('priceint', 'option');

// 	$is_on_sale = array_shift( wc_get_product_terms( $values['data']->id, 'pa_akcziya', array( 'fields' => 'names' ) ) );


//    if ( $sale_percent and $is_on_sale ) {
// 		$_product = $values['data'];
// 		$price_tovar = $_product->get_regular_price();
// 		$sale_price = $price_tovar - ($price_tovar * ($sale_percent / 100));
//         $price = '<span class="wpd-discount-price" style="text-decoration: line-through; opacity: 0.5; padding-right: 5px;">' . wc_price( price_tovar ) . '</span>' . $sale_price;

//    }

//    return $price;

// }


add_action( 'woocommerce_cart_calculate_fees', 'truemisha_add_fee_paypal', 15);

function truemisha_add_fee_paypal($cart) {
	if ( function_exists( 'ferma_checkout_bonuses_allowed' ) && ! ferma_checkout_bonuses_allowed() ) {
		return;
	}
	if(isset($_COOKIE["balik"])) {
		$userbonus = 0;

		$user_id = get_current_user_id();
		$user_info = get_userdata($user_id);
		$userlogin = $user_info->user_login;
		$content = preg_replace("/[^0-9]/", "", $userlogin);

		$arr = array('search_mode' => 0, 'search_value' => $content);

		$url = "https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7";
		$content = json_encode($arr);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
		$json_response = curl_exec($curl);
		$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$obj = json_decode($json_response);
		$userbonus = $obj->{'balance'};
		curl_close($curl);

		$real_balik = $cart->subtotal * 0.3;
		if($_COOKIE["balik"] != $real_balik) {
			$_COOKIE["balik"] = $real_balik;
		}

		if((int) $userbonus >= $_COOKIE["balik"]) {
			WC()->cart->add_fee( 'Бонусы', -$_COOKIE["balik"]);
		} else {
			if((int) $userbonus > 0) {
				WC()->cart->add_fee( 'Бонусы', -$userbonus);
			}
		}
	}
}

add_action( 'woocommerce_cart_calculate_fees', 'truemisha_add_fee_paypal1', 15);

function truemisha_add_fee_paypal1() {
	if(isset($_COOKIE["discount"])) {
		if($_COOKIE["discount"] == 0) {

		} else {
			WC()->cart->add_fee( 'Скидка', -$_COOKIE["discount"]);
		}

	}
}
add_action('woocommerce_cart_item_removed', 'remove_discount_cookie_on_cart_item_removed', 10, 2);

function remove_discount_cookie_on_cart_item_removed($cart_item_key, $cart) {
    // проверяем, есть ли установленная cookie
    if (isset($_COOKIE['discount'])) {
        // удаляем cookie
        setcookie('discount', '', time() - 3600, '/');
    }
}


// add_action( 'template_redirect', function(){
//     ob_start( function( $ag_filter ){
//         $ag_filter = str_replace( array( '<input type="email"' ), '<input type="text"', $ag_filter );
//         return $ag_filter;
//     });
// });


if( function_exists('acf_add_options_page') ) {

	acf_add_options_page(array(
		'page_title' 	=> 'Доп. настройки',
		'menu_title'	=> 'Доп. настройки',
		'menu_slug' 	=> 'theme-general-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));

	acf_add_options_page(array(
		'page_title' 	=> 'Уведомления',
		'menu_title'	=> 'Уведомления',
		'menu_slug' 	=> 'notice-settings',
		'capability'	=> 'edit_posts',
		'redirect'		=> false
	));
}

// add_filter( 'woocommerce_get_price_html', 'truemisha_display_price', 99, 2 );

// function truemisha_display_price( $price_html, $product ) {

// 	// ничего не делаем в админке
// 	if ( is_admin() ) {
// 		return $price_html;
// 	}

// 	// если цена пустая, тоже забиваем
// 	if ( '' === $product->get_price() ) {
// 		return $price_html;
// 	}

// 	$fabric_price = $product->get_price();
// 	$sale_percent = get_field('priceint', 'option');
// 	$sale_price = $fabric_price - ($fabric_price * ($sale_percent / 100));

// 	$check_ac = array_shift( wc_get_product_terms( $product->id, 'pa_akcziya', array( 'fields' => 'names' ) ) );

// 	// класс, это наш пользователь сайта, ему вешаем скидку 20%
// 	if ( $check_ac and $sale_percent ) {
// 		$price_html = wc_price( wc_get_price_to_display( $product ) + 1 );
// 	}

// 	return $price_html;

// }

//add_filter( 'woocommerce_product_get_price' , 'products_price_with_discount' , 5, 2 );
add_filter( 'woocommerce_product_get_price', 'products_price_with_discount', 40, 2 );
//add_filter( 'woocommerce_product_variation_get_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_product_get_sale_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_product_variation_get_sale_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_variation_prices_price', 'products_price_with_discount', 5, 2 );
//add_filter( 'woocommerce_variation_prices_sale_price', 'products_price_with_discount', 5, 2 );

function products_price_with_discount( $price, $product )
{
    $discount = ferma_get_cached_option_field('priceint');
    $product_id = $product->get_id();

	//$price = $product->get_regular_price();

	//$_product = wc_get_product( $product_id );

	//$attributes = $product->get_attributes();

	$is_action = $product->get_attribute( 'pa_akcziya' );

	$price_date = ferma_get_cached_option_field('pricedate');

	$price_date = date("Y-m-d 23:59:59", strtotime($price_date));

	$end_date = strtotime($price_date);
	$current_date = strtotime(date("Y-m-d H:i:s"));

	$green_friday_discount = ferma_get_green_friday_discount_for_product($product_id);
	if ($green_friday_discount !== null) {
		$is_action = 1;
		$price_date = ferma_get_cached_option_field('zp_date_end');
		$end_date = strtotime($price_date);
		$discount = $green_friday_discount;
	}

	if($end_date > $current_date && $discount > 0 && $is_action == 1) {
		//echo $price . "<br>" . $price - (($price / 100) * $discount);
		return $price - (($price / 100) * $discount);
	} else {
		return $price;
	}

}

function get_green_friday_products()
{
	static $green_friday_products = null;

	if ($green_friday_products !== null) {
		return $green_friday_products;
	}

	$cache_key = 'ferma_green_friday_products';
	$cached = wp_cache_get($cache_key, 'theme');
	if ($cached !== false) {
		$green_friday_products = $cached;
		return $green_friday_products;
	}

	$path = trailingslashit($_SERVER['DOCUMENT_ROOT']) . 'green-friday.json';
	$green_friday_products = array(
		'good_ids' => array(),
		'good_ids_with_discount' => array(),
	);

	if (is_readable($path)) {
		$goods = file_get_contents($path);
		$decoded = json_decode($goods, true);
		if (is_array($decoded)) {
			$green_friday_products = array_merge($green_friday_products, $decoded);
		}
	}

	wp_cache_set($cache_key, $green_friday_products, 'theme', 300);

	return $green_friday_products;
}

function ferma_get_green_friday_discount_for_product($product_id)
{
	static $discount_map = null;

	if ($discount_map === null) {
		$discount_map = array();
		$green_friday_products = get_green_friday_products();
		$product_groups = $green_friday_products['good_ids_with_discount'] ?? array();

		foreach ($product_groups as $percent => $product_ids) {
			foreach ((array) $product_ids as $id) {
				$discount_map[(int) $id] = (int) $percent;
			}
		}
	}

	$product_id = (int) $product_id;

	return $discount_map[$product_id] ?? null;
}

function ferma_get_cached_option_field($field_name)
{
	static $option_cache = array();

	if (!array_key_exists($field_name, $option_cache)) {
		$option_cache[$field_name] = get_field($field_name, 'option');
	}

	return $option_cache[$field_name];
}

function product_is_green_price($product) {
	$product_id = $product->get_id();

	$is_action = $product->get_attribute( 'pa_akcziya' );

	$discount = ferma_get_cached_option_field('priceint');
	$price_date = ferma_get_cached_option_field('pricedate');

	$price_date = date("Y-m-d 23:59:59", strtotime($price_date));

	$end_date = strtotime($price_date);
	$current_date = strtotime(date("Y-m-d H:i:s"));

	$green_friday_discount = ferma_get_green_friday_discount_for_product($product_id);
	if ($green_friday_discount !== null) {
		$is_action = 1;
		$price_date = ferma_get_cached_option_field('zp_date_end');
		$end_date = strtotime($price_date);
		$discount = $green_friday_discount;
	}

	if($end_date > $current_date && $discount > 0 && $is_action == 1) {
		return true;
	}

	return false;
}

//add_filter( 'woocommerce_get_price_html', 'products_price_html_with_discount', 10, 2 );
function products_price_html_with_discount( $price, $product )
{
	//echo $price;
	return $price;
}

/**
 * Кешируемый список ID всех рубрик product_cat (для тяжёлых tax_query на акционных категориях).
 *
 * @return int[]
 */
function ferma_get_all_product_cat_term_ids() {
	static $memo = null;

	if ( $memo !== null ) {
		return $memo;
	}

	$cache_key = 'ferma_all_product_cat_ids_v1';
	$ids         = get_transient( $cache_key );

	if ( false === $ids || ! is_array( $ids ) ) {
		$ids = get_terms(
			array(
				'taxonomy'   => 'product_cat',
				'fields'     => 'ids',
				'hide_empty' => false,
			)
		);
		if ( is_wp_error( $ids ) ) {
			$ids = array();
		}
		set_transient( $cache_key, $ids, HOUR_IN_SECONDS );
	}

	$memo = array_map( 'intval', (array) $ids );

	return $memo;
}

function pre_get_posts_product_actions( $q ) {
	if ( is_admin() || ! $q->is_main_query() ) {
		return;
	}

	if ( ! $q->is_tax( 'product_cat' ) ) {
		return;
	}

	$cat_obj = $q->get_queried_object();
	if ( ! is_a( $cat_obj, 'WP_Term' ) || ! isset( $cat_obj->term_id ) ) {
		return;
	}

	if ( (int) $cat_obj->term_id === 355 ) {
		$price_date = get_field('pricedate', 'option');
		//$price_date = date("Y-m-d 23:59:59", strtotime($price_date));
		$discount = get_field('priceint', 'option');

		$price_date = date("Y-m-d 23:59:59", strtotime($price_date));

		$end_date = strtotime($price_date);
		$current_date = strtotime(date("Y-m-d H:i:s"));

		if($current_date > $end_date || $discount == 0) {
			$q->set( 'cat', '7815' );
		}

		$terms = ferma_get_all_product_cat_term_ids();

		$q->set( 'tax_query', array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'pa_akcziya',
				'field' => 'slug',
				'terms' => array(1),
				'operator' => 'IN',
			),
			array(
				'taxonomy' => 'product_cat',
				'field' => 'id',
				'terms' => $terms,
				'operator' => 'IN'
			)
		));
	}

	if ( (int) $cat_obj->term_id === 2626 ) {
		$zp_date_start = get_field('zp_date_start', 'option');
		$zp_date_end = get_field('zp_date_end', 'option');
		//$price_date = date("Y-m-d 23:59:59", strtotime($price_date));
		$discount = get_field('priceint', 'option');

		$zp_date_start = date("Y-m-d 23:59:59", strtotime($zp_date_start));
		$zp_date_end = date("Y-m-d 23:59:59", strtotime($zp_date_end));

		$current_date = strtotime(date("Y-m-d H:i:s"));

		if($current_date > $zp_date_end || $current_date < $zp_date_start) {
			$q->set( 'cat', '7815' );
		}

		$q->set( 'tax_query', null);

		$green_friday_products = get_green_friday_products();
		$good_ids = $green_friday_products['good_ids'];

		$terms = ferma_get_all_product_cat_term_ids();

		$q->set( 'tax_query', array(
			'relation' => 'OR',
			array(
				'taxonomy' => 'product_cat',
				'field' => 'id',
				'terms' => $terms,
				'operator' => 'IN'
			)
		));
		$q->set( 'post__in', $good_ids );
	}

}
add_action( 'pre_get_posts', 'pre_get_posts_product_actions' );


/*add_filter( 'woocommerce_package_rates', 'custom_shipping_costs', 10, 2 );
function custom_shipping_costs( $rates, $package ) {
	if($_SERVER['REMOTE_ADDR'] == "217.150.75.150") {
		$delivery_price = ferma_get_delivery_price($_COOKIE['coords'], $_COOKIE['delivery_time']);
		foreach($rates as $key => $value) {
			$rates[$key]->cost = $delivery_price;
		}
	}

    return $rates;
}*/
function get_weight_ratio($product_id)
{
    // Если включена разбивка веса – всегда 0.1 кг
    if ( ferma_is_weighted_product( $product_id ) ) {
        return 0.1;
    }

    // Всё остальное – без разбивки (1 кг или шт., в зависимости от логики)
    return 1;
}

if ( ! function_exists( 'ferma_calc_percent' ) ) {
	function ferma_calc_percent( $price, $percent ) {
		return (float) $price * ( (float) $percent / 100 );
	}
}

add_action( 'woocommerce_after_checkout_validation', 'ferma_validate_delivery_address', 10, 2 );
function fdv_format_weight( $kg ) {
    $kg = (float) $kg;

    // нормализуем до 1 знака
    $kg = round( $kg, 1 );

    if ( abs( $kg - round( $kg ) ) < 0.00001 ) {
        return (int) round( $kg ) . ' кг';
    }

    return number_format( $kg, 1, ',', ' ' ) . ' кг';
}

function ferma_validate_delivery_address( $fields, $errors ){
    if ( isset($_COOKIE['delivery']) && $_COOKIE['delivery'] == 0 && (!isset($_COOKIE['coords']) || $_COOKIE['coords'] == '') ) {
        $errors->add( 'validation', 'Введите корректный адрес и выберите время для доставки' );
    }
}

add_action( 'woocommerce_after_checkout_validation', 'ferma_checkout_require_delivery_house_and_flat', 25, 2 );
function ferma_checkout_require_delivery_house_and_flat( $data, $errors ) {
	if ( ! function_exists( 'ferma_is_delivery' ) || ! ferma_is_delivery() ) {
		return;
	}
	$apt = isset( $_POST['billing_dev_1'] ) ? trim( wp_unslash( $_POST['billing_dev_1'] ) ) : '';
	if ( $apt === '' ) {
		$errors->add( 'billing_dev_1', __( 'Укажите номер квартиры или офиса.', 'woocommerce' ) );
	}
	$street = '';
	if ( ! empty( $_POST['billing_delivery'] ) ) {
		$street = (string) wp_unslash( $_POST['billing_delivery'] );
	} elseif ( ! empty( $_POST['billing_address_1'] ) ) {
		$street = (string) wp_unslash( $_POST['billing_address_1'] );
	}
	$street = trim( $street );
	if ( $street !== '' && ! preg_match( '/\d/u', $street ) ) {
		$errors->add( 'billing_delivery', __( 'В адресе доставки укажите номер дома (нужна хотя бы одна цифра).', 'woocommerce' ) );
	}
}

add_action( 'woocommerce_cart_calculate_fees', 'ferma_checkout_clear_bonus_cookies_when_disabled', 1 );
function ferma_checkout_clear_bonus_cookies_when_disabled() {
	if ( ! function_exists( 'ferma_checkout_bonuses_allowed' ) || ferma_checkout_bonuses_allowed() ) {
		return;
	}
	if ( isset( $_COOKIE['balik'] ) ) {
		setcookie( 'balik', '', time() - YEAR_IN_SECONDS, '/' );
		unset( $_COOKIE['balik'] );
	}
	if ( isset( $_COOKIE['vibo1r'] ) ) {
		setcookie( 'vibo1r', '', time() - YEAR_IN_SECONDS, '/' );
		unset( $_COOKIE['vibo1r'] );
	}
}


// add_action( 'woocommerce_after_single_product_summary', 'checkout_show_green_prices', 110 );

function checkout_show_green_prices( ) {
	echo '<div class="order-green-prices">';
	$related_products = wc_get_products(array(
		'limit' => -1,
		'tax_query' => array(
			array(
				'taxonomy' => 'pa_akcziya',
				'field' => 'slug',
				'terms' => array(1),
				'operator' => 'IN',
			),
		),
	));
	if ( $related_products ) : ?>

	<section class="related products">

		<?php
		$heading = "Зелёные ценники";

		if ( $heading ) :
			?>
			<h2><?php echo esc_html( $heading ); ?></h2>
		<?php endif; ?>

		<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $related_products as $related_product ) : ?>

					<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] =& $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
					?>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>
	<?php endif; ?>
</div>
<?php
}
function custom_display_price( $price, $cart_item, $cart_item_key ) {
    if ( empty( $cart_item['data'] ) || ! $cart_item['data'] instanceof WC_Product ) {
        return $price;
    }

    $product    = $cart_item['data'];
    $product_id = $product->get_id();
    $qty        = isset( $cart_item['quantity'] ) ? (float) $cart_item['quantity'] : 1;

    if ( $qty <= 0 ) {
        return $price;
    }

    // Коэффициент веса (для весовых 0.1, для обычных 1)
    $weight_ratio = function_exists( 'get_weight_ratio' ) ? (float) get_weight_ratio( $product_id ) : 1;
    if ( $weight_ratio <= 0 ) {
        $weight_ratio = 1;
    }

    // Базовая старая цена за 1 "единицу" (1 кг / 1 шт)
    $regular_base = (float) $product->get_regular_price();
    if ( $regular_base <= 0 ) {
        return $price; // нечего сравнивать
    }

    $discounted_base = (float) $product->get_price() / $weight_ratio;


    // Если реальной скидки нет – оставляем стандартный вывод
    if ( $discounted_base >= $regular_base - 0.01 ) {
        return $price;
    }

    // Считаем старую и новую сумму за всю позицию
    $old_total = $regular_base    * $weight_ratio * $qty;
    $new_total = $discounted_base * $weight_ratio * $qty;

    $currency = get_woocommerce_currency_symbol();

    return sprintf(
        '<span class="woocommerce-Price-amount amount"><bdi><s>%s</s>&nbsp;%s&nbsp;<span class="woocommerce-Price-currencySymbol">%s</span></bdi></span>',
        wc_format_decimal( $old_total, 0 ),
        wc_format_decimal( $new_total, 0 ),
        esc_html( $currency )
    );
}
add_filter( 'woocommerce_cart_item_subtotal', 'custom_display_price', 10, 3 );

function so_43922864_add_content() {
	global $product;

	$ugl = $product->get_attribute('pa_uglevody-g');
	$jir = $product->get_attribute('pa_жиры-г');
	$belk = $product->get_attribute('pa_белки-г');
	$kal = $product->get_attribute('pa_energeticheskaya-cen');

	if(!empty($ugl) || !empty($jir) || !empty($belk) || !empty($kal)) : ?>

	<div class="shop-ferma__params shop-ferma__params_pc prod-params">

		<div class="shop-ferma__params-title prod-params__title">Пищевая ценность на 100 грамм</div>

		<div class="shop-ferma__params-list prod-params__list">
			<?php if(!empty($belk)) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Белки — </span>
					<?php echo $belk; ?>
				</div>
			<?php endif; ?>

			<?php if(!empty($jir)) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Жиры — </span>
					<?php echo $jir; ?>
				</div>
			<?php endif; ?>

			<?php if(!empty($ugl)) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Углеводы — </span>
					<?php echo $ugl; ?>
				</div>
			<?php endif; ?>

			<?php if(!empty($kal)) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Калории — </span>
					<?php echo $kal; ?>
				</div>
			<?php endif; ?>
		</div>

	</div>

	<?php endif;
}
add_action( 'woocommerce_single_product_summary', 'so_43922864_add_content', 45 );

// Удалил старый фильтр
remove_filter( 'the_content', 'display_attributes_after_product_description', 10 );
// Добавил вывод атрибутов в .summary
add_action( 'woocommerce_single_product_summary', 'custom_display_product_attributes_in_summary', 35 );
function custom_display_product_attributes_in_summary() {
	if ( ! is_product() ) return;

	global $product;

	$country = $product->get_attribute('pa_strana');
	$energy  = $product->get_attribute('pa_energeticheskaya-cen');
	$volume  = $product->get_attribute('pa_obyom-ml');
	$weight  = $product->get_attribute('pa_ves-g');
	$sostav  = $product->get_attribute('pa_sostav');
	$usl     = $product->get_attribute('pa_usloviya-hraneniya');
	$srok    = $product->get_attribute('pa_srok-godnosti');
	$mesto   = $product->get_attribute('pa_mesto-proishojdeniya');

	if (
		!$country && !$energy && !$volume && !$weight &&
		!$sostav && !$usl && !$srok && !$mesto
	) return;

	echo '<div class="shop-ferma__attributes">';

	if ($country) echo '<div class="product-attribute"><span class="product-attribute__text">Страна происхождения: </span>' . esc_html($country) . '</div>';
	if ($mesto)   echo '<div class="product-attribute"><span class="product-attribute__text">Место происхождения: </span>' . esc_html($mesto) . '</div>';
	if ($energy)  echo '<div class="product-attribute"><span class="product-attribute__text">Энергетическая ценность на 100 г, кКал: </span>' . esc_html($energy) . '</div>';
	if ($volume)  echo '<div class="product-attribute"><span class="product-attribute__text">Объём, мл: </span>' . esc_html($volume) . '</div>';
	if ($weight)  echo '<div class="product-attribute"><span class="product-attribute__text">Вес, гр: </span>' . esc_html($weight) . '</div>';
	if ($sostav)  echo '<div class="product-attribute"><span class="product-attribute__text">Состав: </span>' . esc_html($sostav) . '</div>';
	if ($usl)     echo '<div class="product-attribute"><span class="product-attribute__text">Условия хранения: </span>' . esc_html($usl) . '</div>';
	if ($srok)    echo '<div class="product-attribute"><span class="product-attribute__text">Срок годности: </span>' . esc_html($srok) . '</div>';

	echo '</div>';
}


function redirect_child_category() {
    if (is_product_category()) {
        $category = get_queried_object();
        $category_id = $category->term_id;

		$category_link = get_category_link($category_id);

		if($_SERVER['REMOTE_ADDR'] == "217.150.75.124") {

			$current_url = strtok("https://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]", '?');

			if ($category_link !== $current_url && !isset($_SERVER['HTTP_REFERER'])) {
				if (!empty($_SERVER['QUERY_STRING'])) {
					$category_link .= '?' . $_SERVER['QUERY_STRING'];
				}
				wp_redirect($category_link, 301);
				exit;
			}
		}
    }
}
add_action('template_redirect', 'redirect_child_category');

//add_action( 'woocommerce_email', 'ferma_disable_emails' );

function ferma_disable_emails( $email_class ) {
    //remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    //remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
    //remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

	//remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
    //remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

	remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
}

//add_filter( 'pre_option_woocommerce_hide_out_of_stock_items', 'fix_kulichi_category' );
function fix_kulichi_category( $hide ) {
   if ( is_product_category( 'kulichi' )) {
      $hide = 'no';
   }
   return $hide;
}

function ferma_woocommerce_email_recipient( $recipient, $order, $email ) {
    if ( ! $order || ! is_a( $order, 'WC_Order' ) ) return $recipient;
    $recipient = '';
    return $recipient;
}
add_filter( 'woocommerce_email_recipient_customer_on_hold_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_processing_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_pending_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_on-hold_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_completed_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_cancelled_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_refunded_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_failed_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_podtverjden_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_sobran_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otgrujen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_kurer-naznachen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_zakaz-v-puti_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_zakaz-oplachen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_picked-up_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_dostavlen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otmenen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_vozvrat_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otmena-otkaz_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otmena-otkaz2_order', 'ferma_woocommerce_email_recipient', 10, 3 );

function ferma_admin_new_order_recipient( $recipient, $order, $email ) {
	return 'zakaz@ferma-dv.ru';
}
add_filter( 'woocommerce_email_recipient_new_order', 'ferma_admin_new_order_recipient', 10, 3 );

//Moysklad



// CPT "Промокоды"
add_action( 'init', function () {
    register_post_type( 'q_promocode', array(
        'labels' => array(
            'name'          => 'Промокоды Q',
            'singular_name' => 'Промокод Q',
            'add_new'       => 'Добавить промокод',
            'add_new_item'  => 'Добавить промокод',
            'edit_item'     => 'Редактировать промокод',
        ),
        'public'       => false,
        'show_ui'      => true,
        'menu_position'=> 25,
        'menu_icon'    => 'dashicons-tickets-alt',
        'supports'     => array('title'),
    ) );
} );

// Метабоксы
add_action( 'add_meta_boxes', function () {
    add_meta_box(
        'q_promocode_meta',
        'Настройки промокода',
        'q_promocode_meta_box_cb',
        'q_promocode',
        'normal',
        'high'
    );
} );

function q_promocode_meta_box_cb( $post ) {
    $code          = get_post_meta( $post->ID, '_q_code', true );
    $gift_sku      = get_post_meta( $post->ID, '_q_gift_sku', true );
    $discount_type = get_post_meta( $post->ID, '_q_discount_type', true ); // percent|absolute
    $discount_val  = get_post_meta( $post->ID, '_q_discount_val', true );
    $lifetime      = get_post_meta( $post->ID, '_q_lifetime_hours', true );
    $usage_limit   = get_post_meta( $post->ID, '_q_usage_limit', true );
    ?>
        <style>
            .product-card__cart {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .product-card__cart .add_to_cart_button {
                margin-left: auto;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                white-space: nowrap;
            }
            .product-card__cart {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .product-card {
                padding-right: 10px; /* или больше/меньше */
            }
            .add_to_cart_button.shop-ferma__rel-add {
                margin-left: 28px; /* или сколько тебе нужно */
            }
            .product-card__cart .cart__qty {
                margin-right: 32px; /* подбери число под макет */
            }

            /* или, если такого контейнера нет, просто так: */
            .cart__qty {
                margin-right: 20px;
            }
            .product-card__cart .add_to_cart_button {
                margin-left: auto;
                margin-right: 10px; /* сколько нужно – подбери */
                display: inline-flex;
                justify-content: center;
                align-items: center;
                white-space: nowrap;
            }

            /* количество как и раньше */
            .product-card__cart .cart__qty {
                display: inline-flex;
                align-items: center;
                gap: 8px;
            }

        </style>
    <p>
        <label>Код (формат Q123):</label><br>
        <input type="text" name="q_code" value="<?php echo esc_attr( $code ); ?>" style="width:100%;">
    </p>

    <p>
        <label>Артикул товара (SKU подарка):</label><br>
        <input type="text" name="q_gift_sku" value="<?php echo esc_attr( $gift_sku ); ?>" style="width:100%;">
    </p>
    <p>
        <label>Тип скидки:</label><br>
        <select name="q_discount_type">
            <option value="percent"  <?php selected($discount_type,'percent'); ?>>Процент, %</option>
            <option value="absolute" <?php selected($discount_type,'absolute'); ?>>Абсолютная цена (руб.)</option>
        </select>
    </p>
    <p>
        <label>Значение скидки (процент или итоговая цена):</label><br>
        <input type="number" step="0.01" name="q_discount_val" value="<?php echo esc_attr( $discount_val ); ?>">
    </p>
    <p>
        <label>Срок действия, часов:</label><br>
        <input type="number" name="q_lifetime_hours" value="<?php echo esc_attr( $lifetime ); ?>">
    </p>
    <p>
        <label>Макс. применений на 1 пользователя (по телефону):</label><br>
        <input type="number" name="q_usage_limit" value="<?php echo esc_attr( $usage_limit ); ?>">
    </p>
    <?php
}

add_action( 'save_post_q_promocode', function ( $post_id ) {
    if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

    if ( isset($_POST['q_code']) ) {
        $code = strtoupper( trim($_POST['q_code']) );

        // любой код из 1–6 латинских букв/цифр
        if ( preg_match('/^[A-Z0-9]{1,9}$/', $code) ) {
            update_post_meta( $post_id, '_q_code', $code );
        }
    }
    if ( isset($_POST['q_gift_sku']) ) {
        update_post_meta( $post_id, '_q_gift_sku', sanitize_text_field($_POST['q_gift_sku']) );
    }
    if ( isset($_POST['q_discount_type']) ) {
        update_post_meta( $post_id, '_q_discount_type', $_POST['q_discount_type'] === 'absolute' ? 'absolute' : 'percent' );
    }
    if ( isset($_POST['q_discount_val']) ) {
        update_post_meta( $post_id, '_q_discount_val', floatval($_POST['q_discount_val']) );
    }
    if ( isset($_POST['q_lifetime_hours']) ) {
        update_post_meta( $post_id, '_q_lifetime_hours', intval($_POST['q_lifetime_hours']) );
    }
    if ( isset($_POST['q_auto_add_gift']) ) {
        update_post_meta( $post_id, '_q_auto_add_gift', '1' );
    } else {
        delete_post_meta( $post_id, '_q_auto_add_gift' );
    }
    if ( isset($_POST['q_usage_limit']) ) {
        update_post_meta( $post_id, '_q_usage_limit', intval($_POST['q_usage_limit']) );
    }
} );


function q_get_active_promocode() {
    return WC()->session->get( 'q_active_promo' );
}
add_action( 'wp_ajax_check_active_promo', 'handle_check_active_promo' );
add_action( 'wp_ajax_nopriv_check_active_promo', 'handle_check_active_promo' );

function handle_check_active_promo() {
    check_ajax_referer( 'q_promo_nonce', 'nonce' );

    $active_promo = q_get_active_promocode();

    if ( $active_promo ) {
        wp_send_json_success( array(
            'active_promo' => true,
            'promo_code' => $active_promo['code']
        ) );
    } else {
        wp_send_json_success( array(
            'active_promo' => false
        ) );
    }
}
function q_apply_promocode_discount( $promo ) {
    // Сохраняем полную информацию о промокоде в сессии
    WC()->session->set( 'q_active_promo', array(
        'code' => $promo['code'],
        'id' => $promo['id'],
        'discount_type' => $promo['discount_type'],
        'discount_val' => $promo['discount_val'],
        'gift_sku' => $promo['gift_sku']
    ) );

    // Рассчитываем скидку
    $discount_amount = 0;

    if ( $promo['discount_type'] === 'percent' ) {
        $subtotal = WC()->cart->get_subtotal();
        $discount_amount = ( $subtotal * $promo['discount_val'] ) / 100;
    } else {
        $discount_amount = $promo['discount_val'];
    }

    // Применяем скидку как fee (отрицательная плата)
    WC()->cart->add_fee( "Скидка по промокоду {$promo['code']}", -$discount_amount );

    return true;
}
add_action( 'wp_ajax_remove_q_promocode', 'handle_remove_q_promocode' );
add_action( 'wp_ajax_nopriv_remove_q_promocode', 'handle_remove_q_promocode' );

function handle_remove_q_promocode() {
    check_ajax_referer( 'q_promo_nonce', 'nonce' );

    // Удаляем подарки из корзины
    $cart_items = WC()->cart->get_cart();

    foreach ( $cart_items as $cart_item_key => $cart_item ) {
        if ( isset( $cart_item['q_promo_gift'] ) ) {
            WC()->cart->remove_cart_item( $cart_item_key );
        }
    }

    // Удаляем промокод из сессии
    WC()->session->__unset( 'q_active_promo' );

    // Пересчитываем корзину
    WC()->cart->calculate_totals();

    wp_send_json_success( array(
        'message' => 'Промокод удален',
        'fragments' => apply_filters( 'woocommerce_add_to_cart_fragments', array() )
    ) );
}

add_action( 'woocommerce_before_calculate_totals', 'set_zero_price_for_promo_gifts', 1 );

function set_zero_price_for_promo_gifts( $cart ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
        return;
    }

    foreach ( $cart->get_cart() as $cart_item_key => $cart_item ) {
        if ( isset( $cart_item['q_promo_gift'] ) && $cart_item['q_promo_gift'] ) {
            $cart_item['data']->set_price( 0 );
        }
    }
}
add_action( 'woocommerce_checkout_update_order_review', 'ferma_apply_q_promo_from_cookie', 10, 1 );


function ferma_apply_q_promo_from_cookie( $posted_data ) {
    // Берём промокод из cookie
    $code = ! empty( $_COOKIE['ferma_promo_code'] )
        ? sanitize_text_field( wp_unslash( $_COOKIE['ferma_promo_code'] ) )
        : '';

    $active = q_get_active_promocode();

    // 1) Куки НЕТ – промо считаем выключенным, чистим сессию и выходим
    if ( ! $code ) {
        if ( $active ) {
            WC()->session->__unset( 'q_active_promo' );
        }
        return;
    }

    // 2) Кука есть, но этот же код уже активен – ничего не делаем,
    // чтобы не дублировать скидку/подарок
    if ( $active && ! empty( $active['code'] ) && strtoupper( $active['code'] ) === strtoupper( $code ) ) {
        return;
    }

    // 3) Пытаемся применить промо
    $result = q_apply_promocode_with_gift( $code );

    if ( is_wp_error( $result ) ) {
        // Чистим сессию
        WC()->session->__unset( 'q_active_promo' );

        // Чистим куку
        setcookie( 'ferma_promo_code', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN );
        if ( SITECOOKIEPATH !== COOKIEPATH ) {
            setcookie( 'ferma_promo_code', '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN );
        }

        // ВАЖНО: добавляем notice, чтобы Woo вернул его в "messages"
        wc_add_notice( $result->get_error_message(), 'error' );

        return;
    }
}

add_action( 'woocommerce_before_checkout_form', function() {
    // Только на checkout и только не в AJAX
    if ( ! is_checkout() || wp_doing_ajax() ) {
        return;
    }

    // posted_data внутри не используется, можно передать пустую строку
    ferma_apply_q_promo_from_cookie( '' );

    // Пересчёт тоталов после возможного применения промо
    if ( WC()->cart ) {
        WC()->cart->calculate_totals();
        WC()->cart->set_session();
    }
}, 5 );
// Сохраняем активный Q-промокод в мета-поля заказа
add_action( 'woocommerce_checkout_create_order', 'ferma_save_qpromo_to_order', 30, 2 );
function ferma_save_qpromo_to_order( WC_Order $order, $data ) {

    // Берём активный промо из сессии (то, что ты кладёшь в q_active_promo)
    $active = q_get_active_promocode();

    $code = '';
    if ( ! empty( $active['code'] ) ) {
        $code = $active['code'];
    } elseif ( ! empty( $_COOKIE['ferma_promo_code'] ) ) {
        // запасной вариант – из куки
        $code = sanitize_text_field( wp_unslash( $_COOKIE['ferma_promo_code'] ) );
    }

    if ( ! $code ) {
        return;
    }

    $code = strtoupper( trim( $code ) );

    // Чтобы не схватить мусор – только формата твоих промо
    if ( ! preg_match( '/^[A-Z0-9]{1,9}$/', $code ) ) {
        return;
    }

    // Сохраняем в мету заказа, откуда потом берём для МойСклада
    $order->update_meta_data( 'q_promocode', $code );
}

add_action( 'wp_ajax_apply_q_promocode', 'handle_apply_q_promocode' );
add_action( 'wp_ajax_nopriv_apply_q_promocode', 'handle_apply_q_promocode' );

function handle_apply_q_promocode() {
    check_ajax_referer( 'q_promo_nonce', 'nonce' );

    $promo_code = sanitize_text_field( $_POST['promo_code'] );

    $result = q_apply_promocode_with_gift( $promo_code );

    if ( is_wp_error( $result ) ) {

        // Добавляем сообщение в стандартный WooCommerce вывод ошибок
        wc_add_notice( $result->get_error_message(), 'error' );

        // Возвращаем JSON для фронта
        wp_send_json_error( array(
            'message'  => $result->get_error_message(),
            'wc_html'  => wc_print_notices( true ) // HTML всех ошибок WC
        ) );
    } else {
        // ОБНОВЛЯЕМ КОРЗИНУ
        WC()->cart->calculate_totals();
        WC()->cart->set_session();

        // ПОЛУЧАЕМ ФРАГМЕНТЫ КОРЗИНЫ
        $fragments = get_cart_fragments();

        wp_send_json_success( array(
            'message' => 'Промокод применен! Подарок добавлен в корзину.',
            'fragments' => $fragments,
            'cart_contents_count' => WC()->cart->get_cart_contents_count(),
            'cart_total' => WC()->cart->get_cart_total()
        ) );
    }
}
add_action( 'wp_ajax_nopriv_wc_print_errors', 'ferma_wc_print_errors' );
add_action( 'wp_ajax_wc_print_errors', 'ferma_wc_print_errors' );

function ferma_wc_print_errors() {
    wp_send_json_success( array(
        'html' => wc_print_notices( true )
    ) );
}

function get_cart_fragments() {
    $fragments = array();

    // Мини-корзина
    ob_start();
    woocommerce_mini_cart();
    $fragments['div.widget_shopping_cart_content'] = '<div class="widget_shopping_cart_content">' . ob_get_clean() . '</div>';

    // Счетчик товаров (если у вас есть такой элемент)
    $fragments['span.cart-contents-count'] = '<span class="cart-contents-count">' . WC()->cart->get_cart_contents_count() . '</span>';

    // Итоговая сумма (если у вас есть такой элемент)
    $fragments['span.cart-total'] = '<span class="cart-total">' . WC()->cart->get_cart_total() . '</span>';

    // Обновляем всю секцию корзины если есть
    ob_start();
    echo '<div class="cart-update-section">';
    foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
        $_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
        $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

        if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 ) {
            echo '<div class="cart-item">';
            echo $_product->get_name() . ' × ' . $cart_item['quantity'];
            echo '</div>';
        }
    }
    echo '</div>';
    $fragments['div.cart-update-section'] = ob_get_clean();

    return $fragments;
}
add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id, $variation_id) {
    if (isset($cart_item_data['q_promo_gift'])) {
        $cart_item_data['q_promo_gift'] = 1; // лучше 1, чем true
    }
    if (isset($cart_item_data['q_promo_code'])) {
        $cart_item_data['q_promo_code'] = sanitize_text_field($cart_item_data['q_promo_code']);
    }
    return $cart_item_data;
}, 10, 3);
add_filter('woocommerce_get_cart_item_from_session', function ($item, $values) {

    // Woo сохраняет custom cart item data на верхнем уровне,
    // но при некоторых сценариях часть может оказаться в $values['data'].
    // Поэтому проверяем оба варианта.

    if (isset($values['q_promo_gift'])) {
        $item['q_promo_gift'] = $values['q_promo_gift'];
    } elseif (isset($values['data']['q_promo_gift'])) {
        $item['q_promo_gift'] = $values['data']['q_promo_gift'];
    }

    if (isset($values['q_promo_code'])) {
        $item['q_promo_code'] = sanitize_text_field($values['q_promo_code']);
    } elseif (isset($values['data']['q_promo_code'])) {
        $item['q_promo_code'] = sanitize_text_field($values['data']['q_promo_code']);
    }

    if (isset($values['custom_price'])) {
        $item['custom_price'] = (float) $values['custom_price'];
    } elseif (isset($values['data']['custom_price'])) {
        $item['custom_price'] = (float) $values['data']['custom_price'];
    }

    return $item;
}, 20, 2);

function q_reset_promo_after_checkout( $order_id ) {
    // Сбрасываем активный промокод
    WC()->session->__unset( 'q_active_promo' );

    // Убираем cookie промокода, чтобы он не применялся автоматически
    if ( isset($_COOKIE['ferma_promo_code']) ) {
        setcookie('ferma_promo_code', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        if ( SITECOOKIEPATH !== COOKIEPATH ) {
            setcookie('ferma_promo_code', '', time() - 3600, SITECOOKIEPATH, COOKIE_DOMAIN);
        }
    }
}

function q_apply_promocode_with_gift( $promo_code ) {
    $promo = q_get_local_promocode( $promo_code );

    if ( ! $promo ) {
        return new WP_Error(
            'invalid_promo',
            'Промокод с истекшим сроком, попробуйте ввести другой промокод'
        );
    }

    // ПРОВЕРКА ЛИМИТА — ИМЕННО В МОМЕНТ ПРИМЕНЕНИЯ
    if ( ! q_can_use_promo_for_user( $promo ) ) {
        return new WP_Error(
            'usage_limit',
            'Промокод уже использован максимально допустимое количество раз'
        );
    }

    if ( ! empty( $promo['gift_sku'] ) ) {
        $gift_product_id = wc_get_product_id_by_sku( $promo['gift_sku'] );

        if ( ! $gift_product_id ) {
            return new WP_Error( 'gift_error', 'Товар-подарок не найден' );
        }

        // Чистим прошлые подарки
        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
            if ( isset( $cart_item['q_promo_gift'] ) ) {
                WC()->cart->remove_cart_item( $cart_item_key );
            }
        }

        $cart_item_data = array(
            'q_promo_gift' => true,
            'q_promo_code' => $promo_code,
            'custom_price' => 0,
        );

        $added = WC()->cart->add_to_cart( $gift_product_id, 1, 0, array(), $cart_item_data );

        if ( ! $added ) {
            return new WP_Error( 'gift_error', 'Не удалось добавить подарок в корзину' );
        }

        WC()->cart->calculate_totals();
        WC()->cart->set_session();

    }
    $result = q_apply_promocode_discount( $promo );

    if ( ! is_wp_error( $result ) ) {
        // считаем использование промокода
        q_mark_promo_used_for_user( $promo );
    }

    return $result;
}
add_action( 'wp_enqueue_scripts', function() {
    // Всплывашка
    wp_enqueue_script(
        'q-promo-toast',
        get_template_directory_uri() . '/assets/js/q-promo-toast1.js',
        array('jquery'),
        '1.0.0',
        true
    );

    wp_enqueue_script(
        'q-promocodes-js',
        get_template_directory_uri() . '/assets/js/promocodes11.js',
        array('jquery', 'q-promo-toast'),
        filemtime( get_template_directory() . '/assets/js/promocodes11.js' ),
        true
    );



    wp_localize_script( 'q-promocodes-js', 'q_promo_vars', array(
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'q_promo_nonce' )
    ) );
} );

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'product-card-qty',
        get_template_directory_uri() . '/assets/css/product-card-qty.css',
        [],
        '1.0'
    );
});
add_filter( 'woocommerce_cart_item_name', 'ferma_cart_gift_label_under_name', 10, 3 );
function ferma_cart_gift_label_under_name( $name, $cart_item, $cart_item_key ) {

    // Наш подарок по промокоду
    if ( empty( $cart_item['q_promo_gift'] ) ) {
        return $name;
    }

    /** @var WC_Product $product */
    $product = $cart_item['data'];
    $qty     = max( 1, (int) $cart_item['quantity'] );

    // Берём базовую цену (до обнуления)
    $regular_price = (float) $product->get_regular_price();
    if ( $regular_price <= 0 ) {
        $regular_price = (float) $product->get_price();
    }

    // Если вообще нет адекватной цены — только "В подарок"
    if ( $regular_price <= 0 ) {
        return $name . '<div class="ferma-gift-info"><span class="ferma-gift-label">В подарок</span></div>';
    }

    // Старая сумма за всё количество
    $old_line_total = wc_price( $regular_price * $qty );

    // Без sprintf — безопасно
    $gift_html =
        '<div class="ferma-gift-info">
            <span class="ferma-gift-old-price"><del>' . $old_line_total . '</del></span>
            <span class="ferma-gift-label">В подарок</span>
        </div>';

    return $name . $gift_html;
}

add_filter( 'woocommerce_cart_item_subtotal', 'ferma_gift_subtotal_replace', 10, 3 );
function ferma_gift_subtotal_replace( $subtotal, $cart_item, $cart_item_key ) {

    // В чекауте НИЧЕГО не меняем – пусть будет стандартный 0 ₽
    if ( is_checkout() ) {
        return $subtotal;
    }

    // Не подарок — не трогаем
    if ( empty( $cart_item['q_promo_gift'] ) ) {
        return $subtotal;
    }

    /** @var WC_Product $product */
    $product = $cart_item['data'];
    $qty     = max( 1, (int) $cart_item['quantity'] );

    $regular_price = (float) $product->get_regular_price();
    if ( $regular_price <= 0 ) {
        $regular_price = (float) $product->get_price();
    }

    if ( $regular_price <= 0 ) {
        return '<div class="ferma-gift-subtotal">
                    <span class="ferma-gift-label">В подарок</span>
                </div>';
    }

    $old_line_total = wc_price( $regular_price * $qty );

    return sprintf(
        '<div class="ferma-gift-subtotal">
            <span class="ferma-gift-old"><del>%s</del></span>
            <span class="ferma-gift-label">В подарок</span>
        </div>',
        $old_line_total
    );
}
add_filter( 'woocommerce_cart_item_data_to_restore', function( $item_data, $cart_item ) {
    if ( isset( $cart_item['q_promo_gift'] ) ) {
        $item_data['q_promo_gift'] = $cart_item['q_promo_gift'];
    }
    return $item_data;
}, 10, 2 );
add_filter( 'woocommerce_add_cart_item_data', function( $cart_item_data, $product_id, $variation_id ) {
    if ( isset( $cart_item_data['q_promo_gift'] ) ) {
        $cart_item_data['q_promo_gift'] = true;
    }
    return $cart_item_data;
}, 10, 3 );
function q_get_local_promocode( $code ) {
    $code = strtoupper( trim($code) );

    if ( ! preg_match('/^[A-Z0-9]{1,9}$/', $code) ) {
        return false;
    }

    $q = new WP_Query([
        'post_type'      => 'q_promocode',
        'posts_per_page' => 1,
        'meta_query'     => [
            [
                'key'   => '_q_code',
                'value' => $code,
            ],
        ],
        'post_status'    => 'publish',
    ]);

    if ( ! $q->have_posts() ) {
        return false;
    }

    $post = $q->posts[0];
    $id   = $post->ID;

    $promo = [
        'id'            => $id,
        'code'          => get_post_meta($id, '_q_code', true),
        'gift_sku'      => get_post_meta($id, '_q_gift_sku', true),
        'discount_type' => get_post_meta($id, '_q_discount_type', true),
        'discount_val'  => (float) get_post_meta($id, '_q_discount_val', true),
        'lifetime'      => (int) get_post_meta($id, '_q_lifetime_hours', true),
        'usage_limit'   => (int) get_post_meta($id, '_q_usage_limit', true),
        'created'       => get_post_time('U', true, $id),
    ];

    // срок действия
    if ( $promo['lifetime'] > 0 && ( time() - $promo['created'] ) > $promo['lifetime'] * 3600 ) {
        return false;
    }

    // просто записываем телефон, не проверяя лимиты
    $phone = '';
    if ( is_user_logged_in() ) {
        $customer = WC()->customer;
        if ( $customer ) {
            $phone = preg_replace('/\D+/', '', $customer->get_billing_phone() );
        }
    }
    $promo['phone'] = $phone;

    return $promo;
}
function q_mark_promo_used_for_user( array $promo ): void {
    $usage_limit = (int) ( $promo['usage_limit'] ?? 0 );
    if ( $usage_limit <= 0 ) {
        // Лимит не задан — ничего не считаем
        return;
    }

    if ( ! is_user_logged_in() ) {
        // Для гостей сейчас не считаем (как и в q_can_use_promo_for_user)
        return;
    }

    $phone = $promo['phone'] ?? '';
    if ( ! $phone ) {
        // Если нет телефона, не к чему привязаться
        return;
    }

    $user_id  = get_current_user_id();
    $meta_key = '_q_promo_used_' . $promo['id'] . '_' . $phone;
    $used     = (int) get_user_meta( $user_id, $meta_key, true );

    update_user_meta( $user_id, $meta_key, $used + 1 );
}

function q_can_use_promo_for_user( array $promo ): bool {
    $usage_limit = (int) ( $promo['usage_limit'] ?? 0 );
    if ( $usage_limit <= 0 ) {
        // Лимит не задан — без ограничений
        return true;
    }

    if ( ! is_user_logged_in() ) {
        // Если гостей тоже надо считать — дописать отдельную схему, сейчас пропускаем
        return true;
    }

    $phone = $promo['phone'] ?? '';
    if ( ! $phone ) {
        return true;
    }

    $user_id = get_current_user_id();
    $meta_key = '_q_promo_used_' . $promo['id'] . '_' . $phone;
    $used     = (int) get_user_meta( $user_id, $meta_key, true );

    return $used < $usage_limit;
}

add_action( 'wp_ajax_update_cart_qty', 'theme_update_cart_qty' );
add_action( 'wp_ajax_nopriv_update_cart_qty', 'theme_update_cart_qty' );

function theme_update_cart_qty() {
    check_ajax_referer( 'update_cart_qty', 'nonce' );

    $cart_item_key = isset($_POST['cart_item_key'])
        ? wc_clean( wp_unslash( $_POST['cart_item_key'] ) )
        : '';

    $product_id = isset($_POST['product_id'])
        ? absint( $_POST['product_id'] )
        : 0;

    $qty = isset($_POST['qty'])
        ? wc_format_decimal( wp_unslash( $_POST['qty'] ) )
        : 0;

    $qty = (float) $qty;

    if ( ! WC()->cart ) {
        wp_send_json_error( array( 'message' => 'Cart not initialized' ) );
    }

    // --- НОВЫЙ БЛОК ПОИСКА СТРОКИ КОРЗИНЫ ---
    $line_key = '';

    // 1) Если пришёл cart_item_key и он реально есть в корзине — используем его
    if ( $cart_item_key && isset( WC()->cart->cart_contents[ $cart_item_key ] ) ) {
        $line_key = $cart_item_key;
    }
    // 2) Иначе, если пришёл product_id — ищем по product_id в корзине
    elseif ( $product_id ) {
        foreach ( WC()->cart->get_cart() as $key => $item ) {
            if ( (int) $item['product_id'] === (int) $product_id ) {
                $line_key = $key;
                break;
            }
        }
    }

    // 3) Если так и не нашли — отдаем ошибку
    if ( ! $line_key ) {
        wp_send_json_error( array( 'message' => 'Cart item not found' ) );
    }

    // 4) Применяем количество
    if ( $qty <= 0 ) {
        WC()->cart->remove_cart_item( $line_key );
    } else {
        WC()->cart->set_quantity( $line_key, $qty, true );
    }

    // 5) Отдаём фрагменты
    WC_AJAX::get_refreshed_fragments();
    wp_die();
}

add_filter( 'woocommerce_loop_add_to_cart_link', 'ferma_loop_add_to_cart_with_qty', 10, 3 );
function ferma_loop_add_to_cart_with_qty( $button, $product, $args ) {

    if ( is_admin() ) {
        return $button;
    }

    $product_id = $product->get_id();
    $is_weighted = (get_field( "razbivka_vesa", $product_id ) == 'да');

    // Для каталога всегда показываем целые числа
    $display_qty = 1;
    // Реальное количество для корзины
    $cart_qty = $is_weighted ? 0.1 : 1;

    ob_start();
    ?>
    <div class="product-card__cart">
        <div class="cart__qty"
             data-product_id="<?php echo esc_attr( $product_id ); ?>"
             data-is_weighted="<?php echo $is_weighted ? '1' : '0'; ?>"
             data-step="<?php echo esc_attr( $step ); ?>"
             data-current_qty="<?php echo esc_attr( $display_qty ); ?>"
             data-max_qty="<?php echo esc_attr( $product->get_max_purchase_quantity() ); ?>">



            <button type="button"
                    class="cart__qty-btn cart__qty-btn--minus is-disabled"
                    aria-label="<?php esc_attr_e( 'Уменьшить количество', 'woocommerce' ); ?>">
                –
            </button>

            <span class="cart__qty-val">
                <?php echo esc_html( $display_qty ); ?>
            </span>

            <button type="button"
                    class="cart__qty-btn cart__qty-btn--plus"
                    aria-label="<?php esc_attr_e( 'Увеличить количество', 'woocommerce' ); ?>">
                +
            </button>
            <style>
                .cart__qty {
                    display: inline-flex;
                    align-items: center;
                    gap: 8px;
                    margin-right: 15px;
                }

                .cart__qty-val {
                    min-width: 30px;
                    text-align: center;
                    font-weight: 600;
                    font-size: 16px;
                }

                .cart__qty-btn {
                    width: 32px;
                    height: 32px;
                    border-radius: 6px;
                    border: 1px solid #d0d0d0;
                    font-size: 16px;
                    line-height: 1;
                    cursor: pointer;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background: #fff;
                }

                .cart__qty-btn--plus {
                    background: #4fbd01;
                    border-color: #4fbd01;
                    color: #fff;
                    font-weight: bold;
                }

                .cart__qty-btn--minus {
                    color: #444;
                    font-weight: bold;
                }

                .cart__qty-btn.is-disabled {
                    opacity: 0.4;
                    cursor: default;
                    pointer-events: none;
                }

                .product-card__cart {
                    display: flex;
                    align-items: center;
                    justify-content: flex-start;
                }

                .added_to_cart {
                    display: none !important;
                }

                .product-in-cart .add_to_cart_button {
                    background: #cccccc !important;
                    border-color: #cccccc !important;
                    cursor: default;
                }
            </style>
        </div>
        <?php
        $button = update_cart_button_quantity( $button, $cart_qty, $product_id );
        echo $button;
        ?>
    </div>
    <?php

    return ob_get_clean();
}
// Отключили подключение cart-validation.js, чтобы не было 404
// add_action('wp_enqueue_scripts', function() {
//     wp_enqueue_script(
//         'cart-validation',
//         get_template_directory_uri() . '/js/cart-validation.js',
//         array('jquery'),
//         '1.0.0',
//         true
//     );
//
//     wp_localize_script( 'cart-validation', 'theme_qty', array(
//         'ajaxurl' => admin_url( 'admin-ajax.php' ),
//         'nonce'   => wp_create_nonce( 'update_cart_qty' ),
//     ) );
// });


// Добавьте эту новую функцию в functions.php
function update_cart_button_quantity( $button, $quantity, $product_id ) {
    // Обновляем data-quantity атрибут
    if ( preg_match( '/data-quantity=["\']([^"\']*)["\']/', $button ) ) {
        $button = preg_replace(
            '/(data-quantity=["\'])([^"\']*)(["\'])/',
            '$1' . esc_attr( $quantity ) . '$3',
            $button
        );
    } else {
        $button = preg_replace(
            '/<a\s+/',
            '<a data-quantity="' . esc_attr( $quantity ) . '" ',
            $button,
            1
        );
    }

    // Обновляем URL параметр quantity
    $button = preg_replace(
        '/(href=["\'])([^"\']*add-to-cart=' . $product_id . '[^"\']*)(["\'])/',
        '$1$2&quantity=' . $quantity . '$3',
        $button
    );

    return $button;
}


// Отключили подключение delivery-address.js, чтобы не было 404
// add_action( 'wp_enqueue_scripts', function () {
//     wp_enqueue_script(
//         'theme-delivery-address',
//         get_template_directory_uri() . '/assets/js/delivery-address.js',
//         array( 'jquery' ),
//         '1.0',
//         true
//     );
// } );

add_action('wp_enqueue_scripts', function() {
    wp_enqueue_style(
        'catalog-qty',
        get_template_directory_uri() . '/assets/css/catalog-qty.css',
        [],
        '1.0',
        'all'
    );
});

// Отключили дополнительное подключение cart-validation.js, чтобы не было 404
// function enqueue_cart_validation_script() {
//     if (is_product() || is_shop() || is_product_category()) {
//         wp_enqueue_script(
//             'cart-validation',
//             get_template_directory_uri() . '/js/cart-validation.js',
//             array('jquery'),
//             '1.0.0',
//             true
//         );
//     }
// }
// add_action('wp_enqueue_scripts', 'enqueue_cart_validation_script');

add_filter('woocommerce_add_to_cart_validation', 'validate_stock_before_add', 10, 3);

function validate_stock_before_add( $passed, $product_id, $quantity ) {
    // Если уже кто-то завалил валидацию – выходим
    if ( ! $passed ) {
        return false;
    }

    $product = wc_get_product( $product_id );
    if ( ! $product ) {
        return $passed;
    }

    // Весовые товары (твоя разбивка) – вообще не проверяем руками, доверяем Woo
    if ( get_field( 'razbivka_vesa', $product_id ) === 'да' ) {
        return $passed;
    }

    // Стандартная проверка: товар в наличии?
    if ( ! $product->is_in_stock() ) {
        wc_add_notice(
            sprintf( 'Товар "%s" отсутствует на складе', $product->get_name() ),
            'error'
        );
        return false;
    }

    // Для товаров с управлением остатками используем штатную логику Woo
    if ( $product->managing_stock() ) {
        // has_enough_stock сам учитывает null, backorders и т.д.
        if ( ! $product->has_enough_stock( $quantity ) ) {
            $stock_quantity = $product->get_stock_quantity();

            wc_add_notice(
                sprintf(
                    'Недостаточно товара "%s" на складе. Доступно: %s.',
                    $product->get_name(),
                    $stock_quantity !== null ? $stock_quantity : 0
                ),
                'error'
            );
            return false;
        }
    }

    return $passed;
}
// Разрешаем десятичное количество для весовых товаров при добавлении в корзину
add_filter( 'woocommerce_add_to_cart_validation', 'ferma_allow_decimal_qty_for_weighted', 10, 5 );
function ferma_allow_decimal_qty_for_weighted( $passed, $product_id, $quantity, $variation_id = 0, $variations = array() ) {

    // если это НЕ наш весовой товар — не трогаем стандартную валидацию
    if ( get_post_meta( $product_id, '_is_weighted', true ) != '1' ) {
        return $passed;
    }

    // приводим к float и валидируем «по-своему»
    $qty = floatval( $quantity );

    // минимальное количество 0.1, дальше шаг ты уже держишь на фронте
    if ( $qty <= 0 ) {
        return false;
    }

    // здесь можно добавить свою проверку остатка, если нужно
    // if ( $qty > ferma_get_stock_in_kg( $product_id ) ) { ... }

    return true; // не блочим добавление, даже если количество дробное
}


add_action( 'wp_enqueue_scripts', 'ferma_enqueue_catalog_price_script' );
function ferma_enqueue_catalog_price_script() {

    // Если нужно только в каталоге:
    if ( ! ( is_shop() || is_product_category() || is_product_tag() ) ) {
        return;
    }

    wp_enqueue_script(
        'ferma-catalog-price', // handle
        get_template_directory_uri() . '/assets/js/ferma-catalog-price4.js',
        array( 'jquery' ),     // зависимости
        '1.0.0',
        true                   // в футере
    );
}
// Добавляем колонку "Осталось" в список промокодов
add_filter( 'manage_q_promocode_posts_columns', function( $columns ) {
    $new = [];

    // Вставим нашу колонку после заголовка (Title)
    foreach ( $columns as $key => $label ) {
        $new[ $key ] = $label;

        if ( 'title' === $key ) {
            $new['q_promo_time_left'] = 'Осталось';
        }
    }

    return $new;
} );

// Заполняем колонку "Осталось"
add_action( 'manage_q_promocode_posts_custom_column', function( $column, $post_id ) {
    if ( 'q_promo_time_left' !== $column ) {
        return;
    }

    $lifetime_hours = (int) get_post_meta( $post_id, '_q_lifetime_hours', true );

    // Без срока действия
    if ( $lifetime_hours <= 0 ) {
        echo '<span class="q-promo-no-expire">Без срока</span>';
        return;
    }

    // Время создания промо (UTC)
    $created_ts = get_post_time( 'U', true, $post_id );
    $expires_ts = $created_ts + $lifetime_hours * 3600;

    // Текущее время (UTC)
    $now  = current_time( 'timestamp', true );
    $diff = $expires_ts - $now;

    if ( $diff <= 0 ) {
        echo '<span class="q-promo-expired">Истёк</span>';
        return;
    }

    // Рисуем "заглушку" + передаём diff в секундах в data-атрибут
    echo '<span 
            class="q-promo-countdown q-promo-active" 
            data-seconds-left="' . esc_attr( $diff ) . '"
          ></span>';

}, 10, 2 );
// Динамический обратный отсчёт в списке q_promocode
add_action( 'admin_footer-edit.php', function () {
    $screen = get_current_screen();
    if ( ! $screen || $screen->post_type !== 'q_promocode' ) {
        return;
    }
    ?>
    <style>
        .column-q_promo_time_left {
            width: 120px;
        }
        .q-promo-active {
            color: #2e7d32; /* зелёный */
            font-weight: 600;
        }
        .q-promo-expired {
            color: #b71c1c;
            font-weight: 600;
        }
        .q-promo-no-expire {
            color: #555;
        }
    </style>
    <script>
        (function () {
            function formatTime(seconds) {
                if (seconds <= 0) {
                    return 'Истёк';
                }

                var hours = seconds / 3600;

                // Больше или равно часу — показываем в часах с одним знаком после запятой
                if (hours >= 1) {
                    var hStr = hours.toFixed(1).replace('.', ',');
                    return hStr + ' ч';
                }

                // Меньше часа — показываем в минутах
                var mins = Math.floor(seconds / 60);
                if (mins < 1) mins = 1;
                return mins + ' мин';
            }

            function tick() {
                var nodes = document.querySelectorAll('.q-promo-countdown[data-seconds-left]');
                nodes.forEach(function (el) {
                    var sec = parseInt(el.getAttribute('data-seconds-left'), 10);
                    if (isNaN(sec)) {
                        return;
                    }

                    if (sec <= 0) {
                        el.textContent = 'Истёк';
                        el.classList.remove('q-promo-active');
                        el.classList.add('q-promo-expired');
                        el.removeAttribute('data-seconds-left');
                        return;
                    }

                    el.textContent = formatTime(sec);
                    el.setAttribute('data-seconds-left', sec - 1);
                });
            }

            document.addEventListener('DOMContentLoaded', function () {
                // первичный рендер
                tick();
                // тикаем каждую секунду
                setInterval(tick, 1000);
            });
        })();
    </script>
    <?php
});

// Форматирование оставшегося времени: "2 д 3 ч", "5 ч 20 мин", "15 мин"
function q_promocode_format_time_left( int $seconds ): string {
    $days  = floor( $seconds / DAY_IN_SECONDS );
    $hours = floor( ( $seconds % DAY_IN_SECONDS ) / HOUR_IN_SECONDS );
    $mins  = floor( ( $seconds % HOUR_IN_SECONDS ) / MINUTE_IN_SECONDS );

    $parts = [];

    if ( $days > 0 ) {
        $parts[] = $days . ' д';
    }
    if ( $hours > 0 ) {
        $parts[] = $hours . ' ч';
    }
    if ( $days === 0 && $mins > 0 ) {
        // минуты показываем только если дней нет (чтоб не раздувать строку)
        $parts[] = $mins . ' мин';
    }

    if ( empty( $parts ) ) {
        // На всякий случай, если осталось меньше минуты
        return 'меньше 1 мин';
    }

    return implode( ' ', $parts );
}
add_action( 'send_headers', function() {
    if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
        nocache_headers();
    }
} );

function fas_log($msg) {
    // Путь к файлу лога
    $file = ABSPATH . 'wp-content/fasovka.log';

    // Если файла нет – пытаемся создать
    if (!file_exists($file)) {
        // Пробуем создать пустой файл
        @file_put_contents($file, "");
    }

    // Пишем в лог
    @file_put_contents(
        $file,
        date('[d-M-Y H:i:s] ') . $msg . "\n",
        FILE_APPEND | LOCK_EX
    );
}

add_action( 'all', function( $tag ) {
    if (strpos($tag, 'wms') !== false) {
        error_log("WMS_HOOK: " . $tag);
    }
});
add_action('rest_api_init', function () {
    register_rest_route('fdv/v1', '/ms-product/', [
        'methods'  => 'POST',
        'callback' => 'fdv_mysklad_webhook_handler',
        'permission_callback' => '__return_true',
    ]);
});
function fdv_mysklad_webhook_handler($request) {

    $data = $request->get_json_params();

    fas_log("WEBHOOK PRODUCT RECEIVED");

    if (empty($data['id'])) {
        fas_log("нет ID");
        return;
    }

    // WooCommerce product_id здесь есть (WooMS создаёт mapping)
    $product_id = wc_get_product_id_by_sku($data['code']);
    // или по uuid если у тебя есть таблица соответствия

    if (!$product_id) {
        fas_log("не найден product_id для МС товара {$data['code']}");
        return;
    }

    // Вытаскиваем фасовку:
    $fasovka = null;

    if (!empty($data['attributes'])) {
        foreach ($data['attributes'] as $attr) {
            if ($attr['name'] === 'Фасовка') {
                $fasovka = trim($attr['value']);
            }
        }
    }

    if (!$fasovka) {
        fas_log("нет фасовки для product_id=$product_id");
        return;
    }

    // Обновляем ACF:
    update_field('cvet_fasovka', $fasovka, $product_id);

    fas_log("Фасовка обновлена: product_id=$product_id → $fasovka");

    return ['status' => 'ok'];
}

add_filter('wms_attribute_before_update', function($attr, $all, $product_id){

    fas_log("ATTR HOOK: product_id={$product_id}");
    fas_log("ATTR HOOK attr label={$attr['label']} value={$attr['value']}");

    return $attr;

}, 5, 3);

// Маппинг категорий/подкатегорий → базовый вес (аналог weightedList из RN)
$FERMA_WEIGHTED_LIST = [
    [
        'name'  => 'Орехи, сухофрукты, снеки',
        'weight'=> 0.5,
        'array' => [
            [ 'name' => 'Цукаты',              'weight' => 0.5 ],
            [ 'name' => 'Снеки',               'weight' => 0.5 ],
            [ 'name' => 'Семечки и семена',    'weight' => 0.5 ],
            [ 'name' => 'Орехи',               'weight' => 0.5 ],
            [ 'name' => 'Сухофрукты',          'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Чай, травы и дикоросы',
        'array' => [
            [ 'name' => 'Ягоды плоды',         'weight' => 1 ],
        ],
    ],
    [
        'name'  => 'Сладости и десерты',
        'array' => [
            [ 'name' => 'Зефир',               'weight' => 0.5 ],
            [ 'name' => 'Конфеты',             'weight' => 0.5 ],
            [ 'name' => 'Торты',               'weight' => 0.5 ],
            [ 'name' => 'Пирожные и десерты',  'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Домашняя консервация',
        'array' => [
            [ 'name' => 'Соленья бочковые',    'weight' => 1 ],
        ],
    ],
    [
        'name'  => 'Колбасные изделия',
        'weight'=> 0.5,
        'array' => [
            [ 'name' => 'Вареные колбасы',         'weight' => 0.5 ],
            [ 'name' => 'Сосиски и сардельки',     'weight' => 0.5 ],
            [ 'name' => 'Паштеты',                 'weight' => 0.5 ],
            [ 'name' => 'Полукопченые колбасы',    'weight' => 0.5 ],
            [ 'name' => 'Сырокопченые изделия',    'weight' => 0.5 ],
            [ 'name' => 'Варено-копченые изделия', 'weight' => 0.5 ],
            [ 'name' => 'Сыровяленые изделия',     'weight' => 0.5 ],
            [ 'name' => 'Запеченные, жареные изделия', 'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Молочная продукция',
        'array' => [
            [ 'name' => 'Масло сливочное',         'weight' => 0.5 ],
            [ 'name' => 'Сырники, творожные десерты', 'weight' => 1 ],
        ],
    ],
    [
        'name'  => 'Овощи, фрукты, ягоды, грибы',
        'array' => [
            [ 'name' => 'Ягода',                   'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Мясо и рыба',
        'weight'=> 1,
        'array' => [
            [ 'name' => 'Для шашлыка и гриля', 'weight' => 1 ],
            [ 'name' => 'Свинина и говядина', 'weight' => 1 ],
            [ 'name' => 'Мясо кролика',       'weight' => 1 ],
            [ 'name' => 'Рыба и морепродукты','weight' => 1 ],
            [ 'name' => 'Мясо птицы',         'weight' => 1 ],
        ],
    ],
    [
        'name'  => 'Мясные деликатесы',
        'weight'=> 0.5,
        'array' => [
            [ 'name' => 'Рулеты фермерские',          'weight' => 0.5 ],
            [ 'name' => 'Копченые, сырокопченые изделия','weight' => 0.5 ],
            [ 'name' => 'Колбасы фермерские',         'weight' => 0.5 ],
            [ 'name' => 'Холодец, зельц, рулька',     'weight' => 0.5 ],
            [ 'name' => 'Барбекю',                    'weight' => 0.5 ],
            [ 'name' => 'Вареные, варено-копченые изделия','weight' => 0.5 ],
            [ 'name' => 'Ветчина фермерская',         'weight' => 0.5 ],
            [ 'name' => 'Сало',                       'weight' => 0.5 ],
        ],
    ],
    [
        'name'  => 'Домашние и ремесленные сыры',
        'weight'=> 0.3,
        'array' => [
            [ 'name' => 'Молодые и рассольные сыры', 'weight' => 0.3 ],
            [ 'name' => 'Твердые и полутвердые сыры','weight' => 0.3 ],
            [ 'name' => 'Сыры с плесенью',           'weight' => 0.3 ],
            [ 'name' => 'Творожные сыры',            'weight' => 0.3 ],
            [ 'name' => 'Сыры из козьего молока',    'weight' => 0.3 ],
        ],
    ],
    [
        'name'  => 'Полуфабрикаты домашние',
        'weight'=> 1,
        'array' => [
            [ 'name' => 'Блинчики',                       'weight' => 1 ],
            [ 'name' => 'Сырники',                        'weight' => 1 ],
            [ 'name' => 'Готовые блюда',                  'weight' => 1 ],
            [ 'name' => 'Рыбные полуфабрикаты',           'weight' => 1 ],
            [ 'name' => 'Котлеты, биточки',               'weight' => 1 ],
            [ 'name' => 'Рулеты',                         'weight' => 1 ],
            [ 'name' => 'Полуфабрикаты из мяса, мяса птицы','weight' => 1 ],
            [ 'name' => 'Выпечка, лапша',                 'weight' => 1 ],
            [ 'name' => 'Пироги для духовки',             'weight' => 1 ],
            [ 'name' => 'Бульоны замороженные',           'weight' => 1 ],
            [ 'name' => 'Пельмени, манты, хинкали',       'weight' => 1 ],
            [ 'name' => 'Вареники',                       'weight' => 1 ],
            [ 'name' => 'Полуфабрикаты из овощей',        'weight' => 1 ],
            [ 'name' => 'Фарш',                           'weight' => 1 ],
        ],
    ],
];
/**
 * Аналог fillWeighed из мобилки.
 *
 * @param string $pathName  Строка из МойСклад вида "Мясо и рыба/Свинина и говядина"
 * @return array{sign:string, weight:float|null}
 */
function ferma_fill_weighed_from_path( $pathName ) {
    global $FERMA_WEIGHTED_LIST;

    if ( empty( $pathName ) ) {
        return [ 'sign' => 'шт', 'weight' => null ];
    }

    $normalizedPath = mb_strtolower( $pathName, 'UTF-8' );

    foreach ( $FERMA_WEIGHTED_LIST as $category ) {
        $catName = mb_strtolower( $category['name'], 'UTF-8' );

        // Если в pathName есть название категории
        if ( mb_strpos( $normalizedPath, $catName ) !== false ) {

            // Сначала пытаемся найти подкатегорию
            if ( ! empty( $category['array'] ) && is_array( $category['array'] ) ) {
                foreach ( $category['array'] as $sub ) {
                    $subName = mb_strtolower( $sub['name'], 'UTF-8' );
                    if ( mb_strpos( $normalizedPath, $subName ) !== false ) {
                        $w = (float) $sub['weight'];
                        if ( $w === 1.0 ) {
                            return [ 'sign' => 'кг', 'weight' => 1.0 ];
                        }
                        return [ 'sign' => 'г', 'weight' => $w ];
                    }
                }
            }

            // Если подкатегории не совпали, но у самой категории есть weight
            if ( isset( $category['weight'] ) ) {
                $w = (float) $category['weight'];
                if ( $w === 1.0 ) {
                    return [ 'sign' => 'кг', 'weight' => 1.0 ];
                }
                return [ 'sign' => 'г', 'weight' => $w ];
            }
        }
    }

    // Ничего не нашли — считаем штучным
    return [ 'sign' => 'шт', 'weight' => null ];
}

add_filter( 'wms_assortment_ms_array', 'ferma_debug_ms_assortment', 10, 2 );
function ferma_debug_ms_assortment( $ms_array, $product_id ) {
    if ( (int) $product_id === 4497 ) {
        fas_log( 'MS_ASSORTMENT_RAW: ' . print_r( $ms_array, true ) );
    }
    return $ms_array;
}
function ferma_set_razbivka_vesa( int $product_id, string $value ): void {
    $value = ( mb_strtolower(trim($value)) === 'да' ) ? 'да' : 'нет';

    // ACF хранит значение в meta key = razbivka_vesa
    update_post_meta( $product_id, 'razbivka_vesa', $value );
    // и reference key в meta key = _razbivka_vesa
    update_post_meta( $product_id, '_razbivka_vesa', 'field_627cbc0e2d6f3' );

    // сброс кешей
    clean_post_cache( $product_id );
    wp_cache_delete( $product_id, 'post_meta' );

    if ( function_exists('wc_delete_product_transients') ) {
        wc_delete_product_transients( $product_id );
    }
}

// Устанавливаем фасовку на основе штрихкода МойСклад
add_filter( 'wms_assortment_ms_array', 'fas_set_fasovka_from_ms_item', 20, 4 );
function fas_set_fasovka_from_ms_item( $ms_item, $product_id, $data, $ctx ) {
    fas_log( "FAS: intercepted assortment item" );
    fas_log( "FAS: incoming product_id=" . var_export( $product_id, true ) );

    // 1. Резолвим product_id через SKU, если не пришёл
    if ( empty( $product_id ) || ! is_numeric( $product_id ) ) {
        $sku = '';
        if ( ! empty( $ms_item['code'] ) ) {
            $sku = (string) $ms_item['code'];
        }

        if ( $sku !== '' ) {
            $resolved_id = wc_get_product_id_by_sku( $sku );
            fas_log( "FAS: trying to resolve product_id by SKU={$sku}, resolved_id=" . var_export( $resolved_id, true ) );
            if ( $resolved_id ) {
                $product_id = (int) $resolved_id;
            }
        }
    }

    fas_log( "FAS: final product_id=" . var_export( $product_id, true ) );

    if ( empty( $product_id ) || ! is_numeric( $product_id ) ) {
        fas_log( "FAS: product_id не определён — выходим" );
        return $ms_item;
    }

    // 2. Забираем флаги из МойСклад
    $has_ms_weighed = array_key_exists( 'weighed', $ms_item );
    $ms_weighed     = $has_ms_weighed ? (bool) $ms_item['weighed'] : false;
    $pathName       = isset( $ms_item['pathName'] ) ? (string) $ms_item['pathName'] : '';

    fas_log( "FAS: MS weighed flag = " . var_export( $ms_weighed, true ) );
    fas_log( "FAS: MS pathName = " . $pathName );

    // 3. Аналог fillWeighed — только для определения "разбивки"
    $unitInfo = ferma_fill_weighed_from_path( $pathName );
    fas_log( "FAS: unitInfo from path = " . print_r( $unitInfo, true ) );

    // 4. Тип фасовки: ДОВЕРЯЕМ ТОЛЬКО МойСклад
    //    если weighed = true → весовая; weighed = false → штучная
    $is_weight = $ms_weighed;
    fas_log( "FAS: resolved is_weight from MS only = " . var_export( $is_weight, true ) );

    if ( $is_weight ) {

        // Тип фасовки = весовая
        update_post_meta( $product_id, '_ferma_fasovka', 'vesovaya' );
        wp_set_object_terms( $product_id, 'Весовая', 'pa_fasovka', false );

        $razbivka_value = $ms_weighed ? 'да' : 'нет';


        ferma_set_razbivka_vesa( (int)$product_id, $razbivka_value );
        fas_log( "FAS: ACF razbivka_vesa → {$razbivka_value} for product {$product_id}" );

    } else {

        // Тип фасовки = штучная
        update_post_meta( $product_id, '_ferma_fasovka', 'shtuchnaya' );
        wp_set_object_terms( $product_id, 'Штучная', 'pa_fasovka', false );

        // Разбивка веса в штучке не бывает
        ferma_set_razbivka_vesa( (int)$product_id, 'нет' );
        fas_log( "FAS: non-weight product, ACF razbivka_vesa → нет for product {$product_id}" );
    }

    return $ms_item;
}
function ferma_find_gift_line_key_in_cart( int $gift_product_id ): string {
    foreach ( WC()->cart->get_cart() as $key => $item ) {
        $pid = !empty($item['variation_id']) ? (int)$item['variation_id'] : (int)$item['product_id'];

        if ( ! empty($item['q_promo_gift']) && $pid === $gift_product_id ) {
            return $key;
        }
    }
    return '';
}

// Полностью отключаем функционал купонов при самовывозе
add_filter('woocommerce_coupons_enabled', 'disable_coupons_for_pickup');

function disable_coupons_for_pickup($enabled) {
    // Проверяем только на странице чекаута
    if (!function_exists('is_checkout') || !is_checkout()) {
        return $enabled;
    }

    // Получаем тип доставки
    $delivery_type = '';

    // Проверяем авторизованного пользователя
    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $delivery_type = get_user_meta($user_id, 'delivery', true);
    }
    // Проверяем куки для неавторизованных
    elseif (isset($_COOKIE['delivery'])) {
        $delivery_type = $_COOKIE['delivery'];
    }

    // Если самовывоз (значение 1), отключаем купоны
    if ($delivery_type === '1') {
        return false;
    }

    return $enabled;
}

// Удаляем форму купона из чекаута при самовывозе
add_action('woocommerce_before_checkout_form', 'remove_coupon_form_for_pickup', 9);

function remove_coupon_form_for_pickup() {
    // Получаем тип доставки
    $delivery_type = '';

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $delivery_type = get_user_meta($user_id, 'delivery', true);
    } elseif (isset($_COOKIE['delivery'])) {
        $delivery_type = $_COOKIE['delivery'];
    }

    // Если самовывоз, удаляем форму купона
    if ($delivery_type === '1') {
        remove_action('woocommerce_before_checkout_form', 'woocommerce_checkout_coupon_form', 10);
    }
}

// Добавляем CSS для скрытия любых остаточных элементов купона
add_action('wp_head', 'add_coupon_hiding_css');

function add_coupon_hiding_css() {
    // Проверяем только на странице чекаута
    if (!function_exists('is_checkout') || !is_checkout()) {
        return;
    }

    // Получаем тип доставки
    $delivery_type = '';

    if (is_user_logged_in()) {
        $user_id = get_current_user_id();
        $delivery_type = get_user_meta($user_id, 'delivery', true);
    } elseif (isset($_COOKIE['delivery'])) {
        $delivery_type = $_COOKIE['delivery'];
    }

    // Если самовывоз, выводим CSS
    if ($delivery_type === '1') {
        ?>
        <style id="pickup-coupon-hider">
            /* Скрываем ВСЕ возможные элементы купона */
            .woocommerce-form-coupon-toggle,
            .checkout_coupon,
            form.checkout_coupon,
            .coupon,
            .woocommerce-info .showcoupon,
            .woocommerce-message .showcoupon,
            .woocommerce-info a[href*="showcoupon"],
            #coupon_code,
            .woocommerce-info[data-title*="coupon" i],
            .woocommerce-info[data-title*="купон" i],
            .showcoupon {
                display: none !important;
                visibility: hidden !important;
                opacity: 0 !important;
                height: 0 !important;
                width: 0 !important;
                overflow: hidden !important;
                position: absolute !important;
                left: -9999px !important;
                margin: 0 !important;
                padding: 0 !important;
                border: 0 !important;
            }

            /* Скрываем разделитель если есть */
            .checkout_coupon + hr,
            .woocommerce-form-coupon + hr {
                display: none !important;
            }
        </style>
        <?php
    }
}

// Отключаем кэширование для страницы чекаута
add_action('wp', 'disable_caching_for_checkout');

function disable_caching_for_checkout()
{
    if (is_checkout() && !is_wc_endpoint_url()) {
        // Для WP Super Cache
        if (!defined('DONOTCACHEPAGE')) {
            define('DONOTCACHEPAGE', true);
        }

        // Для W3 Total Cache
        if (function_exists('w3tc_pgcache_flush')) {
            add_filter('w3tc_can_cache', '__return_false');
        }

        // Для WP Rocket
        add_filter('rocket_override_donotcachepage', '__return_true');

        // Для LiteSpeed Cache
        if (!defined('LSCACHE_NO_CACHE')) {
            define('LSCACHE_NO_CACHE', true);
        }

        // Заголовки для браузеров и прокси
        nocache_headers();
    }
}

// Добавляем куки в список исключений кэширования
add_filter('rocket_cache_dynamic_cookies', 'add_checkout_cookies_to_cache_exception');
function add_checkout_cookies_to_cache_exception($cookies)
{
    $cookies[] = 'delivery';
    $cookies[] = 'billing_delivery';
    $cookies[] = 'billing_samoviziv';
    $cookies[] = 'coords';
    return $cookies;
}
add_action('wp_enqueue_scripts', function () {
    if ( ! is_checkout() || is_order_received_page() ) {
        return;
    }

    // Определяем самовывоз на сервере (это ключевое)
    $is_pickup = false;

    if ( is_user_logged_in() ) {
        $delivery_type = get_user_meta(get_current_user_id(), 'delivery', true);
        $is_pickup = ((int) $delivery_type === 1);
    } else {
        $is_pickup = (isset($_COOKIE['delivery']) && (int) $_COOKIE['delivery'] === 1);
    }

    wp_enqueue_script(
        'fdv-coupon-toggle',
        get_template_directory_uri() . '/assets/js/ferma-coupon-toggle.js',
        ['jquery'],
        '1.0.0',
        true
    );

    wp_add_inline_script(
        'fdv-coupon-toggle',
        'window.FDV_CHECKOUT = window.FDV_CHECKOUT || {}; window.FDV_CHECKOUT.is_pickup = ' . ($is_pickup ? 'true' : 'false') . ';',
        'before'
    );
}, 20);
remove_all_filters('woocommerce_add_cart_item_data'); // если не можешь — вручную удали один из add_filter

add_filter('woocommerce_add_cart_item_data', function ($cart_item_data, $product_id, $variation_id) {
    if (isset($cart_item_data['q_promo_gift'])) {
        $cart_item_data['q_promo_gift'] = 1;
    }
    if (isset($cart_item_data['q_promo_code'])) {
        $cart_item_data['q_promo_code'] = sanitize_text_field($cart_item_data['q_promo_code']);
    }
    if (isset($cart_item_data['custom_price'])) {
        $cart_item_data['custom_price'] = (float)$cart_item_data['custom_price'];
    }
    return $cart_item_data;
}, 10, 3);


add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/stocks', [
        'methods'  => 'GET',
        'callback' => 'ferma_get_stocks',
        'permission_callback' => '__return_true',
    ]);
    register_rest_route('ferma/v1', '/stock-migration-status', [
        'methods'  => 'GET',
        'callback' => 'ferma_get_stock_migration_status',
        'permission_callback' => '__return_true',
    ]);
});


function ferma_get_stocks(WP_REST_Request $req)
{
    $id = intval($req->get_param('product_id'));
    if (!$id) {
        return new WP_REST_Response(['error' => 'product_id is required'], 400);
    }

    return new WP_REST_Response(ferma_build_stock_payload($id), 200);
}


add_action('rest_api_init', function () {

    register_rest_route('ferma/v1', '/stocks-by-skus', [
        'methods' => 'GET',
        'callback' => 'ferma_get_stocks_by_skus',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/stocks-by-sku', [
        'methods' => 'GET',
        'callback' => 'ferma_get_stocks_by_sku',
        'permission_callback' => '__return_true',
    ]);

});

function ferma_store_meta_map(): array {
    return [
        '028e05a7-b4fa-11ee-0a80-1198000442be' => 'Заря',
        '7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93' => 'Эгершельд',
        'a99d6fdf-0970-11ed-0a80-0ed600075845' => 'Космос',
        'b24e4c35-9609-11eb-0a80-0d0d008550c2' => 'Реми-Сити',
    ];
}

function ferma_get_store_stocks_with_fallback(int $product_id, array $store_ids): array {
    $stocks = [];

    if (class_exists('\Wdc\Addition\Stores\StockTable')) {
        $stocks = \Wdc\Addition\Stores\StockTable::get_product_stocks($product_id, $store_ids);
    }

    foreach ($store_ids as $store_id) {
        if (!array_key_exists($store_id, $stocks)) {
            $stocks[$store_id] = (float) get_post_meta($product_id, $store_id, true);
        } else {
            $stocks[$store_id] = (float) $stocks[$store_id];
        }
    }

    return $stocks;
}

function ferma_build_stock_payload(int $product_id): array {
    $store_meta = ferma_store_meta_map();
    $store_stocks = ferma_get_store_stocks_with_fallback($product_id, array_keys($store_meta));

    $data = [
        'product_id' => $product_id,
        'sku' => (string) get_post_meta($product_id, '_sku', true),
        'manage_stock' => get_post_meta($product_id, '_manage_stock', true) === 'yes',
        'stock_total' => (float) get_post_meta($product_id, '_stock', true),
        'stores' => [],
    ];

    foreach ($store_meta as $meta_key => $title) {
        $data['stores'][] = [
            'id' => $meta_key,
            'name' => $title,
            'stock' => (float) ($store_stocks[$meta_key] ?? 0),
        ];
    }

    return $data;
}

function ferma_get_stock_migration_status(WP_REST_Request $req) {
    if (!class_exists('\Wdc\Addition\Stores\StockTable')) {
        return new WP_REST_Response([
            'enabled' => false,
            'error' => 'StockTable service is not available',
        ], 200);
    }

    return new WP_REST_Response([
        'enabled' => true,
        'status' => \Wdc\Addition\Stores\StockTable::get_diagnostics(),
    ], 200);
}

function ferma_get_stocks_by_sku(WP_REST_Request $req) {
    $sku = (string) $req->get_param('sku');
    $sku = trim($sku);

    if ($sku === '') {
        return new WP_REST_Response(['error' => 'sku is required'], 400);
    }

    $q = new WP_Query([
        'post_type' => ['product', 'product_variation'],
        'post_status' => 'publish',
        'posts_per_page' => 1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_sku',
                'value' => $sku,
                'compare' => '=',
            ]
        ],
    ]);

    if (empty($q->posts)) {
        return new WP_REST_Response([], 200);
    }

    $pid = (int) $q->posts[0];
    return new WP_REST_Response(ferma_build_stock_payload($pid), 200);
}

function ferma_get_stocks_by_skus(WP_REST_Request $req) {
    $raw = $req->get_param('skus');

    // принимает и "a,b,c", и skus[]=a&skus[]=b
    $skus = [];
    if (is_array($raw)) {
        $skus = $raw;
    } else {
        $skus = explode(',', (string)$raw);
    }

    $skus = array_values(array_unique(array_filter(array_map(function ($s) {
        $s = trim((string)$s);
        return $s !== '' ? $s : null;
    }, $skus))));

    if (empty($skus)) {
        return new WP_REST_Response(['error' => 'skus is required'], 400);
    }

    $q = new WP_Query([
        'post_type' => ['product', 'product_variation'],
        'post_status' => 'publish',
        'posts_per_page' => -1,
        'fields' => 'ids',
        'meta_query' => [
            [
                'key' => '_sku',
                'value' => $skus,
                'compare' => 'IN',   // <-- ВОТ ЭТО КЛЮЧЕВО
            ]
        ],
    ]);

    $out = [];
    foreach ($q->posts as $pid) {
        $pid = (int)$pid;
        $sku = (string) get_post_meta($pid, '_sku', true);
        if ($sku === '') continue;
        $out[$sku] = ferma_build_stock_payload($pid);
    }

    return new WP_REST_Response($out, 200);
}


add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/push/register', [
        'methods'  => 'POST',
        'callback' => 'ferma_push_register_device',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/push/test', [
        'methods'  => 'POST',
        'callback' => 'ferma_push_test_send',
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/push/cart-sync', [
        'methods'  => 'POST',
        'callback' => 'ferma_push_cart_sync',
        'permission_callback' => '__return_true',
    ]);
});
function ferma_push_register_device(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_devices';

    if ($wpdb->get_var($wpdb->prepare("SHOW TABLES LIKE %s", $table)) !== $table) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Push devices table not found',
            'table' => $table,
        ], 500);
    }

    $token = trim((string) $request->get_param('token'));
    $platform = trim((string) $request->get_param('platform'));
    $phone = ferma_normalize_phone($request->get_param('phone'));
    $user_id = absint($request->get_param('user_id'));
    $app_version = trim((string) $request->get_param('app_version'));

    if ($token === '' || strlen($token) < 50) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'Invalid token',
        ], 400);
    }

    if ($platform === '') {
        $platform = 'android';
    }

    $now = current_time('mysql');

    $existing = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT id, token FROM {$table} WHERE token = %s LIMIT 1",
            $token
        )
    );

    if (!empty($wpdb->last_error)) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'DB select error',
            'db_error' => $wpdb->last_error,
        ], 500);
    }

    $data = [
        'user_id'      => $user_id ?: null,
        'phone'        => $phone ?: null,
        'platform'     => $platform,
        'app_version'  => $app_version ?: null,
        'is_active'    => 1,
        'last_seen_at' => $now,
        'updated_at'   => $now,
    ];

    if ($existing) {
        $updated = $wpdb->update(
            $table,
            $data,
            ['id' => (int) $existing->id],
            ['%d', '%s', '%s', '%s', '%d', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'DB update error',
                'db_error' => $wpdb->last_error,
            ], 500);
        }

        return new WP_REST_Response([
            'success'   => true,
            'message'   => 'Device updated',
            'device_id' => (int) $existing->id,
        ], 200);
    }

    $inserted = $wpdb->insert(
        $table,
        [
            'user_id'      => $user_id ?: null,
            'phone'        => $phone ?: null,
            'token'        => $token,
            'platform'     => $platform,
            'app_version'  => $app_version ?: null,
            'is_active'    => 1,
            'last_seen_at' => $now,
            'created_at'   => $now,
            'updated_at'   => $now,
        ],
        ['%d', '%s', '%s', '%s', '%s', '%d', '%s', '%s', '%s']
    );

    if ($inserted === false) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'DB insert error',
            'db_error' => $wpdb->last_error,
        ], 500);
    }

    $device_id = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM {$table} WHERE token = %s LIMIT 1",
            $token
        )
    );

    return new WP_REST_Response([
        'success'   => true,
        'message'   => 'Device registered',
        'device_id' => $device_id,
    ], 200);
}


function ferma_fcm_get_access_token() {
    $service_account_path = WP_CONTENT_DIR . '/uploads/ferma/firebase-service-account.json';

    if (!file_exists($service_account_path)) {
        return new WP_Error('missing_service_account', 'Firebase service account file not found');
    }

    $json = json_decode(file_get_contents($service_account_path), true);

    if (
        !$json ||
        empty($json['client_email']) ||
        empty($json['private_key']) ||
        empty($json['project_id'])
    ) {
        return new WP_Error('invalid_service_account', 'Invalid Firebase service account JSON');
    }

    $header = ['alg' => 'RS256', 'typ' => 'JWT'];
    $now = time();

    $claims = [
        'iss'   => $json['client_email'],
        'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'exp'   => $now + 3600,
        'iat'   => $now,
    ];

    $base64UrlEncode = function ($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    };

    $jwt_header = $base64UrlEncode(json_encode($header));
    $jwt_claims = $base64UrlEncode(json_encode($claims));
    $signature_input = $jwt_header . '.' . $jwt_claims;

    $private_key = $json['private_key'];
    $signature = '';

    $ok = openssl_sign($signature_input, $signature, $private_key, 'sha256WithRSAEncryption');
    if (!$ok) {
        return new WP_Error('jwt_sign_error', 'Failed to sign JWT');
    }

    $jwt = $signature_input . '.' . $base64UrlEncode($signature);

    $response = wp_remote_post('https://oauth2.googleapis.com/token', [
        'timeout' => 20,
        'body' => [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ],
    ]);

    if (is_wp_error($response)) {
        return $response;
    }

    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($body['access_token'])) {
        return new WP_Error('access_token_error', 'Failed to get Firebase access token', $body);
    }

    return [
        'access_token' => $body['access_token'],
        'project_id'   => $json['project_id'],
    ];
}

function ferma_send_fcm_push($token, $title, $body, $data = []) {
    $auth = ferma_fcm_get_access_token();

    if (is_wp_error($auth)) {
        return $auth;
    }

    $access_token = $auth['access_token'];
    $project_id = $auth['project_id'];

    $payload = [
        'message' => [
            'token' => $token,
            'notification' => [
                'title' => $title,
                'body'  => $body,
            ],
            'data' => array_map('strval', $data),
            'android' => [
                'priority' => 'high',
            ],
        ],
    ];

    $response = wp_remote_post(
        "https://fcm.googleapis.com/v1/projects/{$project_id}/messages:send",
        [
            'timeout' => 20,
            'headers' => [
                'Authorization' => 'Bearer ' . $access_token,
                'Content-Type'  => 'application/json',
            ],
            'body' => wp_json_encode($payload),
        ]
    );

    if (is_wp_error($response)) {
        return $response;
    }

    $status = wp_remote_retrieve_response_code($response);
    $resp_body = json_decode(wp_remote_retrieve_body($response), true);

    if ($status < 200 || $status >= 300) {
        return new WP_Error('fcm_send_error', 'FCM send failed', [
            'status' => $status,
            'body'   => $resp_body,
        ]);
    }

    return $resp_body;
}

function ferma_push_test_send(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_devices';

    $phone = ferma_normalize_phone($request->get_param('phone'));
    $user_id = absint($request->get_param('user_id'));

    if (!$phone && !$user_id) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'phone or user_id required',
        ], 400);
    }

    if ($user_id) {
        $devices = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE user_id = %d AND is_active = 1",
                $user_id
            )
        );
    } else {
        $devices = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM $table WHERE phone = %s AND is_active = 1",
                $phone
            )
        );
    }

    if (!$devices) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'No active devices found',
        ], 404);
    }

    $results = [];

    foreach ($devices as $device) {
        $result = ferma_send_fcm_push(
            $device->token,
            'Тестовый пуш',
            'Проверка отправки уведомлений',
            [
                'type' => 'test_push',
                'screen' => 'catalog',
            ]
        );

        $results[] = [
            'device_id' => (int) $device->id,
            'phone' => $device->phone,
            'result' => is_wp_error($result)
                ? [
                    'error' => $result->get_error_code(),
                    'message' => $result->get_error_message(),
                    'data' => $result->get_error_data(),
                ]
                : $result,
        ];
    }

    return [
        'success' => true,
        'results' => $results,
    ];
}

function ferma_normalize_phone($phone) {
    $phone = preg_replace('/\D+/', '', (string) $phone);

    if ($phone === '') {
        return '';
    }

    if (strpos($phone, '8') === 0) {
        $phone = '7' . substr($phone, 1);
    }

    if (strpos($phone, '7') !== 0) {
        $phone = '7' . $phone;
    }

    return $phone;
}

function ferma_push_install_table() {
    global $wpdb;

    $charset_collate = $wpdb->get_charset_collate();

    $devices = $wpdb->prefix . 'ferma_push_devices';
    $logs = $wpdb->prefix . 'ferma_push_logs';
    $carts = $wpdb->prefix . 'ferma_push_carts';

    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql_devices = "CREATE TABLE $devices (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        user_id BIGINT UNSIGNED NULL,
        phone VARCHAR(32) NULL,
        token TEXT NOT NULL,
        platform VARCHAR(20) NOT NULL DEFAULT 'android',
        app_version VARCHAR(32) NULL,
        is_active TINYINT(1) NOT NULL DEFAULT 1,
        last_seen_at DATETIME NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY user_id (user_id),
        KEY phone (phone),
        KEY is_active (is_active)
    ) $charset_collate;";

    $sql_logs = "CREATE TABLE $logs (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        phone VARCHAR(32) NULL,
        user_id BIGINT UNSIGNED NULL,
        push_type VARCHAR(64) NOT NULL,
        dedupe_key VARCHAR(191) NULL,
        title VARCHAR(255) NOT NULL,
        body TEXT NOT NULL,
        payload LONGTEXT NULL,
        sent_at DATETIME NOT NULL,
        status VARCHAR(32) NOT NULL DEFAULT 'sent',
        PRIMARY KEY (id),
        KEY phone (phone),
        KEY user_id (user_id),
        KEY push_type (push_type),
        KEY dedupe_key (dedupe_key)
    ) $charset_collate;";

    $sql_carts = "CREATE TABLE $carts (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        phone VARCHAR(32) NULL,
        user_id BIGINT UNSIGNED NULL,
        cart_hash VARCHAR(191) NULL,
        items LONGTEXT NULL,
        updated_at DATETIME NOT NULL,
        PRIMARY KEY (id),
        KEY phone (phone),
        KEY user_id (user_id),
        KEY cart_hash (cart_hash)
    ) $charset_collate;";

    dbDelta($sql_devices);
    dbDelta($sql_logs);
    dbDelta($sql_carts);
}

add_action('init', function () {
    if (get_option('ferma_push_tables_installed') !== '1') {
        ferma_push_install_table();
        update_option('ferma_push_tables_installed', '1');
    }
});
function ferma_push_cart_sync(WP_REST_Request $request) {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_carts';

    $phone = ferma_normalize_phone($request->get_param('phone'));
    $user_id = absint($request->get_param('user_id'));
    $items = $request->get_param('items');

    if (!$phone && !$user_id) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'phone or user_id required',
        ], 400);
    }

    if (!is_array($items)) {
        $items = [];
    }

    $now = current_time('mysql');
    $items_json = wp_json_encode($items);
    $cart_hash = md5($items_json . '|' . $phone . '|' . $user_id);

    $existing = null;

    if ($user_id) {
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE user_id = %d LIMIT 1",
                $user_id
            )
        );
    } elseif ($phone) {
        $existing = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT id FROM {$table} WHERE phone = %s LIMIT 1",
                $phone
            )
        );
    }

    if ($existing) {
        $updated = $wpdb->update(
            $table,
            [
                'phone' => $phone ?: null,
                'user_id' => $user_id ?: null,
                'cart_hash' => $cart_hash,
                'items' => $items_json,
                'updated_at' => $now,
            ],
            ['id' => (int) $existing->id],
            ['%s', '%d', '%s', '%s', '%s'],
            ['%d']
        );

        if ($updated === false) {
            return new WP_REST_Response([
                'success' => false,
                'message' => 'DB update error',
                'db_error' => $wpdb->last_error,
            ], 500);
        }

        return new WP_REST_Response([
            'success' => true,
            'message' => 'Cart updated',
        ], 200);
    }

    $inserted = $wpdb->insert(
        $table,
        [
            'phone' => $phone ?: null,
            'user_id' => $user_id ?: null,
            'cart_hash' => $cart_hash,
            'items' => $items_json,
            'updated_at' => $now,
        ],
        ['%s', '%d', '%s', '%s', '%s']
    );

    if ($inserted === false) {
        return new WP_REST_Response([
            'success' => false,
            'message' => 'DB insert error',
            'db_error' => $wpdb->last_error,
        ], 500);
    }

    return new WP_REST_Response([
        'success' => true,
        'message' => 'Cart saved',
    ], 200);
}
function ferma_send_push_to_phone($phone, $title, $body, $data = [], $push_type = 'generic', $dedupe_key = '') {
    global $wpdb;

    $phone = ferma_normalize_phone($phone);
    if (!$phone) return false;

    if ($dedupe_key && ferma_push_already_sent($dedupe_key)) {
        return 'duplicate_skipped';
    }

    $devices_table = $wpdb->prefix . 'ferma_push_devices';

    $devices = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$devices_table} WHERE phone = %s AND is_active = 1 ORDER BY updated_at DESC",
            $phone
        )
    );

    if (!$devices) {
        return false;
    }

    $ok = false;

    foreach ($devices as $device) {
        $res = ferma_send_fcm_push($device->token, $title, $body, $data);

        if (!is_wp_error($res)) {
            $ok = true;
        }
    }

    if ($ok) {
        ferma_log_push_send($phone, 0, $push_type, $dedupe_key, $title, $body, $data, 'sent');
        return true;
    }

    ferma_log_push_send($phone, 0, $push_type, $dedupe_key, $title, $body, $data, 'failed');
    return false;
}
add_action('woocommerce_order_status_changed', function ($order_id, $old_status, $new_status, $order) {
    if (!$order) return;

    $phone = ferma_normalize_phone($order->get_billing_phone());
    if (!$phone) return;

    $map = [
        'processing'   => ['Новый заказ', 'Ваш заказ принят и передан в обработку.'],
        'on-hold'      => ['Новый заказ', 'Ваш заказ принят и передан в обработку.'],
        'assembled'    => ['Заказ собран', 'Ваш заказ собран и готов к передаче в доставку.'],
        'in-delivery'  => ['Заказ передан в доставку', 'Ваш заказ уже в пути.'],
        'completed'    => ['Заказ доставлен', 'Ваш заказ доставлен. Спасибо за покупку!'],
        'picked-up'    => ['Заказ получен', 'Ваш заказ успешно получен. Спасибо за покупку!'],
    ];

    if (!isset($map[$new_status])) {
        return;
    }

    [$title, $body] = $map[$new_status];

    ferma_send_push_to_phone(
        $phone,
        $title,
        $body,
        [
            'screen'  => 'history',
            'orderId' => (string) $order_id,
            'type'    => 'order_status',
            'status'  => (string) $new_status,
        ],
        'order_status_' . $new_status,
        'order_status:' . $order_id . ':' . $new_status
    );
}, 10, 4);
function ferma_cron_no_first_order_3d() {
    global $wpdb;

    $users = $wpdb->get_results("
        SELECT phone, registered_at
        FROM wp_ferma_app_users
        WHERE registered_at <= DATE_SUB(NOW(), INTERVAL 3 DAY)
    ");

    foreach ($users as $user) {
        $phone = ferma_normalize_phone($user->phone);
        if (!$phone) continue;

        $orders_count = (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM wp_wc_orders_meta_lookup WHERE billing_phone = %s",
                $phone
            )
        );

        if ($orders_count > 0) continue;

        ferma_send_push_to_phone(
            $phone,
            'Вы ещё ничего не заказали',
            'Попробуйте наш каталог — у нас много вкусного и фермерского.',
            [
                'screen' => 'catalog',
                'type' => 'no_first_order_3d',
            ],
            'no_first_order_3d',
            'no_first_order_3d:' . $phone
        );
    }
}
function ferma_cron_abandoned_cart() {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_carts';

    $rows = $wpdb->get_results("
        SELECT *
        FROM {$table}
        WHERE updated_at <= DATE_SUB(NOW(), INTERVAL 1 HOUR)
    ");

    foreach ($rows as $row) {
        $items = json_decode($row->items, true);
        $phone = ferma_normalize_phone($row->phone);

        if (empty($items) || !$phone) {
            continue;
        }

        if (ferma_user_has_any_order($phone)) {
            $latest_order_after_cart = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT order_id
                     FROM {$wpdb->prefix}wc_orders_meta_lookup
                     WHERE billing_phone = %s
                       AND date_created_gmt > %s
                     LIMIT 1",
                    $phone,
                    gmdate('Y-m-d H:i:s', strtotime($row->updated_at))
                )
            );

            if ($latest_order_after_cart) {
                continue;
            }
        }

        $updated_ts = strtotime($row->updated_at);
        if (!$updated_ts) continue;

        $hours_passed = floor((time() - $updated_ts) / 3600);

        if ($hours_passed >= 1) {
            ferma_send_push_to_phone(
                $phone,
                'Товары ждут вас в корзине',
                'Вы добавили товары в корзину. Оформите заказ, пока они в наличии.',
                [
                    'screen' => 'cart',
                    'type'   => 'abandoned_cart_1h',
                ],
                'abandoned_cart_1h',
                'abandoned_cart_1h:' . $phone . ':' . $row->cart_hash
            );
        }

        if ($hours_passed >= 24) {
            ferma_send_push_to_phone(
                $phone,
                'Товар ещё в наличии',
                'Не откладывайте заказ — нужные товары всё ещё ждут вас в корзине.',
                [
                    'screen' => 'cart',
                    'type'   => 'abandoned_cart_24h',
                ],
                'abandoned_cart_24h',
                'abandoned_cart_24h:' . $phone . ':' . $row->cart_hash
            );
        }

        if ($hours_passed >= 72) {
            ferma_send_push_to_phone(
                $phone,
                'Поторопитесь с заказом',
                'Мы сохраним товары в корзине ещё ненадолго. Оформите заказ, пока их не купили.',
                [
                    'screen' => 'cart',
                    'type'   => 'abandoned_cart_72h',
                ],
                'abandoned_cart_72h',
                'abandoned_cart_72h:' . $phone . ':' . $row->cart_hash
            );
        }
    }
}
add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/push/run-abandoned-cart', [
        'methods'  => 'POST',
        'callback' => 'ferma_run_abandoned_cart_now',
        'permission_callback' => '__return_true',
    ]);
});
function ferma_get_user_phone_or_fallback($user_id = 0, $phone = '') {
    $phone = ferma_normalize_phone($phone);
    if ($phone) return $phone;

    if ($user_id > 0) {
        global $wpdb;
        $app_users_table = $wpdb->prefix . 'ferma_app_users';

        $db_phone = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT phone FROM {$app_users_table} WHERE id = %d LIMIT 1",
                $user_id
            )
        );

        return ferma_normalize_phone($db_phone);
    }

    return '';
}

function ferma_order_exists_after_phone($phone, $after_datetime_gmt) {
    global $wpdb;

    $phone = ferma_normalize_phone($phone);
    if (!$phone || !$after_datetime_gmt) return false;

    $exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT order_id
             FROM {$wpdb->prefix}wc_orders_meta_lookup
             WHERE billing_phone = %s
               AND date_created_gmt > %s
             LIMIT 1",
            $phone,
            $after_datetime_gmt
        )
    );

    return !empty($exists);
}

function ferma_user_has_any_order($phone) {
    global $wpdb;

    $phone = ferma_normalize_phone($phone);
    if (!$phone) return false;

    $count = (int) $wpdb->get_var(
        $wpdb->prepare(
            "SELECT COUNT(*)
             FROM {$wpdb->prefix}wc_orders_meta_lookup
             WHERE billing_phone = %s",
            $phone
        )
    );

    return $count > 0;
}
function ferma_run_abandoned_cart_now() {
    ferma_cron_abandoned_cart();

    return new WP_REST_Response([
        'success' => true,
        'message' => 'abandoned cart cron executed',
    ], 200);
}

function ferma_cron_repeat_order_reminders() {
    global $wpdb;

    $phones = $wpdb->get_col("
        SELECT DISTINCT billing_phone
        FROM {$wpdb->prefix}wc_orders_meta_lookup
        WHERE billing_phone IS NOT NULL
          AND billing_phone != ''
    ");

    foreach ($phones as $raw_phone) {
        $phone = ferma_normalize_phone($raw_phone);
        if (!$phone) continue;

        $last_completed_row = $wpdb->get_row(
            $wpdb->prepare(
                "SELECT order_id, date_completed_gmt
                 FROM {$wpdb->prefix}wc_orders_meta_lookup
                 WHERE billing_phone = %s
                   AND status = 'wc-completed'
                   AND date_completed_gmt IS NOT NULL
                 ORDER BY date_completed_gmt DESC
                 LIMIT 1",
                $raw_phone
            )
        );

        if (!$last_completed_row || empty($last_completed_row->date_completed_gmt)) {
            continue;
        }

        $completed_gmt = $last_completed_row->date_completed_gmt;
        $completed_ts = strtotime($completed_gmt);

        if (!$completed_ts) continue;

        $days = floor((time() - $completed_ts) / DAY_IN_SECONDS);

        if ($days >= 10) {
            ferma_send_push_to_phone(
                $phone,
                'Пора пополнить запасы',
                'С последнего заказа прошло 10 дней. Загляните за новыми вкусностями.',
                [
                    'screen' => 'catalog',
                    'type'   => 'repeat_order_10d',
                ],
                'repeat_order_10d',
                'repeat_order_10d:' . $phone . ':' . date('Y-m-d', $completed_ts)
            );
        }

        if ($days >= 20) {
            ferma_send_push_to_phone(
                $phone,
                'Мы соскучились',
                'С последнего заказа прошло 20 дней. Возвращайтесь за любимыми продуктами.',
                [
                    'screen' => 'catalog',
                    'type'   => 'repeat_order_20d',
                ],
                'repeat_order_20d',
                'repeat_order_20d:' . $phone . ':' . date('Y-m-d', $completed_ts)
            );
        }
    }
}
function ferma_cron_birthday_bonus() {
    global $wpdb;

    $bonus_table = $wpdb->prefix . 'ferma_birthday_bonuses';
    $current_year = (int) current_time('Y');
    $today_md = current_time('m-d');

    $users = $wpdb->get_results("
        SELECT phone, birth_date
        FROM {$wpdb->prefix}ferma_app_users
        WHERE birth_date IS NOT NULL
          AND phone IS NOT NULL
          AND phone != ''
    ");

    foreach ($users as $user) {
        $phone = ferma_normalize_phone($user->phone);
        if (!$phone) continue;

        if (date('m-d', strtotime($user->birth_date)) !== $today_md) {
            continue;
        }

        // Проверяем, не начисляли ли уже в этом году
        $already = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT id FROM {$bonus_table} WHERE phone = %s AND year = %d LIMIT 1",
                $phone,
                $current_year
            )
        );
        if ($already) continue;

        // Находим клиента в Kilbil
        $client = ferma_kilbil_find_client($phone);
        if (is_wp_error($client)) {
            error_log('[FERMA BD BONUS] Kilbil find error for ' . $phone . ': ' . $client->get_error_message());
            continue;
        }

        // Начисляем 300 бонусов
        $bonus_amount = 300;
        $result = ferma_kilbil_add_bonus($client['client_id'], $bonus_amount);
        if (is_wp_error($result)) {
            error_log('[FERMA BD BONUS] Kilbil add error for ' . $phone . ': ' . $result->get_error_message());
            continue;
        }

        // Записываем в трекинг-таблицу
        $now = current_time('mysql');
        $expires = date('Y-m-d H:i:s', strtotime($now . ' +3 days'));

        $wpdb->insert($bonus_table, [
            'phone'            => $phone,
            'kilbil_client_id' => $client['client_id'],
            'bonus_amount'     => $bonus_amount,
            'year'             => $current_year,
            'granted_at'       => $now,
            'expires_at'       => $expires,
            'revoked_at'       => null,
            'status'           => 'active',
        ], ['%s', '%s', '%d', '%d', '%s', '%s', '%s', '%s']);

        error_log('[FERMA BD BONUS] Granted ' . $bonus_amount . ' to ' . $phone . ', expires ' . $expires);

        // Отправляем push
        ferma_send_push_to_phone(
            $phone,
            'С днём рождения!',
            'Мы подарили вам ' . $bonus_amount . ' бонусов. Они действуют 3 дня — успейте потратить!',
            ['screen' => 'profile', 'type' => 'birthday_bonus'],
            'birthday_bonus',
            'birthday_bonus:' . $phone . ':' . $current_year
        );
    }
}
if (!wp_next_scheduled('ferma_push_cron_no_first_order_3d')) {
    wp_schedule_event(time(), 'hourly', 'ferma_push_cron_no_first_order_3d');
}
add_action('ferma_push_cron_no_first_order_3d', 'ferma_cron_no_first_order_3d');

if (!wp_next_scheduled('ferma_push_cron_abandoned_cart')) {
    wp_schedule_event(time(), 'hourly', 'ferma_push_cron_abandoned_cart');
}
add_action('ferma_push_cron_abandoned_cart', 'ferma_cron_abandoned_cart');

if (!wp_next_scheduled('ferma_push_cron_repeat_order_reminders')) {
    wp_schedule_event(time(), 'hourly', 'ferma_push_cron_repeat_order_reminders');
}
add_action('ferma_push_cron_repeat_order_reminders', 'ferma_cron_repeat_order_reminders');

if (!wp_next_scheduled('ferma_push_cron_birthday_bonus')) {
    wp_schedule_event(time(), 'daily', 'ferma_push_cron_birthday_bonus');
}
add_action('ferma_push_cron_birthday_bonus', 'ferma_cron_birthday_bonus');

function ferma_log_push_send($phone, $user_id, $push_type, $dedupe_key, $title, $body, $payload, $status = 'sent') {
    global $wpdb;

    $table = $wpdb->prefix . 'ferma_push_logs';

    $wpdb->insert(
        $table,
        [
            'phone' => $phone ?: null,
            'user_id' => $user_id ?: null,
            'push_type' => $push_type,
            'dedupe_key' => $dedupe_key ?: null,
            'title' => $title,
            'body' => $body,
            'payload' => wp_json_encode($payload),
            'sent_at' => current_time('mysql'),
            'status' => $status,
        ],
        ['%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s', '%s']
    );
}

function ferma_push_already_sent($dedupe_key) {
    global $wpdb;

    if (!$dedupe_key) return false;

    $table = $wpdb->prefix . 'ferma_push_logs';

    $exists = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT id FROM {$table} WHERE dedupe_key = %s LIMIT 1",
            $dedupe_key
        )
    );

    return !empty($exists);
}


function ferma_birthday_bonus_install_table() {
    global $wpdb;
    $table = $wpdb->prefix . 'ferma_birthday_bonuses';
    $charset_collate = $wpdb->get_charset_collate();
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';

    $sql = "CREATE TABLE {$table} (
        id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
        phone VARCHAR(32) NOT NULL,
        kilbil_client_id VARCHAR(64) NULL,
        bonus_amount INT NOT NULL DEFAULT 300,
        year SMALLINT NOT NULL,
        granted_at DATETIME NOT NULL,
        expires_at DATETIME NOT NULL,
        revoked_at DATETIME NULL,
        status VARCHAR(20) NOT NULL DEFAULT 'active',
        PRIMARY KEY (id),
        UNIQUE KEY phone_year (phone, year),
        KEY status (status),
        KEY expires_at (expires_at)
    ) {$charset_collate};";

    dbDelta($sql);
}

add_action('init', function () {
    if (get_option('ferma_birthday_bonus_table_installed') !== '2') {
        ferma_birthday_bonus_install_table();
        update_option('ferma_birthday_bonus_table_installed', '2');
    }
});

function ferma_kilbil_find_client($phone) {
    $content = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($content) < 10) {
        return new WP_Error('short_phone', 'Phone too short for Kilbil');
    }

    $response = wp_remote_post('https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7', [
        'timeout' => 15,
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode(['search_mode' => 0, 'search_value' => $content]),
    ]);

    if (is_wp_error($response)) return $response;
    $body = json_decode(wp_remote_retrieve_body($response), true);

    if (empty($body['client_id'])) {
        return new WP_Error('client_not_found', 'Kilbil client not found for ' . $phone);
    }

    return [
        'client_id' => $body['client_id'],
        'balance'   => isset($body['balance']) ? (float) $body['balance'] : 0,
    ];
}

function ferma_kilbil_add_bonus($kilbil_client_id, $amount) {
    $response = wp_remote_post('https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7', [
        'timeout' => 15,
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode(['client_id' => $kilbil_client_id, 'bonus_in' => (float) $amount]),
    ]);
    if (is_wp_error($response)) return $response;
    return json_decode(wp_remote_retrieve_body($response), true) ?: [];
}

function ferma_kilbil_deduct_bonus($kilbil_client_id, $amount) {
    $response = wp_remote_post('https://bonus.kilbil.ru/load/manualadd?h=666c13d171b01d80b04e590794a968b7', [
        'timeout' => 15,
        'headers' => ['Content-Type' => 'application/json'],
        'body'    => wp_json_encode(['client_id' => $kilbil_client_id, 'bonus_out' => (float) $amount]),
    ]);
    if (is_wp_error($response)) return $response;
    return json_decode(wp_remote_retrieve_body($response), true) ?: [];
}

function ferma_cron_birthday_bonus_expire() {
    global $wpdb;
    $bonus_table = $wpdb->prefix . 'ferma_birthday_bonuses';
    $now = current_time('mysql');

    $expired_rows = $wpdb->get_results(
        $wpdb->prepare(
            "SELECT * FROM {$bonus_table} WHERE status = 'active' AND expires_at <= %s",
            $now
        )
    );

    foreach ($expired_rows as $row) {
        $kilbil_client_id = $row->kilbil_client_id;

        if (empty($kilbil_client_id)) {
            $client = ferma_kilbil_find_client($row->phone);
            if (is_wp_error($client)) continue;
            $kilbil_client_id = $client['client_id'];
        }

        // Проверяем баланс — списываем min(начислено, текущий баланс)
        $client_info = ferma_kilbil_find_client($row->phone);
        $current_balance = !is_wp_error($client_info) ? (float) $client_info['balance'] : 0;
        $deduct_amount = min((int) $row->bonus_amount, $current_balance);

        if ($deduct_amount > 0) {
            $result = ferma_kilbil_deduct_bonus($kilbil_client_id, $deduct_amount);
            if (is_wp_error($result)) continue;
            error_log('[FERMA BD EXPIRE] Deducted ' . $deduct_amount . ' from ' . $row->phone);
        }

        $wpdb->update(
            $bonus_table,
            ['status' => 'revoked', 'revoked_at' => current_time('mysql')],
            ['id' => (int) $row->id],
            ['%s', '%s'],
            ['%d']
        );

        ferma_send_push_to_phone(
            $row->phone,
            'Бонусы истекли',
            'Срок действия бонусов на день рождения закончился.',
            ['screen' => 'profile', 'type' => 'birthday_bonus_expired'],
            'birthday_bonus_expired',
            'birthday_bonus_expired:' . $row->phone . ':' . $row->year
        );
    }
}

if (!wp_next_scheduled('ferma_push_cron_birthday_bonus_expire')) {
    wp_schedule_event(time(), 'hourly', 'ferma_push_cron_birthday_bonus_expire');
}
add_action('ferma_push_cron_birthday_bonus_expire', 'ferma_cron_birthday_bonus_expire');


add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/push/run-birthday-bonus', [
        'methods'  => 'POST',
        'callback' => function () {
            ferma_cron_birthday_bonus();
            return new WP_REST_Response(['success' => true, 'message' => 'Birthday bonus cron executed'], 200);
        },
        'permission_callback' => '__return_true',
    ]);

    register_rest_route('ferma/v1', '/push/run-birthday-expire', [
        'methods'  => 'POST',
        'callback' => function () {
            ferma_cron_birthday_bonus_expire();
            return new WP_REST_Response(['success' => true, 'message' => 'Birthday expire cron executed'], 200);
        },
        'permission_callback' => '__return_true',
    ]);
});

add_action('rest_api_init', function () {
    register_rest_route('ferma/v1', '/profile/update', [
        'methods'  => 'POST',
        'callback' => 'ferma_profile_update',
        'permission_callback' => '__return_true',
    ]);
});

function ferma_profile_update(WP_REST_Request $request) {
    global $wpdb;

    $phone = ferma_normalize_phone($request->get_param('phone'));
    if (!$phone) {
        return new WP_REST_Response(['success' => false, 'message' => 'phone required'], 400);
    }

    $table = $wpdb->prefix . 'ferma_app_users';
    $update = [];
    $formats = [];

    $name = trim((string) $request->get_param('full_name'));
    if ($name !== '') {
        $update['full_name'] = $name;
        $formats[] = '%s';
    }

    $birth = trim((string) $request->get_param('birth_date'));
    if ($birth !== '' && preg_match('/^\d{2}\.\d{2}\.\d{4}$/', $birth)) {
        // Конвертируем ДД.ММ.ГГГГ → YYYY-MM-DD
        $parts = explode('.', $birth);
        $update['birth_date'] = $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        $formats[] = '%s';
    }

    if (empty($update)) {
        return new WP_REST_Response(['success' => false, 'message' => 'nothing to update'], 400);
    }

    $exists = $wpdb->get_var(
        $wpdb->prepare("SELECT id FROM {$table} WHERE phone = %s LIMIT 1", $phone)
    );

    if ($exists) {
        $wpdb->update($table, $update, ['phone' => $phone], $formats, ['%s']);
    } else {
        $update['phone'] = $phone;
        $formats[] = '%s';
        $update['registered_at'] = current_time('mysql');
        $formats[] = '%s';
        $wpdb->insert($table, $update, $formats);
    }

    return new WP_REST_Response(['success' => true, 'message' => 'Profile updated'], 200);
}
add_action('wp_head', function() {
    if (function_exists('is_account_page') && is_account_page()) {
        echo '<style>
            .bwide-desktop,
            .mslider,
            section.selection {
                display: none !important;
            }
        </style>';
    }
});
// Скрываем ссылку "Перейти в магазин" из админ-панели
add_action('admin_bar_menu', 'remove_shop_link_from_admin_bar', 999);
function remove_shop_link_from_admin_bar($wp_admin_bar) {
    $wp_admin_bar->remove_node('view-store');
    $wp_admin_bar->remove_node('view-shop');
}
