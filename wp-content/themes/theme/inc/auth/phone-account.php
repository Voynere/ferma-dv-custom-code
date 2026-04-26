<?php
/**
 * Account phone edit flow and handoff token helpers.
 *
 * @package Theme
 */

/**
 * Одноразовый подписанный токен для сценария «смена телефона → snemanomera cookie → повторная установка сессии».
 * Заменяет небезопасную передачу сырого user ID в cookie (подделка ID).
 */
function ferma_snemanomera_handoff_secret_key() {
	return apply_filters(
		'ferma_snemanomera_handoff_secret_key',
		( function_exists( 'wp_salt' ) ? wp_salt( 'secure_auth' ) : '' ) . 'ferma_snemanomera_v1'
	);
}

/**
 * @param int $user_id ID пользователя, уже прошедшего проверку на сервере.
 * @return string Токен (base64url) или пустая строка.
 */
function ferma_snemanomera_handoff_issue( $user_id ) {
	$user_id = (int) $user_id;
	if ( $user_id < 1 ) {
		return '';
	}
	$expiry  = time() + 120;
	$data    = $user_id . '|' . $expiry;
	$sig     = hash_hmac( 'sha256', $data, ferma_snemanomera_handoff_secret_key() );
	$payload = $data . '|' . $sig;
	return rtrim( strtr( base64_encode( $payload ), '+/', '-_' ), '=' );
}

/**
 * @param string $token Значение из cookie (возможно url-encoded).
 * @return int user_id или 0.
 */
function ferma_snemanomera_handoff_validate( $token ) {
	if ( ! is_string( $token ) || $token === '' ) {
		return 0;
	}
	$token = rawurldecode( $token );
	$b64   = strtr( $token, '-_', '+/' );
	$pad   = strlen( $b64 ) % 4;
	if ( $pad ) {
		$b64 .= str_repeat( '=', 4 - $pad );
	}
	$decoded = base64_decode( $b64, true );
	if ( ! $decoded || substr_count( $decoded, '|' ) !== 2 ) {
		return 0;
	}
	$parts = explode( '|', $decoded, 3 );
	if ( count( $parts ) !== 3 ) {
		return 0;
	}
	list( $uid, $expiry, $sig ) = $parts;
	$uid                        = (int) $uid;
	$expiry                     = (int) $expiry;
	if ( $uid < 1 || $expiry < time() ) {
		return 0;
	}
	$data     = $uid . '|' . $expiry;
	$expected = hash_hmac( 'sha256', $data, ferma_snemanomera_handoff_secret_key() );
	if ( ! hash_equals( $expected, $sig ) ) {
		return 0;
	}
	// Одноразовое использование (защита от повторной подстановки cookie).
	$use_key = 'ferma_snem_' . md5( $sig );
	if ( get_transient( $use_key ) ) {
		return 0;
	}
	set_transient( $use_key, 1, 5 * MINUTE_IN_SECONDS );
	return $uid;
}

/**
 * Returns sanitized billing phone from POST payload.
 *
 * @return string
 */
function ferma_account_posted_billing_phone() {
	if ( ! isset( $_POST['billing_phone'] ) ) {
		return '';
	}

	return sanitize_text_field( wp_unslash( $_POST['billing_phone'] ) );
}

/**
 * Checks whether phone-change cookie flag is enabled.
 *
 * @return bool
 */
function ferma_account_phone_change_cookie_enabled() {
	if ( ! isset( $_COOKIE['snemanomera1'] ) ) {
		return false;
	}

	return (string) wp_unslash( $_COOKIE['snemanomera1'] ) === '1';
}

