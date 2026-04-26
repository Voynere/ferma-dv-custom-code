<?php
/**
 * Single product nutrition attributes output.
 *
 * @package Theme
 */

function so_43922864_add_content() {
	global $product;

	$ugl  = $product->get_attribute( 'pa_uglevody-g' );
	$jir  = $product->get_attribute( 'pa_жиры-г' );
	$belk = $product->get_attribute( 'pa_белки-г' );
	$kal  = $product->get_attribute( 'pa_energeticheskaya-cen' );

	if ( ! empty( $ugl ) || ! empty( $jir ) || ! empty( $belk ) || ! empty( $kal ) ) :
		?>

	<div class="shop-ferma__params shop-ferma__params_pc prod-params">

		<div class="shop-ferma__params-title prod-params__title">Пищевая ценность на 100 грамм</div>

		<div class="shop-ferma__params-list prod-params__list">
			<?php if ( ! empty( $belk ) ) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Белки — </span>
					<?php echo $belk; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $jir ) ) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Жиры — </span>
					<?php echo $jir; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $ugl ) ) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Углеводы — </span>
					<?php echo $ugl; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $kal ) ) : ?>
				<div class="shop-ferma__params-item prod-params__item">
					<span>Калории — </span>
					<?php echo $kal; ?>
				</div>
			<?php endif; ?>
		</div>

	</div>

	<?php
	endif;
}
add_action( 'woocommerce_single_product_summary', 'so_43922864_add_content', 45 );
