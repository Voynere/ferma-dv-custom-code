<?php
/**
 * Single Product stock.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/stock.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

$ugl = $product->get_attribute('pa_uglevody-g');
$jir = $product->get_attribute('pa_жиры-г');
$belk = $product->get_attribute('pa_белки-г');
?>
<p class="stock <?php echo esc_attr( $class ); ?>"><?php echo wp_kses_post( $availability ); ?></p>

<?php if(!empty($ugl) || !empty($jir) || !empty($belk)) : ?>

<div class="prod-params">

<div class="prod-params__title">Пищевая ценность на 100 грамм</div>

<div class="prod-params__list">
	<?php if(!empty($ugl)) : ?>
		<div class="prod-params__item">
			<span>Углеводы</span>
			<?php echo $ugl; ?>
		</div>
	<?php endif; ?>
	
	<?php if(!empty($jir)) : ?>
		<div class="prod-params__item">
			<span>Жиры</span>
			<?php echo $jir; ?>
		</div>
	<?php endif; ?>
	
	<?php if(!empty($belk)) : ?>
		<div class="prod-params__item">
			<span>Белки</span>
			<?php echo $belk; ?>
		</div>
	<?php endif; ?>
</div>

</div>

<?php endif; ?>