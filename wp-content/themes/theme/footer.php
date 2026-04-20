<p style="display:none" id="answer_user"><?php
if ( function_exists( 'ferma_is_catalog_cache_candidate' ) && ferma_is_catalog_cache_candidate() && ! is_user_logged_in() ) {
	echo '0';
} elseif ( is_user_logged_in() ) {
	$const = get_user_meta( get_current_user_id(), 'delivery', true );
	if ( $const === '' ) {
		unset( $const );
	}
	if ( isset( $const ) ) {
		echo 1;
	} else {
		echo 0;
	}
} else {
	if ( isset( $_COOKIE['delivery'] ) && ( ( isset( $_COOKIE['coords'] ) && $_COOKIE['coords'] !== '' ) || ( isset( $_COOKIE['billing_samoviziv'] ) && $_COOKIE['billing_samoviziv'] !== '' ) ) ) {
		echo 1;
	} else {
		echo 0;
	}
}
?></p><?php if ( function_exists( 'ferma_is_catalog_cache_candidate' ) && ferma_is_catalog_cache_candidate() && ! is_user_logged_in() ) : ?>
<script>
(function () {
	function readCookie(name) {
		var prefix = name + '=';
		var cookies = document.cookie ? document.cookie.split(';') : [];
		for (var i = 0; i < cookies.length; i++) {
			var c = cookies[i].trim();
			if (c.indexOf(prefix) === 0) {
				return decodeURIComponent(c.substring(prefix.length));
			}
		}
		return '';
	}
	var el = document.getElementById('answer_user');
	if (!el) return;
	var d = readCookie('delivery');
	var ok = d && ((readCookie('coords') && readCookie('coords') !== '') || (readCookie('billing_samoviziv') && readCookie('billing_samoviziv') !== ''));
	el.textContent = ok ? '1' : '0';
})();
</script>
<?php endif; ?>
<script>
	const samovivizValue = document.querySelector('#data_of_samoviviz').innerHTML;
const options = document.querySelectorAll('#billing_type_delivery_sam option');
options.forEach(option => {
  if (option.value === samovivizValue) {
    option.selected = true;
  }
});

</script>
<script>
// Проверяем значение #answer_user
if (document.getElementById("answer_user").innerText === "0") {
  // Получаем все элементы <a> с классом "add_to_cart_button"
  var links = document.querySelectorAll("a.add_to_cart_button");
  // Проходим по всем найденным элементам
  links.forEach(function(link) {
    // Заменяем класс на "add_to_card_button1"
    link.classList.replace("add_to_cart_button", "add_to_card_button1");
    // Удаляем href
    link.removeAttribute("href");
    // Добавляем обработчик клика на элемент
    link.addEventListener("click", function(event) {
      // Отменяем стандартное поведение ссылки
      event.preventDefault();
      // Открываем модальное окно
      var modal = document.querySelector(".modal1");
      modal.style.display = "block";
    });
  });
}


// Проверяем значение #answer_user
if (document.getElementById("answer_user").innerText === "0") {
  // Получаем все элементы <a> с классом "add_to_cart_button"
  var links = document.querySelectorAll(".single_add_to_cart_button");
  // Проходим по всем найденным элементам
  links.forEach(function(link) {
    // Заменяем класс на "add_to_card_button1"
    link.classList.replace("single_add_to_cart_button", "single_add_to_cart_button1");
    // Удаляем href
    link.removeAttribute("href");
    // Добавляем обработчик клика на элемент
    link.addEventListener("click", function(event) {
      // Отменяем стандартное поведение ссылки
      event.preventDefault();
      // Открываем модальное окно
      var modal = document.querySelector(".modal1");
      modal.style.display = "block";
    });
  });
}



</script>
<style>
	.add_to_card_button1 {
		background-color: #6ba802 !important;
		color: #ffffff !important;
	}
</style>
<div class="container-fluid footer_info d-none d-lg-block"
    style="background: url(https://ferma-dv.ru/wp-content/uploads/2021/04/01-_1_.webp); background-size: cover;"></div>

