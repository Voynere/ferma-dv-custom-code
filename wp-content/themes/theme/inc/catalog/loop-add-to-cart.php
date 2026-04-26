<?php
/**
 * Loop add-to-cart quantity UI customization.
 *
 * @package Theme
 */

add_filter( 'woocommerce_loop_add_to_cart_link', 'ferma_loop_add_to_cart_with_qty', 10, 3 );
function ferma_loop_add_to_cart_with_qty( $button, $product, $args ) {
	unset( $args );

	if ( is_admin() ) {
		return $button;
	}

	$product_id  = $product->get_id();
	$is_weighted = ( get_field( 'razbivka_vesa', $product_id ) == 'да' );

	// Для каталога всегда показываем целые числа.
	$display_qty = 1;
	// Реальное количество для корзины.
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
