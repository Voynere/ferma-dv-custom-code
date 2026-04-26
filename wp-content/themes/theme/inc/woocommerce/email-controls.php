<?php
/**
 * WooCommerce email trigger controls.
 *
 * @package Theme
 */

//add_action( 'woocommerce_email', 'ferma_disable_emails' );
function ferma_disable_emails( $email_class ) {
	//remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_pending_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	remove_action( 'woocommerce_order_status_failed_to_processing_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	//remove_action( 'woocommerce_order_status_failed_to_completed_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );
	//remove_action( 'woocommerce_order_status_failed_to_on-hold_notification', array( $email_class->emails['WC_Email_New_Order'], 'trigger' ) );

	//remove_action( 'woocommerce_order_status_pending_to_processing_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );
	//remove_action( 'woocommerce_order_status_pending_to_on-hold_notification', array( $email_class->emails['WC_Email_Customer_Processing_Order'], 'trigger' ) );

	remove_action( 'woocommerce_order_status_completed_notification', array( $email_class->emails['WC_Email_Customer_Completed_Order'], 'trigger' ) );
}

function ferma_woocommerce_email_recipient( $recipient, $order, $email ) {
	if ( ! $order || ! is_a( $order, 'WC_Order' ) ) {
		return $recipient;
	}

	return '';
}
add_filter( 'woocommerce_email_recipient_customer_on_hold_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_processing_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_pending_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_on-hold_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_completed_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_cancelled_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_refunded_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_failed_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_podtverjden_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_sobran_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otgrujen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_kurer-naznachen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_zakaz-v-puti_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_zakaz-oplachen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_picked-up_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_dostavlen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otmenen_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_vozvrat_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otmena-otkaz_order', 'ferma_woocommerce_email_recipient', 10, 3 );
add_filter( 'woocommerce_email_recipient_customer_otmena-otkaz2_order', 'ferma_woocommerce_email_recipient', 10, 3 );

function ferma_admin_new_order_recipient( $recipient, $order, $email ) {
	return 'zakaz@ferma-dv.ru';
}
add_filter( 'woocommerce_email_recipient_new_order', 'ferma_admin_new_order_recipient', 10, 3 );
