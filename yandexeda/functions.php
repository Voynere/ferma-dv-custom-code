<?php
function generateEAN($number){
  $code = '200' . str_pad($number, 9, '0');
  $weightflag = true;
  $sum = 0;
  // Weight for a digit in the checksum is 3, 1, 3.. starting from the last digit.
  // loop backwards to make the loop length-agnostic. The same basic functionality
  // will work for codes of different lengths.
  for ($i = strlen($code) - 1; $i >= 0; $i--)
  {
    $sum += (int)$code[$i] * ($weightflag?3:1);
    $weightflag = !$weightflag;
  }
  $code .= (10 - ($sum % 10)) % 10;
  return $code;
}


function request_list_products(){
	$response['categories'] = [];
	$args = array(
		'taxonomy'	=> 'product_cat',
		'orderby'	=> 'id'
	);

	$categories = get_categories($args);
	foreach ($categories as $category){
		$category_thumbnail_id = get_woocommerce_term_meta($category->cat_ID, 'thumbnail_id', true);
		$thumbnail_image_url = wp_get_attachment_url($category_thumbnail_id);
		$img_hash = sha1_file($thumbnail_image_url);
		if ($thumbnail_image_url){
			array_push($response['categories'], ['id' => $category->cat_ID, 'parentId' => $category->parent, 'name' => $category->name, 'images' => ['hash' => $img_hash, 'url' => $thumbnail_image_url]]); 	
		}else{
			array_push($response['categories'], ['id' => $category->cat_ID, 'parentId' => $category->parent, 'name' => $category->name]); 	
		}
	}

	$query = new WC_Product_Query( array(
    	'limit' => -1,
    	'return' => 'ids',
	));

	$response['items'] = [];
	$products = $query->get_products();

	foreach($products as $product){
		$product_info = wc_get_product($product);
		$category = $product_info->get_category_ids();
		$sku = $product_info->get_sku();
	    $barcode = generateEAN($sku);

		array_push($response['items'], 
			[
				'id' => $product_info->get_id(),
				'vendorCode' => $sku,
				'categoryId' => $category[0],
				'name' => $product_info->get_name(),
				'description' => $product_info->get_description(),
				'price' => $product_info->get_price(),
				'barcode' => ['value' => $barcode, 'type' => 'ean13'],
			]);
	}
	view($response);
}

function request_stocks_products(){

	$query = new WC_Product_Query( array(
    	'limit' => -1,
    	'return' => 'ids',
	));

	$response['items'] = [];
	$products = $query->get_products();

	foreach($products as $product){
		$product_info = wc_get_product($product);
		array_push($response['items'], 
			[
				'id' => $product_info->get_id(),
				'stock' => $product_info->get_stock_quantity()
			]);
	}
	view($response);

	
}
function view($data){
	echo "<pre>";
	print_R($data);
	echo "</pre>";
}
?>