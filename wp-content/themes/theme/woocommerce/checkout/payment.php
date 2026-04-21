<?php
/**
 * Checkout Payment Section
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/payment.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.3
 */

defined( 'ABSPATH' ) || exit;

$ferma_bonus_checkout_allowed = function_exists( 'ferma_checkout_bonuses_allowed' ) && ferma_checkout_bonuses_allowed();
$userbonus                      = 0;

if ( $ferma_bonus_checkout_allowed && is_user_logged_in() ) {
	$user_info = get_userdata( get_current_user_id() );
	if ( $user_info ) {
		$result = preg_replace( '/[^0-9]/', '', (string) $user_info->user_login );
		if ( strlen( $result ) >= 10 ) {
			$arr = array(
				'search_mode'  => 0,
				'search_value' => $result,
			);
			$url  = 'https://bonus.kilbil.ru/load/searchclient?h=666c13d171b01d80b04e590794a968b7';
			$curl = curl_init( $url );
			curl_setopt( $curl, CURLOPT_HEADER, false );
			curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
			curl_setopt( $curl, CURLOPT_HTTPHEADER, array( 'Content-type: application/json' ) );
			curl_setopt( $curl, CURLOPT_POST, true );
			curl_setopt( $curl, CURLOPT_POSTFIELDS, wp_json_encode( $arr ) );
			$json_response = curl_exec( $curl );
			curl_close( $curl );
			$obj = json_decode( $json_response );
			if ( $obj && isset( $obj->balance ) ) {
				$userbonus = (int) $obj->balance;
			}
		}
	}
}

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>
<div id="payment" class="woocommerce-checkout-payment">
	<?php if ( WC()->cart->needs_payment() ) : ?>
	<ul class="wc_payment_methods payment_methods methods">
		<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				echo '<li class="woocommerce-notice woocommerce-notice--info woocommerce-info">' . apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'Sorry, it seems that there are no available payment methods for your state. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce' ) : esc_html__( 'Please fill in your details above to see available payment methods.', 'woocommerce' ) ) . '</li>'; // @codingStandardsIgnoreLine
			}
			?>
	</ul>
	<?php endif; ?>


	<input type="text" style="display:none" id="textbonuses" value="<?php echo esc_attr( (string) $userbonus ); ?>">

	

	<div style="padding:1em">
		<div class="promo_code_block">
			<h5 class="promo_code_block__title">Промокод</h5>
			<div class="promo_code_block__container">
				<input type="text" class="promo_code_block__inp" placeholder="Промокод..."
					value="<?php echo (isset($_COOKIE['ferma_promo_code']) && $_COOKIE['ferma_promo_code'] != '') ? $_COOKIE['ferma_promo_code'] : ''; ?>"
					id="promo_code"
					<?php echo (isset($_COOKIE['ferma_promo_code']) && $_COOKIE['ferma_promo_code'] != '') ? 'disabled' : ''; ?>>
				<?php if ( isset( $_COOKIE['ferma_promo_code'] ) && $_COOKIE['ferma_promo_code'] != '' ) : ?>
				<button id="promo_code_remove" type="button">Удалить</button>
				<?php else : ?>
				<button id="promo_code_add" type="button">Применить</button>
				<?php endif; ?>
				<style>
					#promo_code_add,
					#promo_code_remove {
						height: 56px;
						width: 100%;
						border-radius: 12px !important;
						border: 1px solid rgba(21, 21, 21, 0.3);
						background-color: #EEEEEE;
						color: #4D4D4D;
						font-weight: var(--font-w-semibold);
						font-size: 18px;
					}
				</style>
			</div>
            <div class="q-promo-message" style="margin-top: 8px;"></div>
		</div>
		<?php if(1==2) : ?>
		<button id="registeronbonussystem" class="button" type="button">Зарегистрироваться по бонусной системе</button>
		<?php endif; ?>
		<?php if ( $ferma_bonus_checkout_allowed ) : ?>
		<p class="ferma-checkout__form-bonus">У вас бонусов:
			<?php echo (int) $userbonus; ?>
		</p>
		<?php endif; ?>
		<?php
		$cart_fees = WC()->cart->get_fees();
		foreach($cart_fees as $cart_fee) :
		?>
		<div class="order-total ferma-checkout__form-order">
			<p><?php echo $cart_fee->name; ?>:
				<?php 
				if($cart_fee->amount == 0) {
					echo 'Бесплатно';
				} else {
					echo number_format( $cart_fee->amount, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );
					echo '&nbsp;<span>руб.</span>';
				}
				?>
			</p>
		</div>
		<?php endforeach; ?>
		<div class="order-total ferma-checkout__form-order">
			<p>Сумма заказа: 
				<?php 
				$total = WC()->cart->total; 
				echo number_format( $total, wc_get_price_decimals(), wc_get_price_decimal_separator(), wc_get_price_thousand_separator() );
				?>
				<span>руб.</span>
			</p>
		</div>
		<?php
		if ( ! empty( $_POST['ball'] ) ) {
			$a = sanitize_text_field( wp_unslash( $_POST['ball'] ) );
			setcookie( 'ball', $a, time() + 3600, '/' );
			setcookie( 'ballcount', $a, time() + 3600, '/' );
		}
		?>
		<?php if ( $ferma_bonus_checkout_allowed && $userbonus > 0 ) : ?>
		<?php if ( isset( $_COOKIE['balik'] ) ) : ?>
			<style>
				#ok { display: none !important; }
				#can1 {
					display: block !important;
					background: #fff !important;
					color: #000 !important;
				}
			</style>
		<?php endif; ?>
		<?php if ( isset( $_COOKIE['vibo1r'] ) ) : ?>
			<style>
				#ok2 { display: none !important; }
				#ok23 {
					display: block !important;
					background: #fff !important;
					color: #000 !important;
				}
			</style>
		<?php endif; ?>
		<style>
		.order-total.ferma-checkout__form-order { margin-bottom: 20px; }
		#payment #ok,
		#payment #can1,
		#payment #ok2,
		#payment #ok23 {
			display: flex;
			justify-content: center;
			align-items: center;
			border: 1px solid var(--color-green) !important;
			border-radius: 12px;
			background-color: var(--color-green) !important;
			padding: 13px 40px;
			font-size: 18px !important;
			font-weight: var(--font-w-bold);
			color: var(--color-white) !important;
			text-transform: uppercase;
			transition: ease-in-out .3s;
			cursor: pointer;
		}
		#payment #ok2,
		#payment #ok23 { margin-left: 1em; }
		#payment #ok:hover,
		#payment #can1:hover,
		#payment #ok2:hover,
		#payment #ok23:hover {
			background-color: #fff !important;
			color: var(--color-green) !important;
		}
		</style>
		<script>
			(function () {
				var ok = document.querySelector("#ok");
				var ok2 = document.querySelector("#ok2");
				var can1 = document.querySelector("#can1");
				var ok23 = document.querySelector("#ok23");
				if (ok) {
					ok.onclick = function () {
						document.cookie = "balik=" + document.getElementById("ballcount").value + ";path=/";
						jQuery(document.body).trigger("update_checkout");
					};
				}
				if (ok2) {
					ok2.onclick = function () {
						document.cookie = "vibo1r=" + 1 + ";path=/";
						jQuery(document.body).trigger("update_checkout");
					};
				}
				if (can1) {
					can1.onclick = function () {
						document.cookie = "balik=" + 0 + ";path=/;expires=Thu, 01 Jan 1970 00:00:01 GMT";
						jQuery(document.body).trigger("update_checkout");
					};
				}
				if (ok23) {
					ok23.onclick = function () {
						document.cookie = "vibo1r=" + 1 + ";path=/;expires=Thu, 01 Jan 1970 00:00:01 GMT";
						jQuery(document.body).trigger("update_checkout");
					};
				}
			})();
		</script>
		<div style="display:flex;margin-left:1em;">
			<input type="hidden" name="option" value="1">
			<input type="hidden" id="ballcount" name="ball" value="<?php
				global $woocommerce;
				if ( $userbonus < ferma_calc_percent( $woocommerce->cart->total, 30 ) ) {
					echo esc_attr( (string) $userbonus );
				} else {
					echo esc_attr( (string) ferma_calc_percent( $woocommerce->cart->total, 30 ) );
				}
			?>">
			<button type="button" id="ok" class="buttonbonus">ПОТРАТИТЬ <?php
				global $woocommerce;
				if ( $userbonus < ferma_calc_percent( $woocommerce->cart->total, 30 ) ) {
					echo (int) $userbonus;
				} else {
					echo (int) ferma_calc_percent( $woocommerce->cart->total, 30 );
				}
			?></button>
			<button type="button" class="buttonbonus" id="can1" style="display:none;">СПИСАТЬ</button>
			<input type="hidden" name="option" value="2">
			<button type="button" class="buttonbonus" id="ok2" style="margin-left:1em;">КОПИТЬ</button>
			<button type="button" class="buttonbonus" id="ok23" style="margin-left:1em;display:none">КОПИТЬ</button>
		</div>
		<?php endif; ?>

	<!--<div class="promocodes" style="display:flex;gap:5px;">
	<input type="text" id="promo_count" placeholder="Промокод..">
	<button id="promo_play" type="button" class="button">Применить</button>
	</div> !-->

	</div>

	<div class="place-order ferma-checkout__form-place">


		<?php
		$user_id = get_current_user_id();
		if ( is_user_logged_in() ) {
			$args_check = get_user_meta( $user_id, 'samovivoz', true );
		} else {
			$args_check = isset( $_COOKIE['market'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['market'] ) ) : '';
		}
		?>

		<noscript>
			<?php
			/* translators: $1 and $2 opening and closing emphasis tags respectively */
			printf( esc_html__( 'Since your browser does not support JavaScript, or it is disabled, please ensure you click the %1$sUpdate Totals%2$s button before placing your order. You may be charged more than the amount stated above if you fail to do so.', 'woocommerce' ), '<em>', '</em>' );
			?>
			<br /><button type="submit" class="ferma-checkout__form-submit" name="woocommerce_checkout_update_totals"
				value="<?php esc_attr_e( 'Update totals', 'woocommerce' ); ?>"><?php esc_html_e( 'Update totals', 'woocommerce' ); ?></button>
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<p class="ferma-checkout__form-privacy">
			Оформляя заказ, Вы соглашаетесь с условиями <a href="https://ferma-dv.ru/privacy/">политики конфиденциальности</a> 
			и <a href="https://ferma-dv.ru/agreement/">правилами продажи</a>.
		</p>

		<div class="ferma-checkout-submit-anchor">
			<div class="ferma-checkout-inline-notices" role="alert" aria-live="assertive"></div>
			<?php
			echo apply_filters(
				'woocommerce_order_button_html',
				'<button type="submit" class="ferma-checkout__form-submit" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr( $order_button_text ) . '" data-value="' . esc_attr( $order_button_text ) . '">' . esc_html( $order_button_text ) . '</button>'
			);
			?>
		</div>
		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>

	</div>

