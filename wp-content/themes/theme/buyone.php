<?
/*
Template Name: Мой шаблон страницы1345
Template Post Type: post, page, product
*/
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
global $wpdb;

global $woocommerce;
$cur_user_id = get_current_user_id(); 
if (isset($_POST['phone'])) {
    $phone = $_POST['phone'];
} else {
    $phone = get_user_meta( $cur_user_id, "billing_phone", true );
}
if (isset($_POST['name'])) {
    $name = $_POST['name'];
} else {
    $name = get_user_meta( $cur_user_id, "first_name", true );
}

if ($woocommerce->cart->cart_contents_total < 600) {
 echo json_encode(array('success' => 4));
} else {

$count = 0;
$items = $woocommerce->cart->get_cart();
foreach($items as $item => $values) {
    $count++;
}
if ($count == 0) {
 echo json_encode(array('success' => 3));
} else {
    $order = wc_create_order(array('customer_id'=>$cur_user_id));
    $address = array(
        'first_name' => $name,
        'last_name'  => '',
        'company'    => '',
        'email'      => $email,
        'phone'      => $phone,
        'address_1'  => get_user_meta( $cur_user_id, "billing_address_1", true ),
        'address_2'  => get_user_meta( $cur_user_id, "billing_address_2", true ),
        'city'       => get_user_meta( $cur_user_id, "billing_city", true ),
        'state'      => get_user_meta( $cur_user_id, "billing_state", true ),
        'postcode'   => get_user_meta( $cur_user_id, "billing_postcode", true ),
        'country'    => get_user_meta( $cur_user_id, "billing_country", true )
    ); 

    foreach($items as $item => $values) {
        $order->add_product( get_product( $values['product_id'] ), $values['quantity'] );
    }
 $order->set_address( $address, 'billing' ); //Добавляем данные о доставке
 $order->set_address( $address, 'shipping' ); // и оплате
 $order->update_status('on-hold');
 $order->calculate_totals(); //подбиваем сумму и видим что наш заказ появился в админке
 $woocommerce->cart->empty_cart();
 echo json_encode(array('success' => 1));
}
}
?>