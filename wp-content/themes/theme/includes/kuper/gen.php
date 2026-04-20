<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/wp-load.php');

$exclude_terms = get_field('kuper_categories', 'option');
$exclude_products = get_field('kuper_products', 'option');

$price_markup = get_field('kuper_markup', 'option');

global $wpdb;

if(count($exclude_terms) > 0) {
	$query = "SELECT t.term_id AS ID, t.name AS TITLE, t.slug AS SLUG
		FROM {$wpdb->prefix}terms AS t
		LEFT JOIN {$wpdb->prefix}term_taxonomy AS ta
			ON ta.term_id = t.term_id            
		WHERE ta.taxonomy='product_cat'
			AND ta.term_id NOT IN ( '" . implode( "', '" , $exclude_terms ) . "' )
		ORDER BY t.name ASC";
} else {
	$query = "SELECT t.term_id AS ID, t.name AS TITLE, t.slug AS SLUG
		FROM {$wpdb->prefix}terms AS t
		LEFT JOIN {$wpdb->prefix}term_taxonomy AS ta
			ON ta.term_id = t.term_id 
		WHERE ta.taxonomy='product_cat'
		ORDER BY t.name ASC";
}

$categories = $wpdb->get_results($query);

$cat_ids = [];
$cat_list_ids = [];

$dom = new DOMDocument();
$dom->encoding = 'utf-8';
$dom->xmlVersion = '1.0';
$dom->formatOutput = true;

$dom_prices = new DOMDocument();
$dom_prices->encoding = 'utf-8';
$dom_prices->xmlVersion = '1.0';
$dom_prices->formatOutput = true;

$dom_stock = new DOMDocument();
$dom_stock->encoding = 'utf-8';
$dom_stock->xmlVersion = '1.0';
$dom_stock->formatOutput = true;

$dom_stock_chkalova = new DOMDocument();
$dom_stock_chkalova->encoding = 'utf-8';
$dom_stock_chkalova->xmlVersion = '1.0';
$dom_stock_chkalova->formatOutput = true;

$dom_stock_narodniy = new DOMDocument();
$dom_stock_narodniy->encoding = 'utf-8';
$dom_stock_narodniy->xmlVersion = '1.0';
$dom_stock_narodniy->formatOutput = true;

$dom_stock_more = new DOMDocument();
$dom_stock_more->encoding = 'utf-8';
$dom_stock_more->xmlVersion = '1.0';
$dom_stock_more->formatOutput = true;

$dom_stock_eger = new DOMDocument();
$dom_stock_eger->encoding = 'utf-8';
$dom_stock_eger->xmlVersion = '1.0';
$dom_stock_eger->formatOutput = true;

$dom_stock_timir = new DOMDocument();
$dom_stock_timir->encoding = 'utf-8';
$dom_stock_timir->xmlVersion = '1.0';
$dom_stock_timir->formatOutput = true;

$dom_stock_ussur = new DOMDocument();
$dom_stock_ussur->encoding = 'utf-8';
$dom_stock_ussur->xmlVersion = '1.0';
$dom_stock_ussur->formatOutput = true;

$offers_file_name = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/theme/includes/kuper/kuper-offers.xml';
$prices_file_name = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/theme/includes/kuper/kuper-prices.xml';
$stock_file_name = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/theme/includes/kuper/kuper-stock.xml';
$stock_chkalova_file_name = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/theme/includes/kuper/kuper-stock-chkalova.xml';
$stock_narodniy_file_name = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/theme/includes/kuper/kuper-stock-narodniy.xml';
$stock_more_file_name = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/theme/includes/kuper/kuper-stock-more.xml';

$stock_eger_file_name = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/theme/includes/kuper/kuper-stock-eger.xml';
$stock_timir_file_name = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/theme/includes/kuper/kuper-stock-timir.xml';
$stock_ussur_file_name = $_SERVER['DOCUMENT_ROOT'] . '/wp-content/themes/theme/includes/kuper/kuper-stock-ussur.xml';

$goods = $dom->createElement('goods_data');
$attr_goods_date = new DOMAttr('goods_date', date("Y-m-d"));
$goods->setAttributeNode($attr_goods_date);

$price_data = $dom_prices->createElement('price_data');
$attr_prices_date = new DOMAttr('price_date', date("Y-m-d"));
$price_data->setAttributeNode($attr_prices_date);

$stock_data = $dom_stock->createElement('stock_data');
$attr_stock_date = new DOMAttr('stock_date', date("Y-m-d"));
$stock_data->setAttributeNode($attr_stock_date);

