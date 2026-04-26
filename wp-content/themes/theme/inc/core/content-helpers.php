<?php
/**
 * Theme content-level helper functions.
 *
 * @package Theme
 */

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
