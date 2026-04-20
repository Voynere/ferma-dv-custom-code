<footer class="footer">
    <div class="container">
        <div class="footer__inner">
            <div class="footer__item">
                <div class="footer__logo">
                    <img src="<?php bloginfo('template_url') ?>/assets/img/logo.svg" alt="Логотип">
                </div>
                <div class="footer__copy">
                    <p><span>© 2020 - 2026 </span><span>ferma-dv.ru</span></p>
                    <p>Магазин фермерских продуктов</p>
                </div>
            </div>
            <div class="footer__item">
                <h3 class="footer__item-title">ИНФОРМАЦИЯ</h3>
                <a href="/about/">О компании</a>
                <a href="/novosti/novogodnie-novosti-rezhim-raboty/">Новости</a>
                <a href="/privacy/">Политика конфиденциальности</a>
                <a href="/agreement/">Пользовательское соглашение</a>
                <a href="/dostavka/">Правила оплаты и возврата</a>
            </div>
            <div class="footer__item">
                <h3 class="footer__item-title">КОНТАКТЫ</h3>
                <a href="tel:+79084411110">+7-908-441-1110</a>
                <a href="mailto:zakaz@ferma-dv.ru">zakaz@ferma-dv.ru</a>
            </div>
            <div class="footer__item">
                <h3 class="footer__item-title">АДРЕСА</h3>
                <ul>
                    <li class="footer__item-city">г. Владивосток
                        <ul>
                            <li>ул. Верхнепортовая, 41в</li>
                            <li>Реми-Сити (ул. Народный пр-т, 20)</li>
                            <li>ул. Тимирязева, 31 строение 1 (район Спутник)</li>
                        </ul>
                    </li>
                    <!--li class="footer__item-city">г. Находка
                        <ul>
                            <li>проспект Мира 65/10, Сити-центр, фермерский мини-рынок</li>
                        </ul>
                    </li>
                    <li class="footer__item-city">г. Уссурийск
                        <ul>
                            <li>ТЦ Москва, 1-й этаж (ул. Суханова, 52)</li>
                        </ul>
                    </li-->
                </ul>

            </div>
        </div>
    </div>
</footer>
</div>
<!-- old -->
<p style="display:none" id="answer_user"><?
global $file_prefix;
wp_cache_clean_cache( $file_prefix, true );
if ( is_user_logged_in() ) {
	$const = get_user_meta( get_current_user_id(), 'delivery', true );
	if($const == "") {
		unset($const);
	}
} else {
	if(isset($_COOKIE['delivery']) && ((isset($_COOKIE['coords']) && $_COOKIE['coords'] != '') || (isset($_COOKIE['billing_samoviziv']) && $_COOKIE['billing_samoviziv'] != '') )) {
		$const = 1;
	}
}

	if (isset($const)) {
		echo 1;
	} else {
		echo 0;
	}
?></p>
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
  var links = document.querySelectorAll(".add_to_cart_button");
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

<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"
    integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q"
    crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"
    integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl"
    crossorigin="anonymous"></script>
<!-- <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script> -->
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
<!-- old -->

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script src="<?php bloginfo('template_url') ?>/assets/js/home.js"></script>
<script src="<?php bloginfo('template_url') ?>/assets/js/main.js?v=1.1"></script>
<script src="<?php bloginfo('template_url') ?>/assets/js/header-follow.js?v=1.2"></script>

</body>

</html>