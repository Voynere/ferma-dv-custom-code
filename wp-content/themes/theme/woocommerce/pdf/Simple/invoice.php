<?php if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly ?>

<?php do_action( 'wpo_wcpdf_before_document', $this->get_type(), $this->order ); ?>
<?php

$dev = get_post_meta( $order->id, 'billing_delivery', true );
$pod = get_post_meta( $order->id, 'billing_dev_2', true );
$et = get_post_meta( $order->id, 'billing_dev_3', true );
$of = get_post_meta( $order->id, 'billing_dev_1', true );
$do = get_post_meta( $order->id, 'billing_dev_4', true );
$time = get_post_meta( $order->id, 'billing_asdx1', true );
$sam = get_post_meta( $order->id, 'billing_samoviziv', true );
$timesam = get_post_meta( $order->id, 'billing_type_delivery_sam', true );
$comc = get_post_meta( $order->id, 'billing_comment', true );
$com = get_post_meta( $order->id, 'billing_comment_zakaz', true );

?>
<table class="head container">
    <tr>
        <td class="header">
            <?php
            if ( $this->has_header_logo() ) {
                $this->header_logo();
            } else {
                echo $this->get_title();
            }
            ?>
        </td>
        <td class="shop-info">
            <?php do_action( 'wpo_wcpdf_before_shop_name', $this->get_type(), $this->order ); ?>
            <div class="shop-name"><h3><?php $this->shop_name(); ?></h3></div>
            <?php do_action( 'wpo_wcpdf_after_shop_name', $this->get_type(), $this->order ); ?>
            <?php do_action( 'wpo_wcpdf_before_shop_address', $this->get_type(), $this->order ); ?>
            <div class="shop-address"><?php $this->shop_address(); ?></div>
            <?php do_action( 'wpo_wcpdf_after_shop_address', $this->get_type(), $this->order ); ?>
        </td>
    </tr>
</table>

<?php do_action( 'wpo_wcpdf_before_document_label', $this->get_type(), $this->order ); ?>

<h1 class="document-type-label">
    <?php if ( $this->has_header_logo() ) echo $this->get_title(); ?>
</h1>

<?php do_action( 'wpo_wcpdf_after_document_label', $this->get_type(), $this->order ); ?>

