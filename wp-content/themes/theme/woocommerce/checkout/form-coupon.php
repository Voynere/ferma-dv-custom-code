<?php
/**
 * Checkout coupon form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-coupon.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.4
 */

defined( 'ABSPATH' ) || exit;

// ========== ДОБАВЬТЕ ЭТОТ КОД ==========
// Проверяем тип доставки
$is_pickup = false;

if ( is_user_logged_in() ) {
    $user_id = get_current_user_id();
    $delivery_type = get_user_meta( $user_id, 'delivery', true );
    if ( $delivery_type === '1' ) {
        $is_pickup = true;
    }
} elseif ( isset( $_COOKIE['delivery'] ) ) {
    if ( $_COOKIE['delivery'] === '1' ) {
        $is_pickup = true;
    }
}

// Если самовывоз - выходим и не показываем купон
if ( $is_pickup ) {
    return;
}
// ========== КОНЕЦ ДОБАВЛЕННОГО КОДА ==========

if ( ! wc_coupons_enabled() ) { // @codingStandardsIgnoreLine.
    return;
}

?>
<div class="woocommerce-form-coupon-toggle">
    <?php wc_print_notice( apply_filters( 'woocommerce_checkout_coupon_message', esc_html__( 'Have a coupon?', 'woocommerce' ) . ' <a href="#" class="showcoupon">' . esc_html__( 'Click here to enter your code', 'woocommerce' ) . '</a>' ), 'notice' ); ?>
</div>

<form class="checkout_coupon woocommerce-form-coupon" method="post" style="display:none">

    <p><?php esc_html_e( 'If you have a coupon code, please apply it below.', 'woocommerce' ); ?></p>

    <p class="form-row form-row-first">
        <input type="text" name="coupon_code" class="input-text" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" id="coupon_code" value="" />
    </p>

    <p class="form-row form-row-last">
        <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'woocommerce' ); ?>"><?php esc_html_e( 'Apply coupon', 'woocommerce' ); ?></button>
    </p>

    <div class="clear"></div>
</form>