</div>
</div>

<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}

?>
<script>
	var myEl = document.getElementById('registeronbonussystem');
	if (myEl) {
		myEl.addEventListener('click', function () {
			var a = document.getElementById("billing_phone").value;
			var c = document.getElementById("billing_first_name").value;

			if (a == null || a == "") {
				alert("Поле номера пустое");
				return false;
			}
			if (c == null || c == "") {
				alert("Поле имя пустое");
				return false;
			} else {
				$.ajax({
					url: '/wp-content/themes/theme/create_acc.php',
					method: 'post',
					dataType: 'html',
					data: {
						phone: a,
						name: c
					},
					success: function (data) {
						alert("успешно отправлено");
					}
				});
			}

		}, false);
	}
</script>
<script>
	function getCookie(c_name) {
		if (document.cookie.length > 0) {
			c_start = document.cookie.indexOf(c_name + "=");
			if (c_start != -1) {
				c_start = c_start + c_name.length + 1;
				c_end = document.cookie.indexOf(";", c_start);
				if (c_end == -1) {
					c_end = document.cookie.length;
				}
				return unescape(document.cookie.substring(c_start, c_end));
			}
		}
		return "";
	}
	a = getCookie("balik");
	document.getElementById('billing_bonus').value = a;
</script>
<script>
	function getFermaCookie(name) {
		var v = document.cookie.match('(^|;) ?' + name + '=([^;]*)(;|$)');
		return v ? v[2] : null;
	}

	function setFermaCookie(name, value, days) {
		var d = new Date;
		d.setTime(d.getTime() + 24 * 60 * 60 * 1000 * days);
		document.cookie = name + "=" + value + ";path=/;expires=" + d.toGMTString();
	}

	function deleteFermaCookie(name) {
		setFermaCookie(name, '', -1);
	}
	$(document).on('click', '#promo_code_add', function () {
		const ferma_promo_code = $('#promo_code').val();
		if (typeof ferma_promo_code !== "undefined" && ferma_promo_code != '') {
			setFermaCookie('ferma_promo_code', ferma_promo_code, '30');
			$('body').trigger('update_checkout');
		}
	});

	$(document).on('click', '#promo_code_remove', function () {
		deleteFermaCookie('ferma_promo_code');
		$('body').trigger('update_checkout');
	});

	$("#promo_code_but__old").click(function () {
		$.ajax({
			url: '/wp-content/themes/theme/promo_code.php',
			/* Куда пойдет запрос */
			method: 'post',
			/* Метод передачи (post или get) */
			dataType: 'html',
			/* Тип данных в ответе (xml, json, script, html). */
			data: {
				count: "Работает",
				result: document.getElementById("promo_code").value
			},
			/* Параметры передаваемые в запросе. */
			success: function (data) {
				/* функция которая будет выполнена после успешного запроса.  */
				console.log(data);
				var data = JSON.parse(data);
				if (data.success == 1) {
					$('body').trigger('update_checkout');
				}
			}
		});
	});
    jQuery(function($) {

        function movePromoNotice() {
            var $title  = $('.promo_code_block__title').first();
            var $notice = $('.woocommerce-NoticeGroup-updateOrderReview');

            if ($title.length && $notice.length) {
                $notice.insertAfter($title);
            }
        }

        // при первом рендере
        movePromoNotice();

        // при каждом ajax-обновлении чекаута/промокода
        $(document.body).on('updated_checkout applied_coupon removed_coupon', function() {
            movePromoNotice();
        });
    });

</script>
<script>
	$("#promo_play").click(function () {
		var a = document.getElementById("promo_count").value;
		$.ajax({
			url: '/wp-content/themes/theme/promo.php',
			/* Куда пойдет запрос */
			method: 'post',
			/* Метод передачи (post или get) */
			dataType: 'html',
			/* Тип данных в ответе (xml, json, script, html). */
			data: {
				count: a
			},
			/* Параметры передаваемые в запросе. */
			success: function (data) {
				/* функция которая будет выполнена после успешного запроса.  */
				alert(data);
				/* В переменной data содержится ответ от index.php. */
			}
		});
	});
</script>
<?php