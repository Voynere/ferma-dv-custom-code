<?php
/**
 * Review order table
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/review-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined( 'ABSPATH' ) || exit;
?>
<table class="shop_table woocommerce-checkout-review-order-table ferma-checkout__form-table">

	<tbody>
		<?php
		do_action( 'woocommerce_review_order_before_cart_contents' );

        foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {

            $_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
            $product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

            if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {

                $product_name = $_product->get_name();
                $thumbnail     = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image( 'woocommerce_thumbnail', ['style' => ''] ), $cart_item, $cart_item_key );
                $quantity      = $cart_item['quantity'];

                // коэффициент веса — как в mini-cart
                if ( function_exists( 'fdv_ms_get_weight_ratio_for_product' ) ) {
                    $weight_ratio = fdv_ms_get_weight_ratio_for_product( $product_id );
                } else {
                    $weight_ratio = 1;
                }

                $is_weight_product = ( $weight_ratio > 0 && $weight_ratio < 1 );
                $qty_steps         = $is_weight_product
                    ? max( 1, (int) round( (float) $quantity / (float) $weight_ratio ) )
                    : max( 1, (int) round( (float) $quantity ) );

                // сумма по позиции (как в mini-cart)
                $line_total = $cart_item['line_total'] + $cart_item['line_tax'];

                // цена одного шага
                $price_step = $quantity > 0 ? $line_total / $quantity : 0;

                // что показываем между кнопками
                $qty_display = wc_format_localized_decimal( $quantity );
                ?>
                <tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <td class="product-name">
                        <div class="product-name__img">
                            <?php echo $thumbnail; ?>
                        </div>

                        <div class="product-name__container">
                            <h4 class="ferma-name">
                                <?php echo wp_kses_post( $product_name ); ?>
                            </h4>

                            <?php if ( empty( $cart_item['q_promo_gift'] ) ) : ?>

                                <div class="cart__item-calc">
                                    <div class="cart__qty"
                                         data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>"
                                         data-product_id="<?php echo esc_attr( $cart_item['product_id'] ); ?>"
                                         data-weight_ratio="<?php echo esc_attr( $weight_ratio ); ?>"
                                         data-current_qty="<?php echo esc_attr( $quantity ); ?>"
                                         data-steps="<?php echo esc_attr( $qty_steps ); ?>">

                                        <button type="button"
                                                class="cart__qty-btn cart__qty-btn--minus"
                                                aria-label="Уменьшить количество">
                                            –
                                        </button>

                                        <span class="cart__qty-val">
                <?php echo esc_html( $qty_display ); ?>
            </span>

                                        <button type="button"
                                                class="cart__qty-btn cart__qty-btn--plus"
                                                aria-label="Увеличить количество">
                                            +
                                        </button>
                                    </div>

                                    <div class="cart__item-calc-sum">
                                        <?php
                                        if ( $is_weight_product ) {
                                            $qty_kg       = wc_format_localized_decimal( $quantity * $weight_ratio );
                                            $price_per_kg = $weight_ratio > 0 ? ( $price_step / $weight_ratio ) : 0;

                                            printf(
                                                '%s кг × %s/кг = %s',
                                                $qty_kg,
                                                wc_price( $price_per_kg ),
                                                wc_price( $line_total )
                                            );
                                        } else {
                                            $qty_formatted = wc_format_localized_decimal( $quantity );

                                            printf(
                                                '%s × %s = %s',
                                                $qty_formatted,
                                                wc_price( $price_step ),
                                                wc_price( $line_total )
                                            );
                                        }
                                        ?>
                                    </div>
                                </div>

                            <?php endif; ?>


                            <?php
                            // атрибуты/вариации
                            echo wc_get_formatted_cart_item_data( $cart_item );

                            // промо-подарок
                            if ( ! empty( $cart_item['q_promo_gift'] ) ) :
                                $regular_price = (float) $_product->get_regular_price();
                                if ( $regular_price <= 0 ) {
                                    $regular_price = (float) $_product->get_price();
                                }
                                ?>
                                <p class="ferma-gift-info">
                                    <?php if ( $regular_price > 0 ) : ?>
                                        <span class="ferma-gift-old">
                    <del><?php echo wc_price( $regular_price ); ?></del>
                </span>
                                        <?php endif; ?>
                                    <span class="ferma-gift-label">В подарок</span>
                                </p>
                            <?php endif; ?>



                        </div>
                    </td>

                    <style>
                        .ferma-gift-info {
                            margin-top: 4px;
                            font-size: 14px;
                            line-height: 1.3;
                        }

                        .ferma-gift-old del {
                            opacity: .7;
                            margin-right: 4px;
                        }

                        .ferma-gift-label {
                            font-weight: 600;
                            color: #4fbd01;
                        }
                        .ferma-checkout__form-table .product-total {
                            text-align: right;
                            vertical-align: top;
                        }

                        .product-total__price {
                            display: block;
                            margin-bottom: 4px;
                        }

                        .cart__delete {
                            display: inline-block;
                        }

                    </style>
                    <td class="product-total">
                        <div class="product-total__price">
                            <?php
                            // чтобы сумма совпадала с тем, что ты уже считаешь
                            echo wc_price( $line_total );
                            ?>
                        </div>
                   
                        <?php
                        // переносим кнопку удаления сюда, под цену
                        echo apply_filters(
                            'woocommerce_cart_item_remove_link',
                            sprintf(
                                '<a href="%s"
                class="cart__delete js-checkout-remove-item"
                aria-label="%s"
                data-cart_item_key="%s"
                data-product_id="%s"
                data-product_sku="%s">
                    <img src="%s/assets/img/delete.svg" alt="%s" width="16" height="16">
            </a>',
                                esc_url( wc_get_cart_remove_url( $cart_item_key ) ),
                                esc_attr__( 'Remove this item', 'woocommerce' ),
                                esc_attr( $cart_item_key ),
                                esc_attr( $cart_item['product_id'] ),
                                esc_attr( $cart_item['data']->get_sku() ),
                                esc_url( get_template_directory_uri() ),
                                esc_attr__( 'Remove', 'woocommerce' )
                            ),
                            $cart_item_key
                        );
                        ?>
                    </td>
                </tr>
                <?php
            }
        }

        do_action( 'woocommerce_review_order_after_cart_contents' );
		?>
	</tbody>
	<tfoot>
    <script>
        jQuery(function($) {
            // Функция для чтения куки
            function getCookie(name) {
                var matches = document.cookie.match(new RegExp(
                    "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
                ));
                return matches ? decodeURIComponent(matches[1]) : undefined;
            }

            function toggleCouponField() {
                var deliveryType = getCookie('delivery');
                console.log('Delivery cookie:', deliveryType);

                if (deliveryType === "1") {
                    // Самовывоз
                    $('.woocommerce-form-coupon-toggle').hide();
                    $('.checkout_coupon').hide();
                    $('form.checkout_coupon').hide();
                    $('.coupon').hide();
                    $('.woocommerce-info').hide();
                } else {
                    // Доставка
                    $('.woocommerce-form-coupon-toggle').show();
                    $('.checkout_coupon').show();
                    $('form.checkout_coupon').show();
                    $('.coupon').show();
                }
            }

            // Проверяем при загрузке
            toggleCouponField();

            // При клике на кнопки в модальном окне (обновление куки)
            $(document).on('click', '#but_dev, #but_dev2', function() {
                setTimeout(toggleCouponField, 800);
            });

            // При обновлении чекаута
            $(document.body).on('updated_checkout', toggleCouponField);

            // Также проверяем каждую секунду (на всякий случай)
            setInterval(toggleCouponField, 1000);
        });
    </script>
    <script>
        jQuery(function ($) {
            $(document).on('click', '.js-checkout-remove-item', function (e) {
                e.preventDefault();

                var $link = $(this);
                var cartItemKey = $link.data('cart_item_key');

                if (!cartItemKey) {
                    return;
                }

                $.ajax({
                    type: 'POST',
                    url: wc_checkout_params.wc_ajax_url
                        .toString()
                        .replace('%%endpoint%%', 'remove_from_cart'),
                    data: {
                        cart_item_key: cartItemKey
                    },
                    success: function (response) {
                        // Удаляем строку из таблицы
                        var $row = $link.closest('tr.cart_item');
                        $row.slideUp(200, function () {
                            $(this).remove();
                        });

                        // Обновляем блоки checkout (итоги, доставку и т.п.)
                        $(document.body).trigger('update_checkout');
                    },
                    error: function () {
                        // На крайний случай: если AJAX упал – фоллбэк на обычный переход
                        window.location.href = $link.attr('href');
                    }
                });
            });
        });
    </script>

		<!-- <tr class="cart-subtotal">
			<th><?php // esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
			<td><?php // wc_cart_totals_subtotal_html(); ?></td>
		</tr> -->

		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
				<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
			</tr>
		<?php endforeach; ?>

		<?php // if ( WC()->cart->needs_shipping() && WC()->cart->show_shipping() ) : ?>

			<?php  // do_action( 'woocommerce_review_order_before_shipping' ); ?>

			<?php  // wc_cart_totals_shipping_html(); ?>

			<?php  // do_action( 'woocommerce_review_order_after_shipping' ); ?>

		<?php  // endif; ?>

		<!-- <?php // foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<tr class="fee">
				<th><?php // echo esc_html( $fee->name ); ?></th>
				<td><?php // wc_cart_totals_fee_html( $fee ); ?></td>
			</tr>
		<?php // endforeach; ?> -->

		<?php if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) : ?>
			<?php if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) : ?>
				<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
					<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<th><?php echo esc_html( $tax->label ); ?></th>
						<td><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
					</tr>
				<?php endforeach; ?>
			<?php else : ?>
				<tr class="tax-total">
					<th><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></th>
					<td><?php wc_cart_totals_taxes_total_html(); ?></td>
				</tr>
			<?php endif; ?>
		<?php endif; ?>

		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>

		<!-- <tr class="order-total">
			<th><?php // esc_html_e( 'Total', 'woocommerce' ); ?></th>
			<td><?php // wc_cart_totals_order_total_html(); ?></td>
		</tr> -->

		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>

	</tfoot>
</table>