// Display the mobile phone field.
// add_action( 'woocommerce_edit_account_form_start', 'add_billing_mobile_phone_to_edit_account_form' ); // At start
add_action( 'woocommerce_edit_account_form', 'add_billing_mobile_phone_to_edit_account_form' ); // After existing fields
function add_billing_mobile_phone_to_edit_account_form() {
	$user = wp_get_current_user();
	?>


	 <div class="form-row page-account__editForm-number">
		<script src="https://unpkg.com/imask"></script>
		<label for="billing_phone"><?php _e( 'Номер телефона', 'woocommerce' ); ?> <span
				class="required">*</span></label>
		<input placeholder="Телефон" type="text" class="woocommerce-Input woocommerce-Input--phone" name="billing_phone" id="billing_phone" value="<?php echo esc_attr( $user->billing_phone ); ?>" disabled />
		<script>
			function izmena() {
				var inputs = document.getElementsByTagName('input');
				for (i = 0; i < inputs.length; i++) {
					inputs[i].disabled = false;
				}

				var a = FindByAttributeValue("for", "billing_phone");
				a.innerHTML = 'Введите новый номер телефона <span class="required">*</span>';
				var b = FindByAttributeValue("name", "save_account_details");
				b.disabled = true;
				$("#smenanomera").hide();
				$("#smenanomera2").show();

				function FindByAttributeValue(attribute, value, element_type) {
					element_type = element_type || "*";
					var All = document.getElementsByTagName(element_type);
					for (var i = 0; i < All.length; i++) {
						if (All[i].getAttribute(attribute) == value) {
							return All[i];
						}
					}
				}
			}
		</script>
		<style>
			#smenanomera2,
			#smenanomera,
			#smenanomera3 {
				font-size: 100%;
				margin: 0;
				line-height: 1;
				cursor: pointer;
				position: relative;
				text-decoration: none;
				overflow: visible;
				padding: 16px 32px;
				font-weight: 700;
				border-radius: 12px;
				left: auto;
				color: #ffffff;
				background-color: var(--color-green);
				border: 0;
				display: inline-block;
				background-image: none;
				box-shadow: none;
				text-shadow: none;
			transition: ease-in-out .2s;
			}
		#smenanomera2,
			#smenanomera,
			#smenanomera3:hover {
			background-color: rgba(79,189,1,.85);
		}
		</style>
		<button class="btn-green" type="button" onclick="izmena()" id="smenanomera">Изменить</button>
		<button class="btn-green" type="button" style="display:none" id="smenanomera2">Применить</button>
		<button class="btn-green" type="button" style="display:none" id="smenanomera3">Изменить</button>
		<input type="hidden" id="id_user" value="<?$cur_user_id = get_current_user_id(); echo $cur_user_id;?>">








		<script>
			var e = FindByAttributeValue("id", "billing_phone");

			$("#smenanomera2").on("click", function () {
				$.ajax({
					url: '/wp-content/themes/theme/obrabotka.php',
					method: 'post',
					dataType: 'html',
					data: {
						text: e.value
					},
					success: function (data) {
						var jsonData = JSON.parse(data);
						if (jsonData.error == 1) {
							document.getElementById("ajaxresult").innerHTML =
								'<p>Вы неверно ввели номер</p>';
						} else {
							document.cookie = "snemanomera1=1;path=/;max-age=30;";
							var number = FindByAttributeValue("name", "billing_phone").value;
							document.getElementById("ajaxresult").innerHTML =
								'<input type="hidden" id="code_telephone" name="code_telephone" value="' +
								jsonData.code +
								'" ><input type="hidden" id="telephone" name="telephone" value="' +
								number + '" >';
							var g = FindByAttributeValue("name", "billing_phone");
							g.value = "";
							var a = FindByAttributeValue("for", "billing_phone");
							a.innerHTML =
								'ВВЕДИТЕ ПОСЛЕДНИЕ 4 ЦИФРЫ ЗВОНЯЩЕГО НОМЕРА <span class="required">*</span>';
							$("#smenanomera").hide();
							$("#smenanomera2").hide();
							$("#smenanomera3").show();
						}



					}
				});
			});

			function FindByAttributeValue(attribute, value, element_type) {
				element_type = element_type || "*";
				var All = document.getElementsByTagName(element_type);
				for (var i = 0; i < All.length; i++) {
					if (All[i].getAttribute(attribute) == value) {
						return All[i];
					}
				}
			}
		</script>

		<script>
			$("#smenanomera3").on("click", function () {
				var e = FindByAttributeValue("id", "billing_phone");
				var k = document.getElementById("code_telephone");
				$.ajax({
					url: '/wp-content/themes/theme/obrabotka1.php',
					method: 'post',
					dataType: 'html',
					data: {
						text: e.value,
						code: k.value
					},
					success: function (data) {
						var jsonData = JSON.parse(data);
						if (jsonData.success == 0) {
							document.getElementById("ajaxresult").innerHTML +=
								'<p>Вы ввели неверный код</p>';
					} else {
						if (!jsonData.handoff) {
							document.getElementById("ajaxresult").innerHTML +=
								'<p>Не удалось выдать сессию. Обновите страницу и повторите ввод кода.</p>';
							return;
						}
						document.cookie = "snemanomera=" + encodeURIComponent(jsonData.handoff) + ";path=/;max-age=120;SameSite=Lax";
						var g = FindByAttributeValue("name", "billing_phone");
							var m = FindByAttributeValue("id", "telephone").value;
							g.value = m;
							document.getElementById("ajaxresult").innerHTML +=
								'<p>Вы успешно прошли изменение номера</p>';
							$("#smenanomera2").hide();
							var b = FindByAttributeValue("name", "save_account_details");
							b.disabled = false;
							IMask(
								document.getElementById('billing_phone'), {
									mask: '+{7}(000)0000000'
								});
							FindByAttributeValue("name", "save_account_details").click();
						}

					}
				});
			});

			function FindByAttributeValue(attribute, value, element_type) {
				element_type = element_type || "*";
				var All = document.getElementsByTagName(element_type);
				for (var i = 0; i < All.length; i++) {
					if (All[i].getAttribute(attribute) == value) {
						return All[i];
					}
				}
			}
		</script>
		<div id="ajaxresult" class="ajaxresult">

		</div>
		<script>
		</script>
	 </div>
	<?php
}

// Check and validate the mobile phone.
add_action( 'woocommerce_save_account_details_errors', 'billing_mobile_phone_field_validation', 20, 1 );
function billing_mobile_phone_field_validation( $args ) {
	if ( isset( $_POST['billing_phone'] ) && '' === ferma_account_posted_billing_phone() ) {
		$args->add( 'error', __( 'Please fill in your Mobile phone', 'woocommerce' ), '' );
	}
}

// Save the mobile phone value to user data.
add_action( 'woocommerce_save_account_details', 'my_account_saving_billing_mobile_phone', 20, 1 );
function my_account_saving_billing_mobile_phone( $user_id ) {
	$billing_phone = ferma_account_posted_billing_phone();
	if ( '' !== $billing_phone ) {
		update_user_meta( $user_id, 'billing_phone', $billing_phone );
	}
	if ( ferma_account_phone_change_cookie_enabled() ) {
		global $wpdb;
		$cur_user_id = get_current_user_id();
		$wpdb->update(
			$wpdb->users,
			array(
				'user_login'   => $billing_phone,
				'display_name' => $billing_phone,
			),
			array( 'ID' => $cur_user_id )
		);
	}
}
