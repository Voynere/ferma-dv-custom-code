<?php
    $body = file_get_contents("https://sms.ru/code/call?phone=" . $_POST['text'] . "&ip=".$_SERVER["REMOTE_ADDR"]."&api_id=8EC86059-F03F-AE01-8A33-3F8443B51BC4"); 
    $json = json_decode($body, true);
    if ($json['status'] == "ERROR") {
        echo json_encode(array('error' => 1));
    } else {
        echo json_encode(array('code' => $json['code']));

    }
?>