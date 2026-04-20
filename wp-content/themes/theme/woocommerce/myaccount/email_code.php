<?
// пример использования
require_once "SendMailSmtpClass.php"; // подключаем класс
$mailSMTP = new SendMailSmtpClass('zakaz@ferma-dv.ru', '9pQ%tAN6', 'ssl://smtp.yandex.ru', 'Ferma-dv', 465);
// $mailSMTP = new SendMailSmtpClass('логин', 'пароль', 'хост', 'имя отправителя');
 
// заголовок письма
$headers= "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=utf-8\r\n"; // кодировка письма
$headers .= "From: Ferma-dv <zakaz@ferma-dv.ru>\r\n"; // от кого письмо
$result =  $mailSMTP->send($_POST['email'], 'Ваш код для входа:', 'Код подтверждения входа: ' . $_POST['email_code_send'] . ' . Приятного пользования услугами нашего сайта!', $headers); // отправляем письмо

?>