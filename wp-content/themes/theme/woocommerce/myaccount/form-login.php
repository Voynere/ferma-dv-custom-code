<?php
/**
 * Login Form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/form-login.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Гарантируем старт сессии для хранения кодов
if ( ! session_id() && ! headers_sent() ) {
    session_start();
}

do_action( 'woocommerce_before_customer_login_form' ); ?>

    <style>
        .ferma-login-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .ferma-login-buttons .btn-green {
            width: 100%;
            text-align: center;
            text-decoration: none;
            display: block;
        }
        .ferma-login-buttons .btn-outline-green {
            width: 100% !important;
            text-align: center !important;
            text-decoration: none !important;
            display: block !important;
            font-size: 100% !important;
            margin: 0 !important;
            line-height: 1 !important;
            cursor: pointer !important;
            padding: .618em 1em !important;
            font-weight: 700 !important;
            border-radius: 3px !important;
            color: #4fbd01 !important;
            background-color: #fff !important;
            background-image: none !important;
            border: #4fbd01 !important;
            box-shadow: none !important;
            text-shadow: none !important;
            transition: 0.3s;
        }
        .ferma-login-buttons .btn-outline-green:hover {
            background-color: #4fbd01 !important;
            color: #fff !important;
        }
    </style>

    <h2 id="vhods" class="page-account__entry"><?php esc_html_e( 'Login', 'woocommerce' ); ?></h2>

    <div id="choose" class="page-account__entry-buttons">
        <button onclick="location.href = 'https://ferma-dv.ru/my-account?login=number';" class="woocommerce-form-login__submit page-account__entry-btn btn-green">Вход по номеру</button>
        <button onclick="location.href = 'https://ferma-dv.ru/my-account?login=email';" class="woocommerce-form-login__submit page-account__entry-btn btn-green">Вход по email</button>
    </div>


<?php
if ( isset( $_COOKIE['snemanomera'] ) ) {
	nocache_headers();
	wp_clear_auth_cookie();
	$raw = isset( $_COOKIE['snemanomera'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['snemanomera'] ) ) : '';
	$uid = 0;
	if ( $raw !== '' && function_exists( 'ferma_snemanomera_handoff_validate' ) ) {
		$uid = (int) ferma_snemanomera_handoff_validate( $raw );
	}
	$cookie_path = defined( 'COOKIEPATH' ) && COOKIEPATH ? COOKIEPATH : '/';
	$cookie_dom  = defined( 'COOKIE_DOMAIN' ) ? COOKIE_DOMAIN : '';
	if ( $uid > 0 && get_userdata( $uid ) ) {
		wp_set_auth_cookie( $uid, true );
		setcookie( 'snemanomera', '', time() - 3600, $cookie_path, $cookie_dom, is_ssl(), true );
		header( 'Location: https://ferma-dv.ru/my-account/edit-account/' );
		exit;
	}
	setcookie( 'snemanomera', '', time() - 3600, $cookie_path, $cookie_dom, is_ssl(), true );
}
?>

<?php
// ========== БЛОК: Проверка кода из email (шаг 3) ==========
if (isset($_POST['code'])) { ?>
    <style>
        #customer_login1 { display: none !important; }
        #customer_email1 { display: none !important; }
        #customer_email2 { display: none !important; }
    </style>
    <div class="u-columns col2-set" id="customer_email3">
        <div class="u-column1 col-1">
            <?php
            global $wpdb;
            if($_POST['code'] == $_SESSION['code']) {
                unset($_SESSION['code']);
                echo "Вы ввели верный код - Сейчас произойдет редирект на ваш аккаунт";
                $hash = wp_hash_password( $_POST['code'] );
                if ( email_exists( $_POST['email'] ) ) {
                    $data = get_user_by('email', $_POST['email']);
                    nocache_headers();
                    wp_clear_auth_cookie();
                    wp_set_auth_cookie( $data->ID );
                    update_user_meta( $data->ID, 'last_login', time() );
                    header("Location: https://ferma-dv.ru/my-account/");
                    exit;
                } else {
                    $wpdb->insert(
                        'wp_users',
                        array( 'user_login' => $_POST['email'], 'user_pass' => $hash, 'user_email' => $_POST['email'], 'user_market' => $_COOKIE["market"], 'user_nicename' => $_POST['email'], 'user_url' => 'none', 'display_name' => $_POST['email']),
                        array( '%s', '%s' )
                    );
                    $data = get_user_by('email', $_POST['email']);
                    nocache_headers();
                    wp_clear_auth_cookie();
                    wp_set_auth_cookie( $data->ID );
                    update_user_meta( $data->ID, 'last_login', time() );
                    header("Location: https://ferma-dv.ru/my-account/");
                    exit;
                }
            } else {
                echo "Вы ввели не верный код попробуйте еще раз - редирект произойдет через 3 секунды";
                ?>
                <script>
                    setTimeout(function () { window.history.go(-2); }, 3000);
                </script>
                <?php
            }
            ?>
        </div>
    </div>
<?php } ?>


<?php
// ========== БЛОК: Ввод кода из email (шаг 2) ==========
// Пришёл POST с email, но ещё без code — генерируем код и отправляем письмо
if (isset($_POST['email']) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL) && !isset($_POST['code'])) {

    // Генерируем код и отправляем на почту
    $_SESSION['code'] = rand(1000, 9999);

    $to      = sanitize_email($_POST['email']);
    $subject = 'Код для входа — Ферма ДВ';
    $message = '<div style="font-family:Arial,sans-serif;max-width:480px;margin:0 auto;padding:20px;">'
        . '<h2 style="color:#4fbd01;">Ферма ДВ</h2>'
        . '<p>Ваш код для входа:</p>'
        . '<p style="font-size:32px;font-weight:bold;letter-spacing:6px;color:#333;">' . $_SESSION['code'] . '</p>'
        . '<p style="color:#888;font-size:13px;">Если вы не запрашивали вход — просто проигнорируйте это письмо.</p>'
        . '</div>';
    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: Ферма ДВ <noreply@ferma-dv.ru>',
    ];

    wp_mail($to, $subject, $message, $headers);

    ?>
    <style>
        #customer_login1 { display: none !important; }
        #customer_email1 { display: none !important; }
    </style>

    <div class="page-account__login" id="customer_email2">
        <div class="page-account__login-inner">
            <form class="woocommerce-form woocommerce-form-login login" method="post">
                <div class="page-account__wrapper-login">
                    <?php do_action( 'woocommerce_login_form_start' ); ?>

                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="code">Введите код из письма&nbsp;<span class="required">*</span></label>
                        <input type="number" class="woocommerce-Input woocommerce-Input--text input-text" name="code" id="code" autocomplete="username" value="">
                        <input type="hidden" name="email" value="<?php echo esc_attr($_POST['email']); ?>">
                    </p>
                    <?php do_action( 'woocommerce_login_form' ); ?>

                    <p class="account-privacy">
                        Регистрируясь на сайте, Вы соглашаетесь с условиями <a href="https://ferma-dv.ru/privacy/">политики конфиденциальности</a> и&nbsp;<a href="https://ferma-dv.ru/agreement/">правилами продажи</a>.
                    </p>

                    <p class="form-row">
                        <button type="submit" class="woocommerce-form-login__submit btn-green" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
                    </p>

                    <?php do_action( 'woocommerce_login_form_end' ); ?>
                </div>
            </form>
        </div>
    </div>
<?php } ?>


<?php
// ========== БЛОК: Форма ввода email (шаг 1) ==========
if (isset($_GET['login']) && $_GET['login'] == "email") {
    // Код НЕ генерируем здесь — он будет создан при POST-отправке формы (шаг 2)
    ?>
    <style>
        #customer_login1 { display: none !important; }
        #choose { display: none !important; }
    </style>

    <div class="page-account__login" id="customer_email1">
        <div class="page-account__login-inner">
            <form class="woocommerce-form woocommerce-form-login login" method="post">
                <?php do_action( 'woocommerce_login_form_start' ); ?>
                <div class="page-account__wrapper-login">

                    <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                        <label for="email">Ваш email&nbsp;<span class="required">*</span></label>
                        <input type="email" class="woocommerce-Input woocommerce-Input--text input-text account-email" name="email" id="email" autocomplete="username" value="" pattern="[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}" required>
                        <span id="email-error" style="color:red; display:none; margin-top:5px; font-size:13px;"></span>
                    </p>

                    <?php do_action( 'woocommerce_login_form' ); ?>

                    <div class="form-row" style="display:block;">
                        Регистрируясь на сайте, Вы соглашаетесь с условиями <a href="https://ferma-dv.ru/privacy/">политики конфиденциальности</a> и&nbsp;<a href="https://ferma-dv.ru/agreement/">правилами продажи</a>.
                    </div>

                    <div class="form-row ferma-login-buttons">
                        <button type="submit" id="email_suc" class="woocommerce-form-login__submit page-account__entry-btn btn-green" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
                        <a href="https://ferma-dv.ru/my-account?login=number" class="btn-outline-green">Войти по номеру телефона</a>
                    </div>

                    <?php do_action( 'woocommerce_login_form_end' ); ?>
                </div>
            </form>

            <script>
                (function() {
                    var emailInput = document.getElementById('email');
                    var errorEl = document.getElementById('email-error');
                    if (!emailInput || !errorEl) return;
                    var form = emailInput.closest('form');

                    emailInput.addEventListener('input', function() {
                        this.value = this.value.replace(/[а-яА-ЯёЁ]/g, '');
                        if (this.value === '') {
                            errorEl.style.display = 'none';
                        } else if (!isValidEmail(this.value)) {
                            errorEl.textContent = 'Введите корректный email (например: name@mail.ru)';
                            errorEl.style.display = 'block';
                        } else {
                            errorEl.style.display = 'none';
                        }
                    });

                    emailInput.addEventListener('paste', function(e) {
                        var text = (e.clipboardData || window.clipboardData).getData('text');
                        if (/[а-яА-ЯёЁ]/.test(text)) {
                            e.preventDefault();
                            this.value = text.replace(/[а-яА-ЯёЁ]/g, '');
                        }
                    });

                    form.addEventListener('submit', function(e) {
                        if (!isValidEmail(emailInput.value)) {
                            e.preventDefault();
                            errorEl.textContent = 'Введите корректный email (например: name@mail.ru)';
                            errorEl.style.display = 'block';
                            emailInput.focus();
                        }
                    });

                    function isValidEmail(email) {
                        return /^[a-zA-Z0-9._%+\-]+@[a-zA-Z0-9.\-]+\.[a-zA-Z]{2,}$/.test(email);
                    }
                })();
            </script>
        </div>
    </div>

<?php } ?>


<?php
// ========== БЛОК: Вход по номеру ==========
if (isset($_GET['login']) && $_GET['login'] == "number") { ?>
    <style>
        #choose { display: none !important; }
    </style>

    <?php
// --- Шаг 3: Проверка СМС-кода ---
    if(!empty($_POST["tel_code"])) {
        global $wpdb;
        $user = htmlspecialchars($_POST["telephone1"]);
        $telecod = htmlspecialchars($_POST["tel_code"]);

        if($telecod == $_SESSION['phone_code']) {
            unset($_SESSION['phone_code']);
            echo "Вы ввели верный код - Сейчас произойдет редирект на ваш аккаунт";
            $hash = wp_hash_password( $telecod );
            if ( username_exists( $user ) ) {
                $data = get_user_by('login', $user);
                wp_set_auth_cookie( $data->ID );
                update_user_meta( $data->ID, 'billing_phone', sanitize_text_field($_POST['telephone1']) );
                update_user_meta( $data->ID, 'last_login', time() );
                header("Location: https://ferma-dv.ru/my-account/");
                exit;
            } else {
                $wpdb->insert(
                    'wp_users',
                    array( 'user_login' => $user, 'user_pass' => $hash, 'user_market' => $_COOKIE["market"], 'user_nicename' => $user, 'user_url' => 'none', 'display_name' => $user),
                    array( '%s', '%s' )
                );
                $data = get_user_by('login', $user);
                wp_set_auth_cookie( $data->ID );
                update_user_meta( $data->ID, 'billing_phone', sanitize_text_field($_POST['telephone1']) );
                update_user_meta( $data->ID, 'last_login', time() );
                header("Location: https://ferma-dv.ru/my-account/");
                exit;
            }
        } else {
            echo "Вы ввели не верный код попробуйте еще раз - редирект произойдет через 3 секунды";
            ?>
            <script>setTimeout(function () { window.history.go(-2); }, 3000);</script>
            <?php
        }
        die();
    }
    ?>

    <?php
// --- Шаг 2: Ввод СМС-кода ---
    if(!empty($_POST["telephone"])) {
        $code_tel = rand(1000,9999);
        $_SESSION['phone_code'] = $code_tel;
        $user = htmlspecialchars($_POST["telephone"]);
        $ch = curl_init("https://sms.ru/sms/send");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array(
            "api_id" => "8EC86059-F03F-AE01-8A33-3F8443B51BC4",
            "to" => $user,
            "msg" => "Ваш код: " . $code_tel,
            "from" => "fermaDV",
            "json" => 1
        )));
        $body = curl_exec($ch);
        curl_close($ch);
        $json = json_decode($body);
        ?>
        <style>
            #customer_login1 { display: none !important; }
            #vhods { display: none !important; }
        </style>

        <script src="https://unpkg.com/imask"></script>
        <?php do_action( 'woocommerce_login_form_start' ); ?>
        <div class="page-account__wrapper-login">
            <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide page-account__wrapper-login-head">
                <label for="tel_code">ВВЕДИТЕ <b>КОД</b> ОТПРАВЛЕННЫЙ НА ВАШ НОМЕР ТЕЛЕФОНА&nbsp;<span class="required" style="color:red">*</span></label>
                <input type="text" class="required lwp_scode" name="tel_code" placeholder="ـ ـ ـ ـ">
                <input type="hidden" name="telephone1" value="<?php echo esc_attr($user); ?>">
            </p>

            <?php do_action( 'woocommerce_login_form' ); ?>

            <div id="resend-block" style="display:none; margin-top: 10px;">
                <p id="sub2" style="margin-bottom: 0.5em !important; color: #666;">Не пришло СМС?</p>
                <button type="button" id="submitcode1" disabled style="
		width: 100%;
		padding: 12px;
		font-size: 14px;
		font-weight: 700;
		border-radius: 3px;
		border: 2px solid #333;
		background: #fff;
		color: #999;
		cursor: not-allowed;
		margin-bottom: 0.5em;
		transition: 0.3s;
	">Отправить ещё раз (<span id="resend-timer">60</span> сек)</button>
            </div>
            <p id="sub3" style="display:none; margin-bottom: 0.5em !important; color: #6ba802; font-weight: 700;">СМС отправлен</p>

            <script>
                (function() {
                    var seconds = 60;
                    var timerEl = document.getElementById('resend-timer');
                    var btn = document.getElementById('submitcode1');
                    var block = document.getElementById('resend-block');

                    // Показываем блок через 5 секунд
                    setTimeout(function() {
                        block.style.display = 'block';
                    }, 5000);

                    // Обратный отсчёт
                    var interval = setInterval(function() {
                        seconds--;
                        if (timerEl) timerEl.textContent = seconds;

                        if (seconds <= 0) {
                            clearInterval(interval);
                            btn.disabled = false;
                            btn.style.cursor = 'pointer';
                            btn.style.color = '#fff';
                            btn.style.backgroundColor = '#333';
                            btn.innerHTML = 'Отправить ещё раз';
                        }
                    }, 1000);

                    // После клика — снова блокируем на 60 сек
                    btn.addEventListener('click', function() {
                        if (btn.disabled) return;

                        btn.disabled = true;
                        btn.style.cursor = 'not-allowed';
                        btn.style.color = '#999';
                        btn.style.backgroundColor = '#fff';
                        seconds = 60;
                        btn.innerHTML = 'Отправить ещё раз (<span id="resend-timer">' + seconds + '</span> сек)';
                        timerEl = document.getElementById('resend-timer');

                        var interval2 = setInterval(function() {
                            seconds--;
                            if (timerEl) timerEl.textContent = seconds;

                            if (seconds <= 0) {
                                clearInterval(interval2);
                                btn.disabled = false;
                                btn.style.cursor = 'pointer';
                                btn.style.color = '#fff';
                                btn.style.backgroundColor = '#333';
                                btn.innerHTML = 'Отправить ещё раз';
                            }
                        }, 1000);
                    });
                })();
            </script>
            <div id="ajaxresult"></div>

            <p class="form-row account-privacy" style="display:block;">
                Регистрируясь на сайте, Вы соглашаетесь с условиями <a href="https://ferma-dv.ru/privacy/">политики конфиденциальности</a> и&nbsp;<a href="https://ferma-dv.ru/agreement/">правилами продажи</a>.
            </p>

            <p class="form-row">
                <button type="button" class="woocommerce-form-login__submit btn-green" id="submitcode" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
            </p>
        </div>
        <?php do_action( 'woocommerce_login_form_end' ); ?>
    <?php } ?>


    <script>
        $("#submitcode1").on("click", function(){
            var l = FindByAttributeValue("name", "telephone1");
            $.ajax({
                url: '/wp-content/themes/theme/obrabotka_code1.php',
                method: 'post',
                dataType: 'html',
                data: {phone: l.value},
                success: function(data){
                    $("#sub3").show('slow');
                    setTimeout(function() { $("#sub3").hide('slow'); }, 3000);
                    var jsonData = JSON.parse(data);
                    var h = FindByAttributeValue("name", "code");
                    h.value = jsonData.fullcode;
                }
            });
        });

        function FindByAttributeValue(attribute, value, element_type) {
            element_type = element_type || "*";
            var All = document.getElementsByTagName(element_type);
            for (var i = 0; i < All.length; i++) {
                if (All[i].getAttribute(attribute) == value) { return All[i]; }
            }
        }
    </script>

    <script>
        $("#submitcode").on("click", function(){
            var e = FindByAttributeValue("name", "tel_code");
            var l = FindByAttributeValue("name", "telephone1");
            var normalizedCode = (e && e.value ? e.value : '').replace(/\D+/g, '');
            var normalizedPhone = (l && l.value ? l.value : '').replace(/\D+/g, '');
            $.ajax({
                url: '/proverka-koda/',
                method: 'post',
                dataType: 'html',
                data: {text: normalizedCode, phone: normalizedPhone},
                success: function(data){
                    var jsonData = JSON.parse(data);
                    if (jsonData.success == 0) {
                        document.getElementById("ajaxresult").innerHTML = '<p class="incorrect">Вы ввели неверный код</p>';
                    } else {
                        document.getElementById("ajaxresult").innerHTML = '<p>Вы успешно вошли</p>';
                        window.location.reload();
                    }
                }
            });
        });
    </script>

    <?php // --- Шаг 1: Форма ввода телефона --- ?>
    <div class="page-account__login" id="customer_login1">
        <div class="page-account__login-inner">
            <script src="https://unpkg.com/imask"></script>
            <form class="woocommerce-form woocommerce-form-login login" method="post">
                <?php do_action( 'woocommerce_login_form_start' ); ?>

                <p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">
                    <label for="telephone">Ваш телефон&nbsp;<span class="required">*</span></label>
                    <input type="tel" class="woocommerce-Input woocommerce-Input--text input-text account-phone" name="telephone" id="telephone" autocomplete="username" value="<?php
                    if(isset($_GET["phone"])){
                        echo esc_attr($_GET["phone"]);
                    }
                    ?>" />
                </p>
                <script>
                    var phoneMask = IMask(
                        document.getElementById('telephone'), {
                            mask: '+{7}(#00)0000000',
                            definitions: {
                                '#': /[01234569]/
                            },
                            lazy: false,
                            placeholderChar: ' '
                        });
                </script>

                <?php do_action( 'woocommerce_login_form' ); ?>

                <p class="account-privacy">
                    Регистрируясь на сайте, Вы соглашаетесь с условиями <a href="https://ferma-dv.ru/privacy/">политики конфиденциальности</a> и&nbsp;<a href="https://ferma-dv.ru/agreement/">правилами продажи</a>.
                </p>

                <div class="form-row ferma-login-buttons">
                    <button type="submit" class="woocommerce-form-login__submit page-account__entry-btn btn-green" name="login" value="<?php esc_attr_e( 'Log in', 'woocommerce' ); ?>"><?php esc_html_e( 'Log in', 'woocommerce' ); ?></button>
                    <a href="https://ferma-dv.ru/my-account?login=email" class="btn-outline-green">Войти по email</a>
                </div>

                <?php do_action( 'woocommerce_login_form_end' ); ?>
            </form>
        </div>
    </div>

<?php } ?>