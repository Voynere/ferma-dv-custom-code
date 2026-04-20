<?php
if (!defined('ABSPATH')) exit;


/**
*
*/
class WmsConnect
{
	const wms_url = 'https://api.moysklad.ru/api/remap/1.1/';//основной адресс подключения

	static function get_connect($url)
	{
		$wms_option = get_option('wms_settings_auth');

		$headers = array(
	      "Authorization: Basic " . base64_encode($wms_option['wms_login'].":".$wms_option['wms_pass']),
	      "Content-Type: application/json",
	      //"Content-Length: ".strlen($databody)
	      );
	    //выполняем подключение
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, self::wms_url.$url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers) ;
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
			//получаем ответ
	     $obj = curl_exec($ch);
			 //закрываем подключение очишаем память
	 	 		curl_close($ch);
				return $obj;

	}

	static function get_connect_product($url)
	{
		$wms_option = get_option('wms_settings_auth');

		$headers = array(
				"Authorization: Basic " . base64_encode($wms_option['wms_login'].":".$wms_option['wms_pass']),
				"Content-Type: application/json",
				//"Content-Length: ".strlen($databody)
				);
			//выполняем подключение
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers) ;
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
			//получаем ответ
			 $obj = curl_exec($ch);
			 //закрываем подключение очишаем память
				curl_close($ch);
				return $obj;

	}


	static function post_connect($url, $databody = '', $type = 'post')
	{
		$wms_option = get_option('wms_settings_auth');

		$headers = array(
	      "Authorization: Basic " . base64_encode($wms_option['wms_login'].":".$wms_option['wms_pass']),
	      "Content-Type: application/json",
	      //"Content-Length: ".strlen($databody)
	      );
	    //выполняем подключение
	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, self::wms_url.$url);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers) ;
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1) ;
			curl_setopt($ch, CURLOPT_POST, true) ;
			if ($type == 'post') :
							curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			elseif ($type == 'put') :
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				elseif ($type == 'delete') :
					curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
			endif;
			curl_setopt($ch, CURLOPT_POSTFIELDS, $databody);
			//получаем ответ
	     $obj = curl_exec($ch);
			 //закрываем подключение очишаем память
	 	 		curl_close($ch);
				return $obj;

	}

}
?>
