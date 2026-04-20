<?php 
    $user = htmlspecialchars($_POST["phone"]);
    $body = file_get_contents("https://sms.ru/code/call?phone=$user&ip=".$_SERVER["REMOTE_ADDR"]."&api_id=8EC86059-F03F-AE01-8A33-3F8443B51BC4"); 
    $json = json_decode($body, true);
    echo json_encode(array('fullcode' => $json['code']));
    ?> 