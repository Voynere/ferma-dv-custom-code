<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}

echo wc_get_stock_html( $product ); // WPCS: XSS ok.


$is_possible = ferma_product_is_available($product->get_id());

if ( $product->is_in_stock() ) : ?>

	<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>
	
	<?php if(product_is_green_price($product) && !ferma_is_delivery()) { ?>
		<div >Товары по акции *Зелёные ценники* доступны для заказа только на доставку, пожалуйста, поменяйте способ получения продуктов.</div>
	<?php } else if(!$is_possible) { ?>
	
		<div>Нет в наличии в выбранном магазине</div>

	<?php } else { ?>

		<?php
			global $product;
			
			$ugl = $product->get_attribute('pa_uglevody-g');
			$jir = $product->get_attribute('pa_жиры-г');
			$belk = $product->get_attribute('pa_белки-г');
			$kal = $product->get_attribute('pa_energeticheskaya-cen');
			
			if(!empty($ugl) || !empty($jir) || !empty($belk) || !empty($kal)) : ?>

			<div class="shop-ferma__greyButtons-table-simple-container">
				<div class="shop-ferma__params_mobile">
					<div class="shop-ferma__params prod-params">
	
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
				</div>
				<div class="shop-ferma__greyButtons-table-simple">
					<button class="shop-ferma__greyButtons-btn shop-ferma__greyButtons-wish">
						<p>В избранное</p>
					</button>
					<button class="shop-ferma__greyButtons-btn shop-ferma__greyButtons-review">
						<p>Читать отзывы</p>
						<img src="<?php bloginfo('template_url') ?>/assets/img/Star.svg" alt="Отзывы">
						<div>
							<p>4.9</p>
							<p>(129)</p>
						</div>
					</button>
					<button class="shop-ferma__greyButtons-btn shop-ferma__greyButtons-share" onclick="shareProduct()">
						<img src="<?php bloginfo('template_url') ?>/assets/img/share.svg" alt="Поделиться">
						<p>Поделиться</p>
					</button>
				</div>
			</div>

		<?php endif; ?>

		<div class="shop-ferma__cartContainer">
            <form class="cart-old shop-ferma__cart ajax-add-to-cart"
                  action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>"
                  method="post"
                  enctype='multipart/form-data'
                  data-product_id="<?php echo esc_attr( $product->get_id() ); ?>">

                <div class="shop-ferma__cart-container">

                    <div class="shop-ferma__cart-info">
                        <p class="<?php echo esc_attr( apply_filters( 'woocommerce_product_price_class', 'price' ) ); ?>">
                            <?php echo $product->get_price_html(); ?>
                        </p>
                        <?php
                        if ( $product->is_in_stock() ) {
                            echo '<p class="stock in-stock">В наличии</p>';
                        } else {
                            echo '<p class="stock out-of-stock">Нет в наличии</p>';
                        }
                        ?>
                    </div>

                    <?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>

                    <div class="shop-ferma__cart-add-container">

                        <div class="cart__qty cart__qty--single">
                            <button type="button"
                                    class="cart__qty-btn cart__qty-btn--minus"
                                    aria-label="Уменьшить количество">
                                –
                            </button>

                            <?php
                            woocommerce_quantity_input(
                                array(
                                    'min_value'   => apply_filters(
                                        'woocommerce_quantity_input_min',
                                        $product->get_min_purchase_quantity(),
                                        $product
                                    ),
                                    'max_value'   => apply_filters(
                                        'woocommerce_quantity_input_max',
                                        $product->get_max_purchase_quantity(),
                                        $product
                                    ),
                                    'input_value' => isset( $_POST['quantity'] )
                                        ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) )
                                        : $product->get_min_purchase_quantity(),
                                )
                            );
                            ?>

                            <button type="button"
                                    class="cart__qty-btn cart__qty-btn--plus"
                                    aria-label="Увеличить количество">
                                +
                            </button>
                        </div>

                        <button type="submit"
                                name="add-to-cart"
                                value="<?php echo esc_attr( $product->get_id() ); ?>"
                                data-product_id="<?php echo esc_attr( $product->get_id() ); ?>"
                                data-quantity="<?php echo esc_attr( $product->get_min_purchase_quantity() ); ?>"
                                data-product_sku="<?php echo esc_attr( $sku ); ?>"
                                class="single_add_to_cart_button shop-ferma__cart-add ajax_add_to_cart add_to_cart_button">
                            <?php echo esc_html( $product->single_add_to_cart_text() ); ?>
                        </button>


                        <div class="shop-ferma__scales">
                            <img src="<?php bloginfo('template_url') ?>/assets/img/scales.svg" alt="весы">
                            <p>Весовой товар, цена может варьироваться</p>
                        </div>
                    </div>

                    <?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

                </div>

            </form>

            <div class="shop-ferma__greyButtons">
				<div class="shop-ferma__greyButtons-container">
					<button class="shop-ferma__greyButtons-btn shop-ferma__greyButtons-wish">
						<p>В избранное</p>
					</button>
					<button class="shop-ferma__greyButtons-btn shop-ferma__greyButtons-share" onclick="shareProduct()">
						<img src="<?php bloginfo('template_url') ?>/assets/img/share.svg" alt="Поделиться">
						<p>Поделиться</p>
					</button>
				</div>
				<button class="shop-ferma__greyButtons-btn shop-ferma__greyButtons-review">
					<p>Читать отзывы</p>
					<img src="<?php bloginfo('template_url') ?>/assets/img/Star.svg" alt="Отзывы">
					<div>
						<p>4.9</p>
						<p>(129)</p>
					</div>
				</button>
			</div>
		</div>
	<?php } ?>
    <script>
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('.cart__qty-btn');
            if (!btn) return;

            const cart = btn.closest('form.cart-old');
            if (!cart) return;

            const input = cart.querySelector('input.qty');
            if (!input) return;

            const min = parseFloat(input.min) || 1;
            const max = parseFloat(input.max) || Infinity;
            let val = parseFloat(input.value) || min;

            if (btn.classList.contains('cart__qty-btn--plus')) {
                val++;
                if (val > max) val = max;
            } else if (btn.classList.contains('cart__qty-btn--minus')) {
                val--;
                if (val < min) val = min;
            }

            input.value = val;
            input.dispatchEvent(new Event('change', { bubbles: true }));

            const addBtn = cart.querySelector('.single_add_to_cart_button');
            if (addBtn) {
                addBtn.setAttribute('data-quantity', val);
            }
        });

        // подстраховка на случай, если AJAX скрипт берёт quantity только из data-quantity
        jQuery(function($){
            $(document).on('click', '.single_add_to_cart_button', function(){
                var $btn  = $(this);
                var $form = $btn.closest('form.cart-old');
                var qty   = $form.find('input.qty').val() || 1;
                $btn.data('quantity', qty).attr('data-quantity', qty);
            });
        });
    </script>
    <style>
        .cart__qty {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .cart__qty-val {
            min-width: 24px;
            text-align: center;
            font-weight: 600;
        }

        .cart__qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #d0d0d0;
            background: #fff;
            font-size: 20px;
            line-height: 1;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cart__qty-btn--plus {
            background: #4fbd01;
            border-color: #4fbd01;
            position: relative;
            font-size: 0;
        }

        .cart__qty-btn--plus::before,
        .cart__qty-btn--plus::after {
            content: '';
            position: absolute;
            left: 50%;
            top: 50%;
            background: #fff;
            transform: translate(-50%, -50%);
            border-radius: 1px;
        }

        .cart__qty-btn--plus::before {
            width: 14px;
            height: 2px;
        }

        .cart__qty-btn--plus::after {
            width: 2px;
            height: 14px;
        }

        .cart__qty-btn--minus {
            color: #444;
            font-size: 28px;
            font-weight: 800;
        }

        .cart__qty-btn.is-disabled {
            opacity: .4;
            cursor: default;
            pointer-events: none;
        }

    </style>

    <?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php endif; ?>