$stock_data_chkalova = $dom_stock_chkalova->createElement('stock_data');
$attr_stock_date = new DOMAttr('stock_date', date("Y-m-d"));
$stock_data_chkalova->setAttributeNode($attr_stock_date);

$stock_data_narodniy = $dom_stock_narodniy->createElement('stock_data');
$attr_stock_date = new DOMAttr('stock_date', date("Y-m-d"));
$stock_data_narodniy->setAttributeNode($attr_stock_date);

$stock_data_more = $dom_stock_more->createElement('stock_data');
$attr_stock_date = new DOMAttr('stock_date', date("Y-m-d"));
$stock_data_more->setAttributeNode($attr_stock_date);

$stock_data_eger = $dom_stock_eger->createElement('stock_data');
$attr_stock_date = new DOMAttr('stock_date', date("Y-m-d"));
$stock_data_eger->setAttributeNode($attr_stock_date);

$stock_data_timir = $dom_stock_timir->createElement('stock_data');
$attr_stock_date = new DOMAttr('stock_date', date("Y-m-d"));
$stock_data_timir->setAttributeNode($attr_stock_date);

$stock_data_ussur = $dom_stock_ussur->createElement('stock_data');
$attr_stock_date = new DOMAttr('stock_date', date("Y-m-d"));
$stock_data_ussur->setAttributeNode($attr_stock_date);

$categories_node = $dom->createElement('categories');

foreach($categories as $key => $category) {
	$category_node = $dom->createElement('category');
	
	$category_id_node = $dom->createElement('id', $category->ID);
	$category_node->appendChild($category_id_node);
	
	$category_title_node = $dom->createElement('name', $category->TITLE);
	$category_node->appendChild($category_title_node);
	
	$category_position_node = $dom->createElement('position', 0);
	$category_node->appendChild($category_position_node);
	
	$category_parent_node = $dom->createElement('parent_id', 0);
	$category_node->appendChild($category_parent_node);
	
	$categories_node->appendChild($category_node);
	
	$cat_ids[] = $category->SLUG;
	$cat_list_ids[] = $category->ID;
}

$goods->appendChild($categories_node);

$products = wc_get_products( 
	array( 
		'status' => 'publish',
		'limit' => -1,
		'category' => $cat_ids
	)
);

$offers_node = $dom->createElement('offers');