<div class="container-fluid" style="background: #68aa2f;">
    <div class="container footer_ferma">
        <div class="row">
            <div class="col-12 col-lg-3">
                <img src="https://ferma-dv.ru/wp-content/uploads/2022/07/Logotype-svg.svg" alt="Логотип Дальневосточный фермер" style="width:50%"> <br> <br>
                © 2020 - 2026 ferma-dv.ru <br> Магазин фермерских продуктов
            </div>
            <div class="col-12 col-lg-3 d-none d-lg-block">
                <span class="yit_footer">НАШ АССОРТИМЕНТ</span>
                <a href="https://ferma-dv.ru/product-category/bakaleya/">Бакалея</a>
                <a href="https://ferma-dv.ru/product-category/varene/">Варенье, домашние соки и компоты</a>
                <a href="https://ferma-dv.ru/product-category/domashnie-syry/">Домашние сыры</a>
                <a href="https://ferma-dv.ru/product-category/domashnyaya-konservacziya/">Домашняя консервация</a>
                <a href="https://ferma-dv.ru/product-category/kolbasy/">Колбасы</a>
                <a href="https://ferma-dv.ru/product-category/kopchenosti/">Копчености</a>
				<a href="https://ferma-dv.ru/product-category/med/">Мед</a>
                <a href="https://ferma-dv.ru/product-category/molochnaya-produkcziya/">Молочная продукция</a>
                <a href="https://ferma-dv.ru/product-category/myaso/">Мясо</a>
                <a href="https://ferma-dv.ru/product-category/ovoshhi/">Овощи, фрукты, ягоды</a>
				<a href="https://ferma-dv.ru/product-category/podarochnye-nabory/">Подарочные наборы</a>
                <a href="https://ferma-dv.ru/product-category/polufabrikaty-domashnie/">Полуфабрикаты домашние</a>
                <a href="https://ferma-dv.ru/product-category/remeslennyj-hleb-i-vypechka/">Ремесленный хлеб и выпечка</a>
				<a href="https://ferma-dv.ru/product-category/sladosti-i-deserty/">Сладости и десерты</a>
                <a href="https://ferma-dv.ru/product-category/tushenka-i-kashi-sobstvennoe-proizvodstvo/">Тушенки и каши собственное производство</a>
                <a href="https://ferma-dv.ru/product-category/chaj-travy-i-dikorosy/">Чай, травы и дикоросы</a>
                <a href="https://ferma-dv.ru/product-category/yajczo/">Яйцо домашнее</a>
            </div>
            <div class="col-12 col-lg-3">
                <span class="yit_footer">ИНФОРМАЦИЯ</span>
				<a href="https://ferma-dv.ru/about/">О компании</a>
                <a href="https://ferma-dv.ru/category/fermerskij-blog/">Фермерский блог</a>
                <a href="https://ferma-dv.ru/privacy/">Политика конфиденциальности</a>
                <a href="https://ferma-dv.ru/agreement/">Пользовательское соглашение</a>
                <a href="https://ferma-dv.ru/oplata-vozvrat/">Правила оплаты и возврат</a>
                <a href="https://ferma-dv.ru/dostavka/">Доставка и оплата</a>
				<a href="https://ferma-dv.ru/otziv/">Отзывы</a>
				<a href="https://ferma-dv.ru/category/akcii/">Акции</a>
            </div>
            <div class="col-12 col-lg-3">
                <span class="yit_footer">КОНТАКТЫ</span>
                <a href="tel:+79084411110" style="font-weight: 700;">+7 (908) 441 1110</a>
                <a href="mailto:zakaz@ferma-dv.ru" style="font-weight: 700;">zakaz@ferma-dv.ru</a>
                09:00 - 21:00 (заказ можно оформить круглосуточно) <br><br>
				<b>г. Владивосток</b> <br> 
				ул. Верхнепортовая, 41в <br>
				Океанский проспект, 108 (Первая Речка, магазин Реми)<br>
                Реми-Сити (ул. Народный пр-т, 20) <br>
                ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а) <br>
				ул. Тимирязева, 31 строение 1 (район Спутник)<br>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"
    integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN"
    crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
    crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
    crossorigin="anonymous"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<?php wp_footer(); ?>
