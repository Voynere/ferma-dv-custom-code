<?php
/**
 * Cart errors page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/cart-errors.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<?php if(1==2) : ?>
<p><?php esc_html_e( 'There are some issues with the items in your cart. Please go back to the cart page and resolve these issues before checking out.', 'woocommerce' ); ?></p>

<p>Некоторых позиций в Вашей корзине нет на остатках.<br>
Пожалуйста, удалите/замените из корзины отсутствующие товары и продолжите оформление.</p>
<?php endif; ?>

<?php do_action( 'woocommerce_cart_has_errors' ); ?>

<?php wc_get_template( 'checkout/form-checkout.php', array( 'checkout' => $checkout ) ); ?>

<?php if(1==2) : ?>
<p><a class="button wc-backward" href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Return to cart', 'woocommerce' ); ?></a></p>
<?php endif; ?>
