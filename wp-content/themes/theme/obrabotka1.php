<?php 
if ($_POST['text'] != $_POST['code']) {
    echo json_encode(array('success' => 0));
} else {
    echo json_encode(array('success' => 1));
}
?>