<style>
    .sale_class a img {border: none !important;}
    .sale_class .price {position:relative;}
    .sale_class .price .old-price{
        position: absolute;
        left: 10px;
        top: -10px;
        z-index: 10;
        font-weight: 500;
        font-size: 14px;
    }

	.woocommerce .blockUI.blockOverlay {
		display: none !important;
	}
</style>

<script>

	
	 function initMap() {
        var element = document.getElementById('map');
        var options = {
            zoom: 5,
            center: {lat: 55.7558, lng: 37.6173},
        };

        var myMap = new google.maps.Map(element, options);

        var markers = [
            {
                coordinates: {lat: 55.751956, lng: 37.622634},
                info: '<h3>Москва</h3><br><img src="https://placehold.it/200x150"><br><p>Описание</p>'
            },
            {
                coordinates: {lat: 59.940208, lng: 30.328092},
                info: '<h3>Санкт-Петербург</h3><br> <img src="https://placehold.it/200x150"><br><p>Описание</p>'
            },
            {
                coordinates: {lat: 50.449973, lng: 30.524911},
                info: '<h3>Киев</h3><br><img src="https://placehold.it/200x150"><br><p>Описание</p>'
            }
        ];

        for(var i = 0; i < markers.length; i++) {
            addMarker(markers[i]);
        }

        function addMarker(properties) {
            var marker = new google.maps.Marker({
                position: properties.coordinates,
                map: myMap
            });

            if(properties.image) {
                marker.setIcon(properties.image);
            }

            if(properties.info) {
                var InfoWindow = new google.maps.InfoWindow({
                    content: properties.info
                });

                marker.addListener('click', function(){
                    InfoWindow.open(myMap, marker);
                });
            }
        }
    }
</script>

<script>
(() => {
	
	const flex_block = document.querySelector('.flex_block');
	console.log(flex_block);
	if(flex_block) {
		flex_block.innerHTML = flex_block.innerHTML.replace(/\&nbsp;/g, '');
	}
})();
</script>

<?php if(1==2): ?>
<div class="buyoneclick-modal">
	<div class="buyoneclick-modal__content">
		<a href="javascript:;" class="buyoneclick-modal__close">Закрыть окно</a>
		
		<div class="buyoneclick-modal__form">
			<form action="" method="post" data-nonce="<?php echo wp_create_nonce( 'buyoneclick_form' ); ?>">
				<input type="hidden" name="product_id" value="" class="buyoneclick-modal__product-id">
				<h2>Предзаказ</h2>
				<div class="buyoneclick-modal__message">
				
				</div>
				
				<div class="buyoneclick-modal__error">
					
				</div>
				<div class="buyoneclick-modal__field">
					<input type="text" class="buyoneclick-modal__name" name="name" value="" placeholder="Введите ваше имя" />
				</div>
				<div class="buyoneclick-modal__field">
					<input type="text" class="buyoneclick-modal__phone" name="phone" value="" placeholder="Введите телефон" />
				</div>
				<div class="buyoneclick-modal__field">
					<select name="delivery" class="buyoneclick-modal__delivery">
						<option value="1">Доставка</option>
						<option value="2">Самовывоз</option>
					</select>
				</div>
				<div class="buyoneclick-modal__field buyoneclick-modal__hide" id="buyoneclick-shop">
					<select name="shop">
						<optgroup label="Владивосток">
							<option>Эгершельд, Верхнепортовая, 41в</option>
							<option>Океанский проспект, 108 (Первая Речка, магазин Реми)</option>
							<option>Реми-Сити (ул. Народный пр-т, 20)</option>
							<option>ТЦ «Море», Гипермаркет (ул. Некрасовская, 49а)</option>
							<option>ул. Тимирязева, 31 строение 1 (район Спутник)</option>
						</optgroup>
						<optgroup label="Уссурийск">
							<option>ТЦ Москва, 1-й этаж (ул. Суханова, 52)</option>
						</optgroup>
						<optgroup label="Находка">
							<option>Находка, Проспект мира, 65/1</option>
						</optgroup>
					</select>
				</div>
				<div class="buyoneclick-modal__field">
					<input type="text" class="buyoneclick-modal__time" name="time" value="" placeholder="Удобная дата" />
				</div>
				<div class="buyoneclick-modal__agree">
					<label>
					<input class="buyoneclick-modal__checkbox" type="checkbox" name="agree" value="1" checked>
					Я согласен с&nbsp;<a href="/privacy/" target="_blank">политикой конфиденциальности</a> и&nbsp;<a href="/agreement/" target="_blank">пользовательским соглашением</a>.
				</div>
				<div class="buyoneclick-modal__field">
					<button type="submit">Заказать</button>
				</div>
			</form>
			
			
		</div>
	</div>
