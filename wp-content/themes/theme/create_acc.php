<?
	$arr = array('phone' => $_POST['phone'], 'first_name' => $_POST['name'], 'generate_card' => 1, );

	$url = "https://bonus.kilbil.ru/load/addclient?h=614c6b88ac346607512f34afcf91326d";
	$content = json_encode($arr);
	$curl = curl_init($url);
	curl_setopt($curl, CURLOPT_HEADER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_HTTPHEADER,
			array("Content-type: application/json"));
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_POSTFIELDS, $content);
	$json_response = curl_exec($curl);
	$status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
	$obj = json_decode($json_response);
    print_r($obj);
	curl_close($curl);
?>