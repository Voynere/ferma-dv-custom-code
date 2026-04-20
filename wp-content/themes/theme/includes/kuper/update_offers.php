<?php
/*$xmldata = '<?xml version="1.0" encoding="UTF-8"?> <student> <info> <name>Rahul kumar</name> <age>10</age> <class>5th</class> <rollno>25</rollno> </info> </student>';
$url = "https://www.website.com/ws";
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_TIMEOUT, 60);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: text/xml'));
curl_setopt($ch, CURLOPT_POSTFIELDS, $xmldata);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
$result = curl_exec($ch);
curl_close($ch);*/

$api_url = "https://merchant-api.sbermarket.ru";
$client_id = "retailer_ferma_dv_magaziny";
$client_secret = "3M8A2dSAzv7XDaUPjjnizQghR4IpsgIB";

$data = [
	'client_id' => $client_id,
	'client_secret' => $client_secret,
	'grant_type' => 'client_credentials'
];

// Получаем токен
$curl = curl_init($api_url . "/auth/token");
curl_setopt($curl, CURLOPT_POST, true);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

$result = curl_exec($curl);
curl_close($curl);

$res = json_decode($result);

if($res->access_token) {
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: Bearer $res->access_token",
		"Content-Type: multipart/form-data; boundary=".$BOUNDARY)
	);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/1.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0');
	curl_setopt($ch, CURLOPT_URL, $api_url . "/api/v1/import/feeds/tasks/413191809");
	curl_setopt($ch, CURLOPT_COOKIEJAR, $BOUNDARY.'.txt');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POST, false);
	$response = curl_exec($ch);
	print_r($response);
	curl_close($ch);
	
	@unlink("./" . $BOUNDARY.'.txt');
	
	exit();
	
	
	$file_url = $_SERVER['DOCUMENT_ROOT'] . "/wp-content/themes/theme/includes/kuper/kuper-offers.xml";
	$eol = "\r\n";
	$BOUNDARY = md5(time());
	$BODY="";
	$BODY.= '--'.$BOUNDARY. $eol;
	$BODY.= 'Content-Disposition: form-data; name="file"; filename="goods_'.date("YmdHi").'.xml"' . $eol;
	$BODY.= 'Content-Type: application/octet-stream' . $eol;
	$BODY.= 'Content-Transfer-Encoding: 8bit' . $eol . $eol;
	$BODY.= file_get_contents($file_url) . $eol;
	$BODY.= '--'.$BOUNDARY .'--' . $eol. $eol;
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		"Authorization: Bearer $res->access_token",
		"Content-Type: multipart/form-data; boundary=".$BOUNDARY)
	);
	curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/1.0 (Windows NT 6.1; WOW64; rv:28.0) Gecko/20100101 Firefox/28.0');
	curl_setopt($ch, CURLOPT_URL, $api_url . "/api/v1/import/feeds");
	curl_setopt($ch, CURLOPT_COOKIEJAR, $BOUNDARY.'.txt');
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $BODY);
	$response = curl_exec($ch);
	print_r($response);
	curl_close($ch);
	
}