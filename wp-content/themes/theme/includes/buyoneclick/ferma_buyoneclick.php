<?php

if(!function_exists('ferma_buyoneclick_callback')) {
	//add_action('wp_ajax_buyoneclick_send', 'ferma_buyoneclick_callback');
	//add_action('wp_ajax_nopriv_buyoneclick_send', 'ferma_buyoneclick_callback');
	
	function ferma_buyoneclick_callback() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['name']) || strlen($_POST['name']) < 2) {
				wp_send_json_error(['error' => 'Введите ваше имя']);
			}
			
			if(!isset($_POST['phone']) || strlen($_POST['phone']) < 10) {
				wp_send_json_error(['error' => 'Введите ваш телефон']);
			}
			
			if(!isset($_POST['product_id']) || (int) $_POST['product_id'] == 0) {
				wp_send_json_error(['error' => 'Вы не выбрали продукт']);
			}
			
			if(!wp_verify_nonce( $_POST[ 'nonce' ], 'buyoneclick_form' )) {
				wp_send_json_error(['error' => 'Ошибка обработки формы, попробуйте еще раз']);
			}
			
			if(!isset($_POST['agree']) || $_POST['agree'] != 1) {
				wp_send_json_error(['error' => 'Подтвердите согласие с политикой конфиденциальности и пользовательским соглашением']);
			}
			
			
			$message = 'Поступил новый предзаказ' . "\n";
			$product = wc_get_product( $_POST['product_id'] );
			if(!$product) {
				wp_send_json_error(['error' => 'Продукт не найден']);
			}
			$message .= 'Продукт: ' . $product->get_name() . "\n";
			$message .= 'Имя: ' . $_POST['name'] . "\n";
			$message .= 'Телефон: ' . $_POST['phone'] . "\n";
			if($_POST['delivery'] == 1) {
				$message .= 'Тип: Доставка' . "\n";
			} else {
				$message .= 'Тип: Самовывоз' . "\n";
				$message .= 'Магазин: ' . $_POST['shop'] . "\n";
			}
			$message .= 'Удобное время: ' . $_POST['time'] . "\n";
			
			$headers = 'From: Предзаказ Ферма-ДВ <zakaz@ferma-dv.ru>' . "\r\n";
			wp_mail('zakaz@ferma-dv.ru', 'Предзаказ Ферма-ДВ', $message, $headers );
			wp_send_json_success(['message' => "Предзаказ успешно отправлен, мы свяжемся с вами в ближайшее время."]);
			
		}
	}
}