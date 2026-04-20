<?php do_action('woocommerce_after_mini_cart'); ?><?php
/**
 * Mini-cart
 *
 * Contains the markup for the mini-cart, used by the cart widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/mini-cart.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 5.2.0
 */

defined('ABSPATH') || exit;

do_action('woocommerce_before_mini_cart'); ?>

<?php if (!WC()->cart->is_empty()) : ?>

    <div class="cart__top">
        <h3 class="cart__title">КОРЗИНА</h3>
        <button class="cart__close"></button>
    </div>

    <ul class="woocommerce-mini-cart cart_list product_list_widget cart__body">
        <?php
        do_action('woocommerce_before_mini_cart_contents');

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $_product = apply_filters('woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key);
            $product_id = apply_filters('woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key);

            if ($_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters('woocommerce_widget_cart_item_visible', true, $cart_item, $cart_item_key)) {
                $product_name = apply_filters('woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key);
                $thumbnail = apply_filters('woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key);
                $product_price = apply_filters('woocommerce_cart_item_price', WC()->cart->get_product_price($_product), $cart_item, $cart_item_key);
                ?>
                <div class="woocommerce-mini-cart-item cart__item"
                     data-product_id="<?php echo esc_attr( $cart_item['product_id'] ); ?>"
                     data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>">
                    <div class="cart__item-content">
                        <div class="cart__item-product">
                            <div class="cart__item-img">
                                <?php echo $thumbnail; ?>
                            </div>
                            <div class="cart__item-container">
                                <h4 class="cart__item-name">
                                    <?php
                                    echo wp_kses_post( $product_name ); // или просто echo $product_name;
                                    ?>

                                    <?php if ( $_product->is_on_sale() ) : ?>
                                        <span class="onsale"><?php esc_html_e( 'Акция!', 'woocommerce' ); ?></span>
                                    <?php endif; ?>
                                </h4>

                                <?php
                                // Кол-во "шагов" (1, 2, 3...)
                                $quantity = $cart_item['quantity'];

                                // Коэффициент веса
                                if ( function_exists( 'fdv_ms_get_weight_ratio_for_product' ) ) {
                                    $weight_ratio = fdv_ms_get_weight_ratio_for_product( $product_id );
                                } else {
                                    $weight_ratio = 1;
                                }

                                $is_weight_product = ( $weight_ratio > 0 && $weight_ratio < 1 );                                $is_weight_product = ( $weight_ratio > 0 && $weight_ratio < 1 );

                                // Итог по позиции из корзины (как считает Woo, уже с налогами)
                                $line_total = $cart_item['line_total'] + $cart_item['line_tax'];

                                // Цена одного шага (единицы quantity)
                                $price_step = $quantity > 0 ? $line_total / $quantity : 0;

                                // Что показываем рядом с +/-: всегда целое число шагов
                                $qty_display = wc_format_localized_decimal( $quantity );
                                ?>
                                <span class="quantity" style="display:none;"><?php echo $quantity; ?></span>

                                <?php if ( empty( $cart_item['q_promo_gift'] ) ) : ?>

                                    <div class="cart__item-calc">
                                        <div class="cart__qty"
                                             data-cart_item_key="<?php echo esc_attr( $cart_item_key ); ?>"
                                             data-product_id="<?php echo esc_attr( $cart_item['product_id'] ); ?>"
                                             data-weight_ratio="<?php echo esc_attr( $weight_ratio ); ?>"
                                             data-current_qty="<?php echo esc_attr( $quantity ); ?>"
                                             data-steps="<?php echo esc_attr( $quantity ); ?>">

                                            <button type="button"
                                                    class="cart__qty-btn cart__qty-btn--minus"
                                                    aria-label="Уменьшить количество">
                                                –
                                            </button>

                                            <span class="cart__qty-val">
                <?php echo esc_html( $qty_display ); ?>
            </span>

                                            <button type="button" class="cart__qty-btn cart__qty-btn--plus"
                                                    aria-label="Увеличить количество">+</button>
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

                                <?php else : ?>

                                    <!-- Подарок: без количества и формулы, только имя и цена 0 ₽ -->
                                    <!-- Можно вообще ничего не выводить -->
                                    <!-- <div class="cart__item-calc cart__item-calc--gift"></div> -->

                                <?php endif; ?>


                            </div>
                        </div>

                        <div class="cart__item-price">
                            <p><?php echo wc_price( $line_total ); ?></p>
                        </div>

                    </div>

                    <?php
                    echo apply_filters(
                        'woocommerce_cart_item_remove_link',
                        sprintf(
                            '<a href="%s" class="cart__delete remove_from_cart_button" aria-label="%s" data-cart_item_key="%s" data-product_id="%s" data-product_sku="%s">
                                <img src="%s/assets/img/delete.svg" alt="%s" width="16" height="16">
                            </a>',
                            esc_url(wc_get_cart_remove_url($cart_item_key)),
                            esc_attr__('Remove this item', 'woocommerce'),
                            esc_attr($cart_item_key),
                            esc_attr($cart_item['product_id']),
                            esc_attr($cart_item['data']->get_sku()),
                            esc_url(get_template_directory_uri()),
                            esc_attr__('Remove', 'woocommerce')
                        ),
                        $cart_item_key
                    );
                    ?>
                </div>
                <?php
            }
        }

        do_action('woocommerce_mini_cart_contents');
        ?>
    </ul>
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
        .cart__item-calc-sum {
            margin-top: 10px;
            display: block;
        }

        .cart__qty-btn {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            border: 1px solid #d0d0d0;
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
            color: #fff;
            font-size: 28px;
            font-weight: 800;
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
            height: 4px;
        }

        .cart__qty-btn--plus::after {
            width: 4px;
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
    <div class="cart__total">
        <h5 class="cart__total-title">
            Итого: <?php echo strip_tags(WC()->cart->get_cart_subtotal()); ?>
        </h5>
        <a href="<?php echo esc_url(wc_get_checkout_url()); ?>" class="cart__total-order btn-green">
            <p>Оформить заказ</p>
        </a>
    </div>

<?php else : ?>

    <p class="woocommerce-mini-cart__empty-message"><?php esc_html_e('Корзина пуста.', 'woocommerce'); ?></p>

<?php endif; ?>

<?php do_action('woocommerce_after_mini_cart'); ?>