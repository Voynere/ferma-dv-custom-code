<?
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
global $wpdb;
global $woocommerce;
$items = $woocommerce->cart->get_cart();
$data_of_items = array();
$total_for_head = 0;

foreach($items as $item => $values) { 
    $product =  wc_get_product( $values['data']->get_id());
    $price = get_post_meta($values['product_id'] , '_price', true);
    $newprice = (float)$price;
    $total_for_head += $newprice * $values['quantity'];
     // Get product category and ACF field value
     $categories = $product->get_category_ids();
     $category_id = ! empty( $categories ) ? $categories[0] : 0; // Get first category ID
     $category = get_term($category_id);
     $category_text = get_field('ajdi_tovara', $category);
     $kodtovara = get_field('kodtovara', $product->get_id());
    if(empty($kodtovara)) {
        $kodtovara = $product->get_sku();
    }

    $new_query = array(

        "code" => $kodtovara,
        "barcode" => "2400000005926",
        "vendor_code" => $kodtovara,
        "name" => $product->get_title(),
        "price" => $newprice,
        "quantity" => (int) $values['quantity'],
        "total" => $newprice * $values['quantity'],
        "minPrice" => 0,
        "maxDiscount" => 100,
        "discounted_price" => $newprice * $values['quantity'],
        "discounted_total" => $newprice * $values['quantity'],
        "parent_code" => $category_text,
        "parent_vendor_code" => ""

      );
    array_push($data_of_items, $new_query); 
}

$arr = array (
    "client_id" => 16880312,
    "type" => 0,
    "bonus_out" => "0",
    "max_bonus_out" => 0,
    "move_id" => "341343153",
    "shift_number" => 563,
    "doc_open_dt" => "16.11.2023 17:27:35",
	"doc_open_dt" => time(),

    "goods_data" => json_encode($data_of_items),
    'promo_codes' => json_encode(array(
        'coupons' => array(
			array(
				'coupon' => $_POST['result'],
			),
		),
	)),
);


$url = "https://bonus.kilbil.ru/load/processsale?h=614c6b88ac346607512f34afcf91326d";
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
  //print_r($content);
  //print_r($obj);
  
  $discount = $obj->_bill_data->discount;
  setcookie('discount', $discount, time() + 3600, '/');
  // Начинаем сессию
session_start();

// Получаем объект со всеми данными
$data = json_decode($json_response);

  // Создаем пустой массив для хранения данных
  $results = array();

  // Проходимся по всем элементам массива items
  foreach ($data->_bill_data->items as $item) {

    // Получаем значение code и discount из текущего элемента
    $code = $item->code;
    $discount = $item->discount;

    // Создаем новый массив с этими значениями
    $result = array(
      'code' => $code,
      'discount' => $discount,
    );

    // Добавляем этот массив в результаты
    $results[] = $result;
  }

  // Сохраняем массив результатов в переменной сессии
  $_SESSION['results'] = $results;

  // Выводим результаты


  if ($obj->_bill_data->discount != 0) {
    echo json_encode(array('success' => 1, 'count' => $obj->_bill_data->discount, 'result' => $_POST['result']));
  } else {
    echo json_encode(array('success' => 0));
  }
  
?>