foreach( $products as $key => $product ) {
	$product_id   = $product->get_id();
    $product_name = $product->get_name();
	$product_status = ($product->get_stock_status() == "instock") ? 'ACTIVE' : 'INACTIVE';
	$product_cats_ids = wc_get_product_term_ids( $product_id, 'product_cat' );
	$image_id  = $product->get_image_id();
	$image_url = wp_get_attachment_image_url( $image_id, 'full' );
	$price = $product->get_regular_price();
	$stock_quantity = $product->get_stock_quantity();
	$product_details = $product->get_data();
	
	if(!in_array($product_cats_ids[0], $cat_list_ids)) {
		continue;
	}
	
	$store_stock_ids = [
		'028e05a7-b4fa-11ee-0a80-1198000442be',
		'b24e4c35-9609-11eb-0a80-0d0d008550c2',
		'cab1caa9-da10-11eb-0a80-07410026c356',
		'7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93',
		'a99d6fdf-0970-11ed-0a80-0ed600075845',
		'9c9dfcc4-733f-11ec-0a80-0da1013a560d',
	];
	$store_stocks = function_exists('ferma_get_store_stocks_with_fallback')
		? ferma_get_store_stocks_with_fallback($product_id, $store_stock_ids)
		: [];

	$chkalova_stock_quantity = $store_stocks['028e05a7-b4fa-11ee-0a80-1198000442be'] ?? $product->get_meta('028e05a7-b4fa-11ee-0a80-1198000442be');
	$narodniy_stock_quantity = $store_stocks['b24e4c35-9609-11eb-0a80-0d0d008550c2'] ?? $product->get_meta('b24e4c35-9609-11eb-0a80-0d0d008550c2');
	$more_stock_quantity = $store_stocks['cab1caa9-da10-11eb-0a80-07410026c356'] ?? $product->get_meta('cab1caa9-da10-11eb-0a80-07410026c356');
	
	$eger_stock_quantity = $store_stocks['7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93'] ?? $product->get_meta('7c0dc9ce-ce1e-11ea-0a80-09ca000e5e93');
	$timir_stock_quantity = $store_stocks['a99d6fdf-0970-11ed-0a80-0ed600075845'] ?? $product->get_meta('a99d6fdf-0970-11ed-0a80-0ed600075845');
	$ussur_stock_quantity = $store_stocks['9c9dfcc4-733f-11ec-0a80-0da1013a560d'] ?? $product->get_meta('9c9dfcc4-733f-11ec-0a80-0da1013a560d');
	
	if(!$image_url || !$price || $stock_quantity == 0) {
		continue;
	}
	
	$ingredients = $product->get_attribute('pa_sostav');
	$calories_per_100 = $product->get_attribute('pa_energeticheskaya-cen');
	$fats_per_100 = $product->get_attribute('pa_жиры-г');
	$proteins_per_100 = $product->get_attribute('pa_белки-г');
	$carbohydrates_per_100 = $product->get_attribute('pa_uglevody-g');
	$storage_conditions = $product->get_attribute('pa_usloviya-hraneniya');
	$shelf_life = $product->get_attribute('pa_srok-godnosti');
	$description = $product->get_attribute('pa_opisanie-dlya-sajta');
	if($description == '' && $product_details['description'] != '') {
		$description = $product_details['description'];
	}
	
	$offer_node = $dom->createElement('offer');
	
	$offer_id_node = $dom->createElement('id', $product_id);
	$offer_node->appendChild($offer_id_node);
	
	$offer_name_node = $dom->createElement('name', $product_name);
	$offer_node->appendChild($offer_name_node);
	
	$offer_position_node = $dom->createElement('position', ($key + 1));
	$offer_node->appendChild($offer_position_node);
	
	$offer_status_node = $dom->createElement('status', $product_status);
	$offer_node->appendChild($offer_status_node);
	
	$offer_is_option_node = $dom->createElement('is_option', 'false');
	$offer_node->appendChild($offer_is_option_node);
	
	$offer_is_excisable_node = $dom->createElement('is_excisable', 'false');
	$offer_node->appendChild($offer_is_excisable_node);
	
	$offer_category_id_node = $dom->createElement('category_id', $product_cats_ids[0]);
	$offer_node->appendChild($offer_category_id_node);
	
	$volume = $product->get_attribute('pa_obyom-ml');
	$weight = $product->get_attribute('pa_ves-g');
	
	$ratio = 1;
	
	$price_ratio = 1;
	
	$per = 'per_item';
	
	if(!empty($volume) || !empty($weight)) {
		
		if(!empty($volume)) {
			$offer_volume_node = $dom->createElement('volume_netto', $volume);
			$attr_volume = new DOMAttr('unit', 'ML');
			$offer_volume_node->setAttributeNode($attr_volume);
			$offer_node->appendChild($offer_volume_node);
		}
		
		if(!empty($weight)) {
			$offer_weight_node = $dom->createElement('weight_netto', $weight);
			$attr_weight = new DOMAttr('unit', 'G');
			$offer_weight_node->setAttributeNode($attr_weight);
			$offer_node->appendChild($offer_weight_node);
		}		
		
	} else {
		$by_weight = (get_field( "razbivka_vesa", $product_id ) == 'да') ? true : false;
		$ratio = get_weight_ratio($product_id);
		
		if($by_weight && $ratio < 1) {
			$price_ratio = 1 / $ratio;
			$ratio = 1;
		}
		
		if($ratio != 1) {
			$offer_weight_node = $dom->createElement('weight_netto', $ratio*1000);
			$attr_weight = new DOMAttr('unit', 'G');
			$offer_weight_node->setAttributeNode($attr_weight);
			$offer_node->appendChild($offer_weight_node);
		} else {
			if($by_weight) {
				$offer_weight_node = $dom->createElement('weight_netto', "1");
				$attr_weight = new DOMAttr('unit', 'KG');
				$offer_weight_node->setAttributeNode($attr_weight);
				$per = 'per_kilo';
			} else {
				$offer_weight_node = $dom->createElement('weight_netto', "");
			}
			$offer_node->appendChild($offer_weight_node);
		}
	}
	
	$offer_weight_br_node = $dom->createElement('weight_brutto', "");
	$offer_node->appendChild($offer_weight_br_node);
	
	$offer_vat_node = $dom->createElement('vat', "NO_VAT");
	$offer_node->appendChild($offer_vat_node);
	
	$images_node = $dom->createElement('images');
	$image_node = $dom->createElement('image');
	$image_url_node = $dom->createElement('image_url', $image_url);
	$image_name_node = $dom->createElement('image_name', basename($image_url));
	$image_node->appendChild($image_url_node);
	$image_node->appendChild($image_name_node);
	$images_node->appendChild($image_node);
	
	$offer_node->appendChild($images_node);
	
	
	$product_info_node = $dom->createElement('product_info');
	$ingredients_node = $dom->createElement('ingredients', $ingredients);
	$calories_per_100_node = $dom->createElement('calories_per_100g', $calories_per_100);
	$calories_per_portion_node = $dom->createElement('calories_per_portion', "");
	$proteins_per_100_node = $dom->createElement('proteins_per_100g', $proteins_per_100);
	$proteins_per_portion_node = $dom->createElement('proteins_per_portion', "");
	$fats_per_100_node = $dom->createElement('fats_per_100g', $fats_per_100);
	$fats_per_portion_node = $dom->createElement('fats_per_portion', "");
	$carbohydrates_per_100_node = $dom->createElement('carbohydrates_per_100g', $carbohydrates_per_100);
	$carbohydrates_per_portion_node = $dom->createElement('carbohydrates_per_portion', "");
	$storage_conditions_node = $dom->createElement('storage_conditions', $storage_conditions);
	$shelf_life_node = $dom->createElement('shelf_life', $shelf_life);
	$description_node = $dom->createElement('description', $description);
	$offers_attributes_node = $dom->createElement('offers_attributes', "");
	$additional_info_node = $dom->createElement('additional_info', "");
	$options_groups_node = $dom->createElement('options_groups', "");
	
	$product_info_node->appendChild($ingredients_node);
	$product_info_node->appendChild($calories_per_100_node);
	$product_info_node->appendChild($calories_per_portion_node);
	$product_info_node->appendChild($proteins_per_100_node);
	$product_info_node->appendChild($proteins_per_portion_node);
	$product_info_node->appendChild($fats_per_100_node);
	$product_info_node->appendChild($fats_per_portion_node);
	$product_info_node->appendChild($carbohydrates_per_100_node);
	$product_info_node->appendChild($carbohydrates_per_portion_node);
	$product_info_node->appendChild($storage_conditions_node);
	$product_info_node->appendChild($shelf_life_node);
	$product_info_node->appendChild($description_node);
	$product_info_node->appendChild($offers_attributes_node);
	$product_info_node->appendChild($additional_info_node);
	$product_info_node->appendChild($options_groups_node);
	
	$offer_node->appendChild($product_info_node);
	
	$offers_node->appendChild($offer_node);
	
	
	$offer_price_node = $dom_prices->createElement('offer');
	
	if($price_markup > 0) {
		$price = $price + (($price / 100) * $price_markup);
	}
	
	//$price = $price * $price_ratio;
	
	$offer_price_id_node = $dom_prices->createElement('id', $product_id);
	$offer_price_node->appendChild($offer_price_id_node);
	$offer_price_type_node = $dom_prices->createElement('type', $per);
	$offer_price_node->appendChild($offer_price_type_node);
	$offer_price_regular_node = $dom_prices->createElement('regular_price', $price*$ratio);
	$offer_price_node->appendChild($offer_price_regular_node);
	$offer_price_discount_node = $dom_prices->createElement('discount_price', "");
	$offer_price_node->appendChild($offer_price_discount_node);
	$offer_price_date_from_node = $dom_prices->createElement('discount_from_date', "");
	$offer_price_node->appendChild($offer_price_date_from_node);
	$offer_price_date_to_node = $dom_prices->createElement('discount_to_date', "");
	$offer_price_node->appendChild($offer_price_date_to_node);
	
	$price_data->appendChild($offer_price_node);
	
	$offer_stock_node = $dom_stock->createElement('offer');
	$offer_stock_id_node = $dom_stock->createElement('id', $product_id);
	$offer_stock_node->appendChild($offer_stock_id_node);
	$offer_stock_stocks_node = $dom_stock->createElement('shop_stocks', $stock_quantity);
	$offer_stock_node->appendChild($offer_stock_stocks_node);
	
	$stock_data->appendChild($offer_stock_node);
	
	// Чкалова
	if($chkalova_stock_quantity > 0) {
		$offer_chkalova_stock_node = $dom_stock_chkalova->createElement('offer');
		$offer_chkalova_stock_id_node = $dom_stock_chkalova->createElement('id', $product_id);
		$offer_chkalova_stock_node->appendChild($offer_chkalova_stock_id_node);
		$offer_chkalova_stock_stocks_node = $dom_stock_chkalova->createElement('shop_stocks', $chkalova_stock_quantity);
		$offer_chkalova_stock_node->appendChild($offer_chkalova_stock_stocks_node);
		
		$stock_data_chkalova->appendChild($offer_chkalova_stock_node);
	}
	
	// Реми-Сити Народный
	if($narodniy_stock_quantity > 0) {
		$offer_narodniy_stock_node = $dom_stock_narodniy->createElement('offer');
		$offer_narodniy_stock_id_node = $dom_stock_narodniy->createElement('id', $product_id);
		$offer_narodniy_stock_node->appendChild($offer_narodniy_stock_id_node);
		$offer_narodniy_stock_stocks_node = $dom_stock_narodniy->createElement('shop_stocks', $narodniy_stock_quantity);
		$offer_narodniy_stock_node->appendChild($offer_narodniy_stock_stocks_node);
		
		$stock_data_narodniy->appendChild($offer_narodniy_stock_node);
	}
	
	if($more_stock_quantity > 0) {
		// ТЦ МОРЕ
		$offer_more_stock_node = $dom_stock_more->createElement('offer');
		$offer_more_stock_id_node = $dom_stock_more->createElement('id', $product_id);
		$offer_more_stock_node->appendChild($offer_more_stock_id_node);
		$offer_more_stock_stocks_node = $dom_stock_more->createElement('shop_stocks', $more_stock_quantity);
		$offer_more_stock_node->appendChild($offer_more_stock_stocks_node);
		
		$stock_data_more->appendChild($offer_more_stock_node);
	}
	
	// Эгершельд
	if($eger_stock_quantity > 0) {
		$offer_eger_stock_node = $dom_stock_eger->createElement('offer');
		$offer_eger_stock_id_node = $dom_stock_eger->createElement('id', $product_id);
		$offer_eger_stock_node->appendChild($offer_eger_stock_id_node);
		$offer_eger_stock_stocks_node = $dom_stock_eger->createElement('shop_stocks', $eger_stock_quantity);
		$offer_eger_stock_node->appendChild($offer_eger_stock_stocks_node);
		
		$stock_data_eger->appendChild($offer_eger_stock_node);
	}
	
	// Тимирязева
	if($timir_stock_quantity > 0) {
		$offer_timir_stock_node = $dom_stock_timir->createElement('offer');
		$offer_timir_stock_id_node = $dom_stock_timir->createElement('id', $product_id);
		$offer_timir_stock_node->appendChild($offer_timir_stock_id_node);
		$offer_timir_stock_stocks_node = $dom_stock_timir->createElement('shop_stocks', $timir_stock_quantity);
		$offer_timir_stock_node->appendChild($offer_timir_stock_stocks_node);
		
		$stock_data_timir->appendChild($offer_timir_stock_node);
	}
	
	if($ussur_stock_quantity > 0) {
		// Уссурийск
		$offer_ussur_stock_node = $dom_stock_ussur->createElement('offer');
		$offer_ussur_stock_id_node = $dom_stock_ussur->createElement('id', $product_id);
		$offer_ussur_stock_node->appendChild($offer_ussur_stock_id_node);
		$offer_ussur_stock_stocks_node = $dom_stock_ussur->createElement('shop_stocks', $ussur_stock_quantity);
		$offer_ussur_stock_node->appendChild($offer_ussur_stock_stocks_node);
		
		$stock_data_ussur->appendChild($offer_ussur_stock_node);
	}
}

$goods->appendChild($offers_node);

$dom->appendChild($goods);
$dom->save($offers_file_name);

$dom_prices->appendChild($price_data);
$dom_prices->save($prices_file_name);

$dom_stock->appendChild($stock_data);
$dom_stock->save($stock_file_name);

$dom_stock_more->appendChild($stock_data_more);
$dom_stock_more->save($stock_more_file_name);

$dom_stock_chkalova->appendChild($stock_data_chkalova);
$dom_stock_chkalova->save($stock_chkalova_file_name);

$dom_stock_narodniy->appendChild($stock_data_narodniy);
$dom_stock_narodniy->save($stock_narodniy_file_name);

$dom_stock_eger->appendChild($stock_data_eger);
$dom_stock_eger->save($stock_eger_file_name);

$dom_stock_timir->appendChild($stock_data_timir);
$dom_stock_timir->save($stock_timir_file_name);

$dom_stock_ussur->appendChild($stock_data_ussur);
$dom_stock_ussur->save($stock_ussur_file_name);