</div>
<?php endif; ?>

<?php if(!isset($_COOKIE['save_client2'])) : ?>
<?php

$popup_show = get_field('popup_is_show', 'option');
$popup_image_desktop = get_field('popup_image_desktop', 'option');
$popup_image_mobile = get_field('popup_image_mobile', 'option');
$popup_button_text = get_field('popup_button', 'option');
$popup_after_text = get_field('popup_message', 'option');
$popup_error = get_field('popup_error', 'option');

if($popup_show) :
?>
<div class="popup-save-client">
	<div class="popup-save-client__content">
		<a href="javascript:;" class="popup-save-client__close">Закрыть окно</a>
		<?php if(is_array($popup_image_desktop) && isset($popup_image_desktop['url']) && $popup_image_desktop['url'] != '') : ?>
		<div class="popup-save-client__image-desktop">
			<img src="<?php echo $popup_image_desktop['url']; ?>" />
		</div>
		<?php endif; ?>
		
		<?php if(is_array($popup_image_mobile) && isset($popup_image_mobile['url']) && $popup_image_mobile['url'] != '') : ?>
		<div class="popup-save-client__image-mobile">
			<img src="<?php echo $popup_image_mobile['url']; ?>" />
		</div>
		<?php endif; ?>
		
		<div class="popup-save-client__form">
			<form action="" method="post" data-nonce="<?php echo wp_create_nonce( 'save_client_form' ); ?>">
				<div class="popup-save-client__field">
					<input type="email" class="popup-save-client__email" name="email" value="" placeholder="Введите адрес эл.почты" />
					<button type="submit"><?php echo $popup_button_text; ?></button>
				</div>
				
				<div class="popup-save-client__agree">
					<label>
					<input class="popup-save-client__checkbox" type="checkbox" name="agree" value="1" checked>
					Я согласен с&nbsp;<a href="/privacy/" target="_blank">политикой конфиденциальности</a> и&nbsp;<a href="/agreement/" target="_blank">пользовательским соглашением</a>.
				</div>
			</form>
			
			<div class="popup-save-client__message">
				
			</div>
			
			<div class="popup-save-client__error">
				
			</div>
		</div>
	</div>
</div>

<script>
(() => {
	function setPopupCookie(name, value) {
		/*let d = new Date,
			h = d.getHours(),
			m = d.getMinutes(),
			s = d.getSeconds(),
			secondsUntilEndOfDate = (24*60*60) - (h*60*60) - (m*60) - s;
		
		d.setTime(d.getTime() + secondsUntilEndOfDate);*/
		
		let expireDate = new Date();
		expireDate.setDate( expireDate.getDate() + 1 );
		expireDate.setHours(0);
		expireDate.setMinutes(1);
		
		console.log(expireDate.toString());
		console.log(expireDate);
		
		document.cookie = name + "=" + value + ";path=/;expires=" + expireDate.toGMTString();
	}
	
	function onSaveClientFormSubmit(event) {
		event.preventDefault();
			
		const save_form = event.target,
			email = document.querySelector('.popup-save-client__email'),
			message = document.querySelector('.popup-save-client__message'),
			error = document.querySelector('.popup-save-client__error'),
			agree = document.querySelector('.popup-save-client__checkbox');
			
		let requestData = {
			action: "save_client",
			email: email.value,
			agree: (agree.checked) ? 1 : 0,
			nonce: save_form.dataset.nonce,
		};

		fetch('/wp-admin/admin-ajax.php', {
			method:"post",
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: new URLSearchParams(requestData).toString(),
		}).then(function(response) {
			return response.json();
		}).then(function(response) {
			if(response.success) {
				message.innerHTML = response.data.message;
				message.style.display = "flex";
				error.style.display = "none";
			} else {
				error.innerHTML = response.data.error;
				message.style.display = "none";
				error.style.display = "flex";
			}
		});
			
		return false;
	}
	
	function show_save_client()
	{
		setPopupCookie('save_client2', 1);
		
		let popup_save_client = document.querySelector(".popup-save-client");
		if(popup_save_client) {
			popup_save_client.style.display = "flex";
				
			const save_client_form = document.querySelector(".popup-save-client__form form");
								
			if(save_client_form) {
				save_client_form.addEventListener("submit", onSaveClientFormSubmit);
			}
				
			const popup_close = document.querySelector(".popup-save-client__close");
			popup_close.addEventListener("click", function(event) {
				event.preventDefault();
				let popup_save_client = document.querySelector(".popup-save-client");
				if(popup_save_client) {
					popup_save_client.remove();
				}
			});
		}
	}
	
	document.addEventListener("mouseleave", function(event){
		if(event.clientY <= 0 || event.clientX <= 0 || (event.clientX >= window.innerWidth || event.clientY >= window.innerHeight))
		{
			show_save_client();
		}
	});
	
	if ("ontouchstart" in document.documentElement)
	{
		setTimeout(() => {
		  show_save_client();
		}, 60000);
	}
  })();
