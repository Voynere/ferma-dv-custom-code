<?php
/**
 * Checkout Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-checkout.php.
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

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, the user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}

?>

<form name="checkout" method="post" class="checkout woocommerce-checkout ferma-checkout__form" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data">

	<?php if ( $checkout->get_checkout_fields() ) : ?>

		<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

		<div class="col2-set" id="customer_details">
			<div class="col-1">
				<?php do_action( 'woocommerce_checkout_billing' ); ?>
			</div>

			<!-- <div class="col-2"> -->
				<?php // do_action( 'woocommerce_checkout_shipping' ); ?>
			<!-- </div> -->
		</div>

		<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

	<?php endif; ?>
	
	<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>
	
	
	<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

	<div id="order_review" class="woocommerce-checkout-review-order">
		<?php do_action( 'woocommerce_checkout_order_review' ); ?>
	</div>

	<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

</form>

<?php
$ferma_billing_coord_src = '';
if ( ! empty( $_COOKIE['billing_coords'] ) ) {
	$ferma_billing_coord_src = (string) wp_unslash( (string) $_COOKIE['billing_coords'] );
} elseif ( ! empty( $_COOKIE['coords'] ) ) {
	$ferma_billing_coord_src = (string) wp_unslash( (string) $_COOKIE['coords'] );
}
$ferma_billing_parts = array_map( 'trim', explode( ',', $ferma_billing_coord_src, 2 ) );
$ferma_checkout_lat  = 43.111787507251414;
$ferma_checkout_lng  = 131.88327396290603;
if ( isset( $ferma_billing_parts[0], $ferma_billing_parts[1] ) && is_numeric( $ferma_billing_parts[0] ) && is_numeric( $ferma_billing_parts[1] ) ) {
	$ferma_checkout_lat = (float) $ferma_billing_parts[0];
	$ferma_checkout_lng = (float) $ferma_billing_parts[1];
}
$ferma_default_delivery = '';
if ( ! empty( $_COOKIE['delivery_day'] ) && ! empty( $_COOKIE['delivery_time'] ) ) {
	$ferma_default_delivery = (string) wp_unslash( (string) $_COOKIE['delivery_day'] ) . '_' . (string) wp_unslash( (string) $_COOKIE['delivery_time'] );
}
?>
<script>
window.current_delivery_value = '';
var updated_times = false;
jQuery(document).ready(function() {
	function fermaTriggerWcCheckoutUpdate() {
		jQuery( document.body ).trigger( 'update_checkout' );
		if ( typeof window.wc_checkout_form !== 'undefined' && typeof window.wc_checkout_form.update_checkout === 'function' ) {
			window.wc_checkout_form.update_checkout();
		}
	}
	var fermaWcAjaxUrl = ( typeof woocommerce_params !== 'undefined' && woocommerce_params.ajax_url ) ? woocommerce_params.ajax_url : '/wp-admin/admin-ajax.php';

	// Класс на <p id="billing_*_field"> — WC триггерит update_checkout по change внутри .update_totals_on_change.
	function fermaEnsureDeliveryTotalsClass() {
		jQuery( '#billing_asdx1_field, #billing_type_delivery_sam_field' ).addClass( 'update_totals_on_change' );
	}
	fermaEnsureDeliveryTotalsClass();
	jQuery( document.body ).on( 'updated_checkout', fermaEnsureDeliveryTotalsClass );

	// Sync hidden billing fields (sent in post_data) so delivery fee recalculates on update_order_review
	// without depending on cookie round-trip timing in the same request.
	function fermaSyncDeliveryCtxFields( deliveryType ) {
		if ( ! deliveryType || String( deliveryType ).indexOf( '_' ) < 0 ) {
			return;
		}
		var a = String( deliveryType ).split( '_' );
		if ( a.length < 2 ) {
			return;
		}
		var $t = jQuery( 'input[name="billing[ferma_ctx_delivery_time]"]' );
		var $d = jQuery( 'input[name="billing[ferma_ctx_delivery_day]"]' );
		$d.val( a[0] );
		$t.val( a.slice( 1 ).join( '_' ) );
		// e.g. today_express -> a = today, express (slice(1) join = express) — actually today_express: split = ['today','express'], slice(1).join = 'express' — good
		// For today_morning: a = [today, morning] — a.slice(1).join = morning — good
	}

	// Не блокируем select времени на update_checkout:
	// disabled-поля не сериализуются и новое время доставки не уходит в Woo.
	
	jQuery( document.body ).on( 'change', 'select[name="billing_asdx1"], #billing_asdx1', function() {
		var $sel = jQuery( this ).find( ':selected' );
		var deliveryType = $sel.attr( 'data-value' );
		if ( ! deliveryType && $sel.length ) {
			deliveryType = $sel.data( 'value' );
		}
		if ( ! deliveryType || String( deliveryType ).indexOf( '_' ) < 0 ) {
			// Fallback for rare cases when data-value is missing in rebuilt options.
			var txt = String( $sel.text() || $sel.val() || '' ).toLowerCase();
			var d = txt.indexOf( 'завтра' ) !== -1 ? 'tomorrow' : 'today';
			var t = 'evening';
			if ( txt.indexOf( '10' ) !== -1 && txt.indexOf( '12' ) !== -1 ) {
				t = 'morning';
			} else if ( txt.indexOf( '15' ) !== -1 && txt.indexOf( '17' ) !== -1 ) {
				t = 'day';
			}
			deliveryType = d + '_' + t;
		}
		fermaSyncDeliveryCtxFields( deliveryType );
		var da = String( deliveryType ).split( '_' );
		if ( da.length >= 2 ) {
			var exp = new Date();
			exp.setTime( exp.getTime() + 24 * 60 * 60 * 1000 * 30 );
			document.cookie = 'delivery_day=' + da[0] + ';path=/;expires=' + exp.toUTCString();
			document.cookie = 'delivery_time=' + da.slice( 1 ).join( '_' ) + ';path=/;expires=' + exp.toUTCString();
		}
		// Immediate WC recalculation even if update_delivery_type AJAX is delayed/failed.
		fermaTriggerWcCheckoutUpdate();
		update_delivery_type( deliveryType );
	} );
	
	//$('#billing_asdx1').attr('disabled', true);
	//$('#billing_asdx1').remove();
	if(!updated_times) {
		ferma_update_times();
	}
	function fermaSetBillingTimeError( message ) {
		var msg = message || 'Не удалось определить время доставки.';
		var $w = jQuery( '#billing_asdx1_field .woocommerce-input-wrapper' );
		if ( ! $w.length ) {
			return;
		}
		$w.find( '.ferma-delivery-time-error' ).remove();
		if ( ! $w.find( 'select#billing_asdx1' ).length ) {
			$w.html( '<select name="billing_asdx1" id="billing_asdx1" class="woodefaults" aria-describedby="ferma-billing-time-error"></select>' );
		}
		jQuery( '#billing_asdx1' )
			.empty()
			.append( jQuery( '<option value=""></option>' ).text( '—' ) )
			.prop( 'disabled', true );
		$w.append( jQuery( '<p class="ferma-delivery-time-error" id="ferma-billing-time-error" role="alert"></p>' ).text( msg ) );
		fermaEnsureDeliveryTotalsClass();
	}
	function ferma_update_times() {
		let default_delivery = <?php echo json_encode( $ferma_default_delivery, JSON_UNESCAPED_UNICODE ); ?>;
		jQuery.ajax({
			type: "post",
			dataType: "json",
			url: "/wp-admin/admin-ajax.php",
			data: {
				action: 'get_delivery_prices',
				coords: [ <?php echo esc_attr( (string) $ferma_checkout_lat ); ?>, <?php echo esc_attr( (string) $ferma_checkout_lng ); ?> ]
			},
			beforeSend: function() {
				jQuery( '#billing_asdx1' ).attr( 'disabled', true );
				//$('#billing_asdx1_field').hide();
			},
			success: function(data) {
				window.updated_times = true;
				if(!data.success) {
					var emsg = ( data && data.data && data.data.error ) ? data.data.error : 'Введите верный адрес доставки.';
					fermaSetBillingTimeError( emsg );
				} else {
					jQuery( '#billing_asdx1_field .ferma-delivery-time-error' ).remove();
					jQuery( '#billing_sss' ).val( data.data.market );
					jQuery( '#billing_point' ).val( data.data.id );
					let choices = '';
					<?php
					date_default_timezone_set("Asia/Vladivostok");
					$express_date_start = strtotime(date("Y-m-d 08:00:00"));
					$express_date_end = strtotime(date("Y-m-d 20:00:00"));
					
					$coords = ($_COOKIE['coords']) ? $_COOKIE['coords'] : '43.111787507251414,131.88327396290603';
					if(isset($_POST['coords']) && is_array($_POST['coords'])) {
						$coords = $_POST['coords'][0] . "," . $_POST['coords'][1];
					}
					
					$express_price = ferma_get_delivery_express($coords);
					$express_stop_time_start = strtotime(date("Y-m-d 17:00:00"));
					$express_stop_time_end = strtotime(date("Y-m-d 19:00:00"));
					
					$curdate = time();
					if($curdate > $express_date_start && $curdate < $express_date_end && ($curdate < $express_stop_time_start || $curdate > $express_stop_time_end) && 1==2) { ?>
						let express_checked = '';
						if(default_delivery == 'today_express') {
							express_checked = ' checked';
						}
						let express_price = <?= (int) $express_price; ?>;
						if(typeof data.data.express != "undefined") {
							express_price = data.data.express;
						}
						choices = choices + 
								'<option data-value="today_express" value="Экспресс-доставка">Экспресс-доставка (В течении 60-90 мин)</option>';
					<?php } ?>
					if(typeof data.data.today != "undefined") {
						jQuery.each(data.data.today, function(type, price) {
							if(price.price == 0) {
								price.price = 'Бесплатно';
							} else {
								price.price = 'от 0 до ' + price.price + '&nbsp;₽';
							}
							
							let value = '';
							if(type == "morning") {
								value = 'Сегодня, с 10 до 12';
							}
							if(type == "day") {
								value = 'Сегодня, с 15 до 17';
							}
							if(type == "evening") {
								value = 'Сегодня, с 19 до 22';
							}
							
							let checked = '';
							if(default_delivery == 'today_' + type) {
								checked = ' selected';
							}
							
							choices = choices + 
								'<option data-value="today_' + type + '" value="' + value + '"'+ checked + '>' + value + '</option>';
						});
					}
									
					if(typeof data.data.tomorrow != "undefined") {
						jQuery.each(data.data.tomorrow, function(type,price) {
							if(price.price == 0) {
								price.price = 'Бесплатно';
							} else {
								price.price = 'от 0 до ' + price.price + '&nbsp;₽';
							}
							let value = '';
							if(type == "morning") {
								value = 'Завтра, с 10 до 12';
							}
							if(type == "day") {
								value = 'Завтра, с 15 до 17';
							}
							if(type == "evening") {
								value = 'Завтра, с 19 до 22';
							}
							
							let checked = '';
							if(default_delivery == 'tomorrow_' + type) {
								checked = ' selected';
							}
							
							choices = choices + 
								'<option data-value="tomorrow_' + type + '" value="' + value + '"'+ checked + '>' + value + '</option>';
						});
					}
					
					//choices = choices + '</div>';
					
					jQuery( '#billing_asdx1' ).html( choices );
					jQuery( '#billing_asdx1' ).attr( 'disabled', false );
					fermaEnsureDeliveryTotalsClass();
					window.current_delivery_value = jQuery( '#billing_asdx1 option:selected' ).attr( 'data-value' ) || '';
					if ( default_delivery ) {
						fermaSyncDeliveryCtxFields( default_delivery );
					} else {
						var dAttr = jQuery( '#billing_asdx1 option:selected' ).attr( 'data-value' );
						if ( dAttr ) {
							fermaSyncDeliveryCtxFields( dAttr );
						}
					}
					// One recalculate so the fee uses ferma_ctx_* in post_data.
					setTimeout( function() {
						fermaTriggerWcCheckoutUpdate();
					}, 30 );
					if(default_delivery == '') {
						const default_delivery_value = jQuery( '#billing_asdx1' ).find( ':selected' ).data( 'value' );
						const deliveryArray = default_delivery_value.split('_');
						
						var d = new Date;
						d.setTime(d.getTime() + 24*60*60*1000*30);
						document.cookie = 'delivery_day' + "=" + deliveryArray[0] + ";path=/;expires=" + d.toGMTString();
						document.cookie = 'delivery_time' + "=" + deliveryArray[1] + ";path=/;expires=" + d.toGMTString();
						//$('.order-times__item').first().find('input').attr('checked', true);
					}
					//$('#billing_asdx1_field').show();
					
					//$('select[name="billing_asdx1"]').trigger('change');
					
					//$('body').trigger( 'update_checkout' );
					//$('input.qty').trigger( 'change' );
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
				fermaSetBillingTimeError( 'Не удалось загрузить окна доставки. Проверьте адрес или обновите страницу.' );
			}
		});
	}
	
	function update_delivery_type(delivery_type)
	{
		if(delivery_type != window.current_delivery_value) {
			window.current_delivery_value = delivery_type;
			jQuery( '#place_order' ).attr( 'disabled', true );
			fermaSyncDeliveryCtxFields( delivery_type );
			var deliveryArray = (delivery_type || '').split('_');
			if (deliveryArray.length === 2) {
				var d = new Date;
				d.setTime(d.getTime() + 24*60*60*1000*30);
				document.cookie = 'delivery_day=' + deliveryArray[0] + ';path=/;expires=' + d.toUTCString();
				document.cookie = 'delivery_time=' + deliveryArray[1] + ';path=/;expires=' + d.toUTCString();
			}
			// Сразу пересчёт по свежим кукам/скрытым полям — не ждём admin-ajax (при сбое POST раньше не было update_order_review).
			fermaTriggerWcCheckoutUpdate();
			var data = {
				action: 'update_delivery_type',
				delivery_type: delivery_type
			};
			jQuery.post( fermaWcAjaxUrl, data, function( response ) {
				fermaTriggerWcCheckoutUpdate();
				setTimeout( function() {
					fermaTriggerWcCheckoutUpdate();
				}, 120 );
			} ).always( function() {
				fermaTriggerWcCheckoutUpdate();
				setTimeout( function() {
					jQuery( '#place_order' ).attr( 'disabled', false );
				}, 400 );
			} );
		} else {
			// Same slot selected again: still force recalc to avoid stale totals after fragment updates.
			fermaTriggerWcCheckoutUpdate();
		}
	}

});
</script>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
