<?php
/**
 * Product
 *
 * This template can be overridden by copying it to yourtheme/templates/side-cart-woocommerce/global/body/product.php.
 *
 * HOWEVER, on occasion we will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen.
 * @see     https://docs.xootix.com/side-cart-woocommerce/
 * @version 2.2
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$productClasses = apply_filters( 'xoo_wsc_product_class', $productClasses );

?>

<div data-key="<?php echo $cart_item_key ?>" class="<?php echo implode( ' ', $productClasses ) ?>">

	<?php do_action( 'xoo_wsc_product_start', $_product, $cart_item_key ); ?>

		<div class="xoo-wsc-img-col">

			<?php if( $showPimage ): ?>

				<?php echo $thumbnail; ?>

			<?php endif; ?>

			<?php if( $showPdel && $deletePosition === 'image' ): ?>

				<?php if( $deleteType === 'icon' ): ?>
					<span class="xoo-wsc-smr-del <?php echo $delete_icon ?>"></span>
				<?php else: ?>
					<span class="xoo-wsc-smr-del xoo-wsc-del-txt"><?php echo $deleteText ?></span>
				<?php endif; ?>

			<?php endif; ?>


			<?php do_action( 'xoo_wsc_product_image_col', $_product, $cart_item_key ); ?>

		</div>


	<div class="xoo-wsc-sum-col">

		<?php do_action( 'xoo_wsc_product_summary_col_start', $_product, $cart_item_key ); ?>

		<div class="xoo-wsc-sm-info">

			<div class="xoo-wsc-sm-left">

				<?php if( $showPname ): ?>
					<span class="xoo-wsc-pname"><?php echo $product_name; ?></span>
				<?php endif; ?>
				
				<?php if($cart_item['is_complect']): ?>
					<span style="font-size: 12px;font-weight:bold">ВЫГОДНЫЙ КОМПЛЕКТ</span>
					<?php
					$complect_second_product_id = get_field('complect_product_second', $cart_item['data']->get_id());
					$complect_third_product_id = get_field('complect_product_third', $cart_item['data']->get_id());
					
					$complect_second_product = wc_get_product( $complect_second_product_id );
					$complect_third_product = wc_get_product( $complect_third_product_id );
					?>
					<span style="font-size: 12px;">+ <?php echo $complect_second_product->get_title(); ?></span>
					<span style="font-size: 12px;">+ <?php echo $complect_third_product->get_title(); ?></span>
				<?php endif; ?>
				
				<?php if( $showPmeta ) echo $product_meta ?>

				<?php if( $showPprice && ( $qtyPriceDisplay === 'separate' ) ): ?>
					<div class="xoo-wsc-pprice">
						<?php echo __( 'Price: ', 'side-cart-woocommerce' ) . $product_price ?>
					</div>
				<?php endif; ?>

				<!-- Quantity -->

				<div class="xoo-wsc-qty-price">

					<?php if( $showPprice && $qtyPriceDisplay === 'one_liner' ): ?>
						<?
							/*<span><?php echo $cart_item['quantity']; ?></span> X <span class="old-price"><?php echo $product_price; ?> </span> <span><?php echo round($current_price); ?> <span class="woocommerce-Price-currencySymbol">₽</span></span>*/
						?>
						<?php if(1==2) { ?>
						<span><?php echo $cart_item['quantity']; ?></span> X <span class="old-price"><?php echo $real_price; ?> ₽</span> <span><?php echo $product_price; ?></span>
						<?php } else { ?>
						<span><?php echo $cart_item['quantity']; ?></span> X <span><?php echo $product_price; ?></span>
						<?php } ?>
						<?php if( $showPtotal ): ?>
							<span> = 
							<?php //echo $product_subtotal; ?>
								<?php if($real_price != $current_price) : ?>
								<span class="woocommerce-Price-amount amount">
									<bdi>
										<s><?php echo $cart_item['quantity']*$real_price; ?></s>&nbsp;<?php echo floor($cart_item['quantity']*$current_price); ?>&nbsp;<span class="woocommerce-Price-currencySymbol">₽</span>
									</bdi>
								</span>
								<?php else : ?>
									<?php echo $product_subtotal; ?>
								<?php endif; ?>
							</span>
						<?php endif; ?>

					<?php else: ?>
						<span><?php _e( 'Qty:', 'side-cart-woocommerce' ) ?></span> <span><?php echo $cart_item['quantity']; ?></span>
					<?php endif; ?>

				</div>

			</div>

			<!-- End Quantity -->

		

			<div class="xoo-wsc-sm-right">

				<?php if( $showPdel && $deletePosition === 'default' ): ?>

					<?php if( $deleteType === 'icon' ): ?>
						<span class="xoo-wsc-smr-del <?php echo $delete_icon ?>"></span>
					<?php else: ?>
						<span class="xoo-wsc-smr-del xoo-wsc-del-txt"><?php echo $deleteText ?></span>
					<?php endif; ?>

				<?php endif; ?>


				<?php if( $showPtotal && ( $qtyPriceDisplay === 'separate' ) ): ?>
					<span class="xoo-wsc-smr-ptotal"><?php echo $product_subtotal ?></span>
				<?php endif; ?>

			</div>

		</div>

		<?php do_action( 'xoo_wsc_product_summary_col_end', $_product, $cart_item_key ); ?>

	</div>

	<?php do_action( 'xoo_wsc_product_end', $_product, $cart_item_key ); ?>

</div>