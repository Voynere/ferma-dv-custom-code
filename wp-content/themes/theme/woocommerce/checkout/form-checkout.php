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
var current_delivery_value = '',
	updated_times = false;
jQuery(document).ready(function() {
	// Не блокируем select времени на update_checkout:
	// disabled-поля не сериализуются и новое время доставки не уходит в Woo.
	
	$(document).on('change', 'select[name="billing_asdx1"]', function() {
		let delivery_type = $(this).find(':selected').data('value');
		update_delivery_type(delivery_type);
	});
	
	//$('#billing_asdx1').attr('disabled', true);
	//$('#billing_asdx1').remove();
	if(!updated_times) {
		ferma_update_times();
	}
	function fermaSetBillingTimeError( message ) {
		var msg = message || 'Не удалось определить время доставки.';
		var $w = $('#billing_asdx1_field .woocommerce-input-wrapper');
		if ( !$w.length ) {
			return;
		}
		$w.find( '.ferma-delivery-time-error' ).remove();
		if ( !$w.find( 'select#billing_asdx1' ).length ) {
			$w.html( '<select name="billing_asdx1" id="billing_asdx1" class="woodefaults" aria-describedby="ferma-billing-time-error"></select>' );
		}
		$( '#billing_asdx1' )
			.empty()
			.append( jQuery( '<option value=""></option>' ).text( '—' ) )
			.prop( 'disabled', true );
		$w.append( jQuery( '<p class="ferma-delivery-time-error" id="ferma-billing-time-error" role="alert"></p>' ).text( msg ) );
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
				$('#billing_asdx1').attr('disabled', true);
				//$('#billing_asdx1_field').hide();
			},
			success: function(data) {
				window.updated_times = true;
				if(!data.success) {
					var emsg = ( data && data.data && data.data.error ) ? data.data.error : 'Введите верный адрес доставки.';
					fermaSetBillingTimeError( emsg );
				} else {
					$( '#billing_asdx1_field .ferma-delivery-time-error' ).remove();
					$('#billing_sss').val(data.data.market);
					$('#billing_point').val(data.data.id);
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
						$.each(data.data.today, function(type, price) {
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
						$.each(data.data.tomorrow, function(type,price) {
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
					
					$('#billing_asdx1').html(choices);
					$('#billing_asdx1').attr('disabled', false);
					if(default_delivery == '') {
						const default_delivery_value = $('#billing_asdx1').find(':selected').data('value');
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
			$('#place_order').attr('disabled', true);
			var deliveryArray = (delivery_type || '').split('_');
			if (deliveryArray.length === 2) {
				var d = new Date;
				d.setTime(d.getTime() + 24*60*60*1000*30);
				document.cookie = 'delivery_day=' + deliveryArray[0] + ';path=/;expires=' + d.toGMTString();
				document.cookie = 'delivery_time=' + deliveryArray[1] + ';path=/;expires=' + d.toGMTString();
			}
			var data = {
				action: 'update_delivery_type',
				delivery_type: delivery_type
			};
			
			jQuery.post( woocommerce_params.ajax_url, data, function( response )
			{
				// Обновляем order review после подтверждения сервера.
				$('body').trigger( 'update_checkout' );
				if (typeof window.wc_checkout_form !== 'undefined' && typeof window.wc_checkout_form.update_checkout === 'function') {
					window.wc_checkout_form.update_checkout();
				}
				// Дополнительный догоняющий пересчёт на случай гонки cookie/update_order_review.
				setTimeout(function() {
					$('body').trigger( 'update_checkout' );
					if (typeof window.wc_checkout_form !== 'undefined' && typeof window.wc_checkout_form.update_checkout === 'function') {
						window.wc_checkout_form.update_checkout();
					}
				}, 120);
				setTimeout(() => {
				  $('#place_order').attr('disabled', false);
				}, 2000);
			});
		}
	}

});
</script>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
