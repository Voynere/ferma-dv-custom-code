<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/wp-load.php';
require __DIR__ . '/functions.php';

//$offer_id = (($product->is_type('variable')) ? $variations[$i]['variation_id'] : $product->get_id());
//$offer = new WC_Product_Variation(6853);

//echo "<pre>";
//print_R($offer);
//echo "</pre>";

/*
$taxonomy     = 'product_cat';
$orderby      = 'id';  
$show_count   = 0;      // 1 for yes, 0 for no
$pad_counts   = 0;      // 1 for yes, 0 for no
$hierarchical = 1;      // 1 for yes, 0 for no  
$title        = '';  
$empty        = 0;




$product_id = get_field( "razbivka_vesa", 6853);
echo "<pre>";
print_r($product_id);
echo "</pre>";
die;
*/

$response['categories'] = [];

$args = array(
	'taxonomy'	=> 'product_cat',
	'orderby'	=> 'id'
);
/*
$categories = get_categories($args);
foreach ($categories as $category){
	$category_thumbnail_id = get_woocommerce_term_meta($category->cat_ID, 'thumbnail_id', true);
	$thumbnail_image_url = wp_get_attachment_url($category_thumbnail_id);
	$img_hash = sha1_file($thumbnail_image_url);
	array_push($response['categories'], ['id' => $category->cat_ID, 'parentId' => $category->parent, 'name' => $category->name, 'sortOrder' => 0, 'images' => ['hash' => $img_hash, 'url' => $thumbnail_image_url]]); 	
}
*/
$query = new WC_Product_Query( array(
    'limit' => -1,
//    'orderby' => 'date',
//    'order' => 'DESC',
    'return' => 'ids',
));

$response['items'] = [];
$products = $query->get_products();

echo "<pre>";
//print_R($products);
echo "</pre>";

foreach($products as $product){
	$product_info = wc_get_product($product);


echo "<pre>";
print_r($product_info);
echo "</pre>";
/*
echo $product_info->get_weight().'<br/>';
echo $product_info->get_length().'<br/>';
echo $product_info->get_width().'<br/>';
echo $product_info->get_height().'<br/>';
echo $product_info->get_dimensions().'<br/>';


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
*/
//'parentId' => $category->parent, 'name' => $category->name, 'sortOrder' => 0, 'images' => ['hash' => $img_hash, 'url' => $thumbnail_image_url]]); 	
}

echo "<pre>";
print_R($response);
echo "</pre>";

/*

echo "<pre>";
//print_R($product_info);
echo "</pre>";


echo $product_info->get_name()."<br/>";
echo $product_info->get_description()."<br/>";
echo $product_info->get_price()."<br/>";
echo $product_info->get_status()."<br/>";
echo $product_info->get_sku()."<br/>";
echo "<pre>";
print_R($product_info->get_category_ids());
echo "</pre>";

//echo $product_info->get_image()."<br/>";
$image = wp_get_attachment_image_src( get_post_thumbnail_id('6668'), 'single-post-thumbnail' );

echo "<pre>";
print_R($image);
echo "</pre>";
*/
//2fd4e1c6 7a2d28fc ed849ee1 bb76e739 1b93eb12
//21ee0783 62577492 395bb4b7 2fde646c 4d13a311
?>