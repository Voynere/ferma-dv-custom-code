<?php

if(!function_exists('ferma_check_register_kilbil')) {
	add_action('wp_ajax_check_register_kilbil', 'ferma_check_register_kilbil');
	add_action('wp_ajax_nopriv_check_register_kilbil', 'ferma_check_register_kilbil');
	
	function ferma_check_register_kilbil() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$user_info = get_userdata(get_current_user_id());
			$user_login = $user_info->user_login;
			$result = preg_replace('/[^0-9]/', '', $user_login);
			$arr = array('search_mode' => 0, 'search_value' => $result);

			$url = "https://bonus.kilbil.ru/load/searchclient?h=614c6b88ac346607512f34afcf91326d";
			$content = json_encode($arr);
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_HTTPHEADER,
				array("Content-type: application/json"));
			curl_setopt($curl, CURLOPT_POST, true);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
			$json_response = curl_exec($curl);
			
			$obj = json_decode($json_response);
			var_dump($obj);
			curl_close($curl);
		}
	}
}