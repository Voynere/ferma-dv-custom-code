<?php
/**
 * Green prices related product list output.
 *
 * @package Theme
 */

// add_action( 'woocommerce_after_single_product_summary', 'checkout_show_green_prices', 110 );

function checkout_show_green_prices() {
	echo '<div class="order-green-prices">';
	$related_products = wc_get_products(
		array(
			'limit'     => -1,
			'tax_query' => array(
				array(
					'taxonomy' => 'pa_akcziya',
					'field'    => 'slug',
					'terms'    => array( 1 ),
					'operator' => 'IN',
				),
			),
		)
	);
	if ( $related_products ) :
		?>
	<section class="related products">

		<?php
		$heading = 'Зелёные ценники';

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