</script>

<?php endif; ?>

<?php endif; ?>

<script>
	$(document).ready(function() {
		$('.mslider').slick({
			autoplay: true,
			autoplaySpeed: 3000,
			prevArrow: $('.mslider__arrow-prev'),
			nextArrow: $('.mslider__arrow-next'),
		});
		
		$('.dslider__list').slick({
			autoplay: true,
			autoplaySpeed: 3000,
			prevArrow: $('.dslider__arrow-prev'),
			nextArrow: $('.dslider__arrow-next'),
		});
	});
</script>
<script src="https://use.fontawesome.com/cba96dfc46.js"></script>

<div type="button" class="callback-bt">
    <a href="https://wa.me/+79084411110?text=Хочу%20заказать%20доставку%20продуктов" target="_blank">
		<div class="text-call"><i class="fa fa-whatsapp"></i></div>
    </a>
</div>
	<style>
	.callback-bt {
	background: #25d366;
	border: 0px solid #38a3fd;
	border-radius: 50%;
	box-shadow: 0 8px 10px rgba(56, 163, 253, 0.3);
	cursor: pointer;
	height: 68px;
	text-align: center;
	width: 68px;
	position: fixed;
	right: 5%;
	bottom: 10%;
	z-index: 999;
	transition: .3s;
	-webkit-animation: hoverWave linear 1s infinite;
	animation: hoverWave linear 1s infinite;
}

.callback-bt .text-call {
	height: 68px;
	width: 68px;
	border-radius: 50%;
	position: relative;
	overflow: hidden;
}

.callback-bt .text-call span {
	text-align: center;
	color: #38a3fd;
	opacity: 0;
	font-size: 0;
	position: absolute;
	right: 4px;
	top: 22px;
	line-height: 14px;
	font-weight: 600;
	text-transform: uppercase;
	transition: opacity .3s linear;
	font-family: 'montserrat', Arial, Helvetica, sans-serif;
}

.callback-bt .text-call:hover span {
	opacity: 1;
	font-size: 11px;
}

.callback-bt:hover i {}

.callback-bt:hover {
	z-index: 1;
	background: #188b43;
	color: transparent;
	transition: .3s;
}

.callback-bt:hover i {
	font-size: 40px;
	transition: .3s;
}

.callback-bt i {
	color: #fff;
	font-size: 44px;
	transition: .3s;
	line-height: 66px;
	margin-left: -2px;
	transition: .5s ease-in-out;
}

.callback-bt i {
	animation: 1200ms ease 0s normal none 1 running shake;
	animation-iteration-count: infinite;
	-webkit-animation: 1200ms ease 0s normal none 1 running shake;
	-webkit-animation-iteration-count: infinite;
}

