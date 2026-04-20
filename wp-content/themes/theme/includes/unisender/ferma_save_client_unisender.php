<?php

if(!function_exists('ferma_save_client_callback')) {
	add_action('wp_ajax_save_client', 'ferma_save_client_callback');
	add_action('wp_ajax_nopriv_save_client', 'ferma_save_client_callback');
	
	function ferma_save_client_callback() {
		$list_id = 617;
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			if(!isset($_POST['email']) || !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
				wp_send_json_error(['error' => 'Введите корректный адрес эл.почты']);
			}
			
			if(!wp_verify_nonce( $_POST[ 'nonce' ], 'save_client_form' )) {
				wp_send_json_error(['error' => 'Ошибка обработки формы, попробуйте еще раз']);
			}
			
			if(!isset($_POST['agree']) || $_POST['agree'] != 1) {
				wp_send_json_error(['error' => 'Подтвердите согласие с политикой конфиденциальности и пользовательским соглашением']);
			}
			
			$res = file_get_contents("https://api.unisender.com/ru/api/subscribe?format=json&api_key=64gjdw53pw39kxjjomdou7zums6jsq1aarhywxna&list_ids=".$list_id."&fields[email]=".$_POST['email']."&double_optin=3&tags=Popup");
			$res = json_decode($res, true);
			if(isset($res['error'])) {
				wp_send_json_error(['error' => 'Вы уже подписаны на рассылку']);
			} else {
				$current_user = wp_get_current_user();
				if($current_user->user_email == "") {
					if(!email_exists( $_POST['email'])) {
						$args = array(
							'ID'         => $current_user->id,
							'user_email' => esc_attr( $_POST['email'] )
						);            
						wp_update_user( $args );
					}
				}
				
				$message = get_field('popup_message', 'option');
				setcookie( 'save_client', 1, time() + (3600 * 24 * 30), '/' );
				wp_send_json_success(['message' => $message]);
			}
		}
		
		/*if(isset($_POST['email']) && wp_verify_nonce( $_POST[ 'nonce' ], 'save_client_form' ) && filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
			
			$res = file_get_contents("https://api.unisender.com/ru/api/subscribe?format=json&api_key=64gjdw53pw39kxjjomdou7zums6jsq1aarhywxna&list_ids=279&fields[email]=".$_POST['email']."&double_optin=3&tags=Popup");
			
			$res = json_decode($res, true);
			
			if(isset($res['error'])) {
				wp_send_json_error();
			} else {
				setcookie( 'save_client', 1, time() + (3600 * 24 * 30), '/' );
				wp_send_json_success([]);
			}
		}*/
	}
}