<table class="order-data-addresses">
    <tr>
        <td class="address billing-address">
            <!-- <h3><?php _e( 'Billing Address:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3> -->
            <?php do_action( 'wpo_wcpdf_before_billing_address', $this->get_type(), $this->order ); ?>
            <?php $this->billing_address(); ?>
            <?php do_action( 'wpo_wcpdf_after_billing_address', $this->get_type(), $this->order ); ?>
            <?php if ( isset( $this->settings['display_email'] ) ) : ?>
                <div class="billing-email"><?php $this->billing_email(); ?></div>
            <?php endif; ?>
            <?php if ( isset( $this->settings['display_phone'] ) ) : ?>
                <div class="billing-phone"><?php $this->billing_phone(); ?></div>
            <?php endif; ?>
        </td>
        <td class="address shipping-address">
            <?php if ( $this->show_shipping_address() ) : ?>
                <h3><?php _e( 'Ship To:', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
                <?php do_action( 'wpo_wcpdf_before_shipping_address', $this->get_type(), $this->order ); ?>

                <?php echo get_post_meta( $order->id, 'billing_delivery', true ); ?><br>

                <?php if ($pod) : ?>
                    <b>Подъезд:</b> <?php echo $pod; ?><br>
                <?php endif; ?>

                <?php if ($et) : ?>
                    <b>Этаж:</b> <?php echo $et; ?><br>
                <?php endif; ?>

                <?php if ($of) : ?>
                    <b>Квартира / офис:</b> <?php echo $of; ?><br>
                <?php endif; ?>

                <?php if ($do) : ?>
                    <b>Домофон:</b> <?php echo $do; ?><br>
                <?php endif; ?>

                <?php if ($time) : ?>
                    <b>Время доставки:</b> <?php echo $time; ?><br>
                <?php endif; ?>

                <?php if ($sam) : ?>
                    <b>Магазин для самовывоза:</b> <?php echo $sam; ?><br>
                <?php endif; ?>

                <?php if ($timesam) : ?>
                    <b>Когда доставка (самовывоз):</b> <?php echo $timesam; ?><br>
                <?php endif; ?>

                <?php if ($comc) : ?>
                    <b>Комментарий курьеру:</b> <?php echo $comc; ?><br>
                <?php endif; ?>

                <?php if ($com) : ?>
                    <b>Комментарий по заказу:</b> <?php echo $com; ?><br>
                <?php endif; ?>


                <?php do_action( 'wpo_wcpdf_after_shipping_address', $this->get_type(), $this->order ); ?>
                <?php if ( isset( $this->settings['display_phone'] ) ) : ?>
                    <div class="shipping-phone"><?php $this->shipping_phone(); ?></div>
                <?php endif; ?>
            <?php endif; ?>
        </td>
        <td class="order-data">
            <table>
                <?php do_action( 'wpo_wcpdf_before_order_data', $this->get_type(), $this->order ); ?>
                <?php if ( isset( $this->settings['display_number'] ) ) : ?>
                    <tr class="invoice-number">
                        <th><?php echo $this->get_number_title(); ?></th>
                        <td><?php $this->invoice_number(); ?></td>
                    </tr>
                <?php endif; ?>
                <?php if ( isset( $this->settings['display_date'] ) ) : ?>
                    <tr class="invoice-date">
                        <th><?php echo $this->get_date_title(); ?></th>
                        <td><?php $this->invoice_date(); ?></td>
                    </tr>
                <?php endif; ?>
                <tr class="order-number">
                    <th><?php _e( 'Order Number:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
                    <td><?php $this->order_number(); ?></td>
                </tr>
                <tr class="order-date">
                    <th><?php _e( 'Order Date:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
                    <td><?php $this->order_date(); ?></td>
                </tr>
                <?php if ( $payment_method = $this->get_payment_method() ) : ?>
                    <tr class="payment-method">
                        <th><?php _e( 'Payment Method:', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
                        <td><?php echo $payment_method; ?></td>
                    </tr>
                <?php endif; ?>
                <?php do_action( 'wpo_wcpdf_after_order_data', $this->get_type(), $this->order ); ?>
            </table>
        </td>
    </tr>
</table>

<?php do_action( 'wpo_wcpdf_before_order_details', $this->get_type(), $this->order ); ?>

<table class="order-details">
    <thead>
    <tr>
        <th class="product"><?php _e( 'Product', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
        <th class="discount">Скидка</th>
        <th class="quantity"><?php _e( 'Quantity', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
        <th class="price"><?php _e( 'Price', 'woocommerce-pdf-invoices-packing-slips' ); ?></th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ( $this->get_order_items() as $item_id => $item ) : ?>
        <?php $product = wc_get_product( $item['product_id'] ); ?>
        <?php
        // весовой / штучный
        $is_weighted = ( get_field( 'razbivka_vesa', $item['product_id'] ) == 'да' );

        // по умолчанию – просто qty (для штучных товаров)
        $display_qty = $item['quantity'];
        $real_weight = null;

        if ( $is_weighted && function_exists( 'fdv_get_weight_ratio_for_product' ) ) {
            $ratio = (float) fdv_get_weight_ratio_for_product( $item['product_id'] );
            if ( $ratio <= 0 ) {
                $ratio = 1;
            }

            // реальный вес в кг
            $real_weight = $ratio * (float) $item['quantity'];

            // красиво форматируем, например "0,3 кг"
            $display_qty = wc_format_localized_decimal( $real_weight ) . ' кг';
        }
        ?>
        <tr class="<?php echo apply_filters( 'wpo_wcpdf_item_row_class', 'item-'.$item_id, $this->get_type(), $this->order, $item_id ); ?>">
            <td class="product">
                <?php $description_label = __( 'Description', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
                <span class="item-name"><?php echo $item['name']; ?></span>
                <?php do_action( 'wpo_wcpdf_before_item_meta', $this->get_type(), $item, $this->order  ); ?>
                <span class="item-meta"><?php echo $item['meta']; ?></span>
                <dl class="meta">
                    <?php $description_label = __( 'SKU', 'woocommerce-pdf-invoices-packing-slips' ); // registering alternate label translation ?>
                    <?php if ( ! empty( $item['sku'] ) ) : ?><dt class="sku"><?php _e( 'SKU:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt><dd class="sku"><?php echo $item['sku']; ?></dd><?php endif; ?>
                    <?php if ( $is_weighted && isset( $real_weight ) ) : ?>
                        <dt class="weight"><?php _e( 'Weight:', 'woocommerce-pdf-invoices-packing-slips' ); ?></dt>
                        <dd class="weight">
                            <?php echo wc_format_localized_decimal( $real_weight ); ?><?php echo get_option( 'woocommerce_weight_unit' ); ?>
                        </dd>
                    <?php endif; ?>
                </dl>
                <?php do_action( 'wpo_wcpdf_after_item_meta', $this->get_type(), $item, $this->order  ); ?>
            </td>
            <td class="discount">
                <?php
                $discount = get_field('priceint', 'option');
                $green_friday_products = get_green_friday_products();

                foreach($green_friday_products['good_ids_with_discount'] as $percent => $green_friday_product) {
                    if(in_array($item['product_id'], $green_friday_product)) {
                        $discount = $percent;
                    }
                }

                if($product->get_regular_price() != $product->get_price()) {
                    echo $discount . "%";
                } else {
                    echo "0%";
                }
                ?>
            </td>
            <td class="quantity"><?php echo $display_qty; ?></td>
            <td class="price">
                <?php
                if($product->get_regular_price() != $product->get_price()) {
                    echo '<s>' . $product->get_regular_price() . '</s> ' . $item['order_price'];
                } else {
                    echo $item['order_price'];
                }
                ?>
                <?php //echo $item['order_price']; ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
    <tfoot>
    <tr class="no-borders">
        <td class="no-borders">
            <div class="document-notes">
                <?php do_action( 'wpo_wcpdf_before_document_notes', $this->get_type(), $this->order ); ?>
                <?php if ( $this->get_document_notes() ) : ?>
                    <h3><?php _e( 'Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
                    <?php $this->document_notes(); ?>
                <?php endif; ?>
                <?php do_action( 'wpo_wcpdf_after_document_notes', $this->get_type(), $this->order ); ?>
            </div>
            <div class="customer-notes">
                <?php do_action( 'wpo_wcpdf_before_customer_notes', $this->get_type(), $this->order ); ?>
                <?php if ( $this->get_shipping_notes() ) : ?>
                    <h3><?php _e( 'Customer Notes', 'woocommerce-pdf-invoices-packing-slips' ); ?></h3>
                    <?php $this->shipping_notes(); ?>
                <?php endif; ?>
                <?php do_action( 'wpo_wcpdf_after_customer_notes', $this->get_type(), $this->order ); ?>
            </div>
        </td>
        <td class="no-borders" colspan="2">
            <table class="totals">
                <tfoot>
                <?php foreach ( $this->get_woocommerce_totals() as $key => $total ) : ?>
                    <tr class="<?php echo $key; ?>">
                        <th class="description"><?php echo $total['label']; ?></th>
                        <td class="price"><span class="totals-price"><?php echo $total['value']; ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tfoot>
            </table>
        </td>
    </tr>
    </tfoot>
</table>

<div class="bottom-spacer"></div>

<?php do_action( 'wpo_wcpdf_after_order_details', $this->get_type(), $this->order ); ?>

<?php if ( $this->get_footer() ) : ?>
    <div id="footer">
        <!-- hook available: wpo_wcpdf_before_footer -->
        <?php $this->footer(); ?>
        <!-- hook available: wpo_wcpdf_after_footer -->
    </div><!-- #letter-footer -->
<?php endif; ?>

<?php do_action( 'wpo_wcpdf_after_document', $this->get_type(), $this->order );

// Шаг веса для товара: 0.1 кг, 1 кг и т.п.
function fdv_get_weight_ratio_for_product( $product_id ) {

    // НЕ весовой товар – считаем, что шаг 1 (штучный)
    if ( get_field( 'razbivka_vesa', $product_id ) !== 'да' ) {
        return 1;
    }

    // Базовый шаг из твоей старой функции (если нужна)
    if ( function_exists( 'fdv_ms_get_weight_ratio_for_product' ) ) {
        $ratio = (float) fdv_ms_get_weight_ratio_for_product( $product_id );
    } else {
        $ratio = 0.1;
    }

    // Если есть твоя функция категорий на 0.1 кг — оставляем
    if ( function_exists( 'ferma_product_in_ratio_01_categories' ) && ferma_product_in_ratio_01_categories( $product_id ) ) {
        $ratio = 0.1;
    }

    if ( $ratio <= 0 ) {
        $ratio = 1;
    }

    return $ratio;
}

?>