@-webkit-keyframes hoverWave {
	0% {
		box-shadow: 0 8px 10px rgba(56, 163, 253, 0.3), 0 0 0 0 rgba(56, 163, 253, 0.2), 0 0 0 0 rgba(56, 163, 253, 0.2)
	}
	40% {
		box-shadow: 0 8px 10px rgba(56, 163, 253, 0.3), 0 0 0 15px rgba(56, 163, 253, 0.2), 0 0 0 0 rgba(56, 163, 253, 0.2)
	}
	80% {
		box-shadow: 0 8px 10px rgba(56, 163, 253, 0.3), 0 0 0 30px rgba(56, 163, 253, 0), 0 0 0 26.7px rgba(56, 163, 253, 0.067)
	}
	100% {
		box-shadow: 0 8px 10px rgba(56, 163, 253, 0.3), 0 0 0 30px rgba(56, 163, 253, 0), 0 0 0 40px rgba(56, 163, 253, 0.0)
	}
}

@keyframes hoverWave {
	0% {
		box-shadow: 0 8px 10px rgba(56, 163, 253, 0.3), 0 0 0 0 rgba(56, 163, 253, 0.2), 0 0 0 0 rgba(56, 163, 253, 0.2)
	}
	40% {
		box-shadow: 0 8px 10px rgba(56, 163, 253, 0.3), 0 0 0 15px rgba(56, 163, 253, 0.2), 0 0 0 0 rgba(56, 163, 253, 0.2)
	}
	80% {
		box-shadow: 0 8px 10px rgba(56, 163, 253, 0.3), 0 0 0 30px rgba(56, 163, 253, 0), 0 0 0 26.7px rgba(56, 163, 253, 0.067)
	}
	100% {
		box-shadow: 0 8px 10px rgba(56, 163, 253, 0.3), 0 0 0 30px rgba(56, 163, 253, 0), 0 0 0 40px rgba(56, 163, 253, 0.0)
	}
}


/* animations icon */

@keyframes shake {
	0% {
		transform: rotateZ(0deg);
		-ms-transform: rotateZ(0deg);
		-webkit-transform: rotateZ(0deg);
	}
	10% {
		transform: rotateZ(-30deg);
		-ms-transform: rotateZ(-30deg);
		-webkit-transform: rotateZ(-30deg);
	}
	20% {
		transform: rotateZ(15deg);
		-ms-transform: rotateZ(15deg);
		-webkit-transform: rotateZ(15deg);
	}
	30% {
		transform: rotateZ(-10deg);
		-ms-transform: rotateZ(-10deg);
		-webkit-transform: rotateZ(-10deg);
	}
	40% {
		transform: rotateZ(7.5deg);
		-ms-transform: rotateZ(7.5deg);
		-webkit-transform: rotateZ(7.5deg);
	}
	50% {
		transform: rotateZ(-6deg);
		-ms-transform: rotateZ(-6deg);
		-webkit-transform: rotateZ(-6deg);
	}
	60% {
		transform: rotateZ(5deg);
		-ms-transform: rotateZ(5deg);
		-webkit-transform: rotateZ(5deg);
	}
	70% {
		transform: rotateZ(-4.28571deg);
		-ms-transform: rotateZ(-4.28571deg);
		-webkit-transform: rotateZ(-4.28571deg);
	}
	80% {
		transform: rotateZ(3.75deg);
		-ms-transform: rotateZ(3.75deg);
		-webkit-transform: rotateZ(3.75deg);
	}
	90% {
		transform: rotateZ(-3.33333deg);
		-ms-transform: rotateZ(-3.33333deg);
		-webkit-transform: rotateZ(-3.33333deg);
	}
	100% {
		transform: rotateZ(0deg);
		-ms-transform: rotateZ(0deg);
		-webkit-transform: rotateZ(0deg);
	}
}

@-webkit-keyframes shake {
	0% {
		transform: rotateZ(0deg);
		-ms-transform: rotateZ(0deg);
		-webkit-transform: rotateZ(0deg);
	}
	10% {
		transform: rotateZ(-30deg);
		-ms-transform: rotateZ(-30deg);
		-webkit-transform: rotateZ(-30deg);
	}
	20% {
		transform: rotateZ(15deg);
		-ms-transform: rotateZ(15deg);
		-webkit-transform: rotateZ(15deg);
	}
	30% {
		transform: rotateZ(-10deg);
		-ms-transform: rotateZ(-10deg);
		-webkit-transform: rotateZ(-10deg);
	}
	40% {
		transform: rotateZ(7.5deg);
		-ms-transform: rotateZ(7.5deg);
		-webkit-transform: rotateZ(7.5deg);
	}
	50% {
		transform: rotateZ(-6deg);
		-ms-transform: rotateZ(-6deg);
		-webkit-transform: rotateZ(-6deg);
	}
	60% {
		transform: rotateZ(5deg);
		-ms-transform: rotateZ(5deg);
		-webkit-transform: rotateZ(5deg);
	}
	70% {
		transform: rotateZ(-4.28571deg);
		-ms-transform: rotateZ(-4.28571deg);
		-webkit-transform: rotateZ(-4.28571deg);
	}
	80% {
		transform: rotateZ(3.75deg);
		-ms-transform: rotateZ(3.75deg);
		-webkit-transform: rotateZ(3.75deg);
	}
	90% {
		transform: rotateZ(-3.33333deg);
		-ms-transform: rotateZ(-3.33333deg);
		-webkit-transform: rotateZ(-3.33333deg);
	}
	100% {
		transform: rotateZ(0deg);
		-ms-transform: rotateZ(0deg);
		-webkit-transform: rotateZ(0deg);
	}
}

@media(max-width:768px) {
	.review-list__container {
		display: grid;
		grid-template-columns: inherit;
		grid-gap: inherit;
	}
	.callback-bt {
		width: 100%;
		border-radius: 0;
		bottom: 0;
		left: 0;
		box-shadow: none;
		transition: none;
		animation: none;
	}
	.callback-bt i {
		color: #fff;
		font-size: 34px;
		transition: .3s;
		line-height: 48px;
		margin-left: -2px;
		transition: .5s ease-in-out;
	}
	.callback-bt:after {
		content: 'Написать в WhatsApp';
		font-size: 20px;
		color: #fff;
		margin-top: -56px;
		position: absolute;
		left: 66px;
	}
	.callback-bt {
		cursor: pointer;
		height: 52px;
	}
	.callback-bt a {
		position: relative;
		z-index: 99999;
		width: 100%;
		display: block;
	}
}
	
	</style>
<style>
    /* ГЛОБАЛЬНОЕ СКРЫТИЕ КАРТ И ВРЕМЕНИ ДОСТАВКИ НА ВСЕХ СТРАНИЦАХ */
    #map,
    #map2,
    #time_change,
    #flex_time,
    .mainblock_time {
        display: none !important;
        visibility: hidden !important;
        height: 0 !important;
        width: 0 !important;
        position: absolute !important;
        left: -9999px !important;
        opacity: 0 !important;
    }

    /* Скрытие контейнеров карт в модальных окнах */
    .modal1 #map,
    .modal1 #map2,
    .tab-content1 #map,
    .tab-content1 #map2 {
        display: none !important;
    }
</style>

<script>
    // Дублируем скрытие через JavaScript для надежности
    document.addEventListener('DOMContentLoaded', function() {
        function hideMapsAndTime() {
            // Скрываем карты
            const maps = document.querySelectorAll('#map, #map2');
            maps.forEach(map => {
                if (map) {
                    map.style.display = 'none';
                    map.style.visibility = 'hidden';
                    map.style.height = '0';
                    map.style.width = '0';
                    map.style.opacity = '0';
                    map.style.position = 'absolute';
                    map.style.left = '-9999px';
                }
            });

            // Скрываем блоки времени доставки
            const timeBlocks = document.querySelectorAll('#time_change, #flex_time, .mainblock_time');
            timeBlocks.forEach(block => {
                if (block) {
                    block.style.display = 'none';
                    block.style.visibility = 'hidden';
                }
            });
        }

        // Скрываем сразу
        hideMapsAndTime();

        // Дополнительные проверки на случай динамической загрузки
        setTimeout(hideMapsAndTime, 100);
        setTimeout(hideMapsAndTime, 500);
        setTimeout(hideMapsAndTime, 1000);

        // Наблюдатель за изменениями DOM
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.type === 'childList') {
                    hideMapsAndTime();
                }
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });
</script>
</body>

</html>