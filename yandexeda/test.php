<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

require __DIR__ . '/../wp-load.php';
require __DIR__ . '/functions.php';



// Get Product ID

//$order = wc_create_order();
//$order->add_product( wc_get_product( 6853 ), 1 );
//$order->calculate_totals();
//die;



//$order = wc_get_order( 6914 );
//view($order);
//die;

function bbloomer_customer_list() {
   $customer_query = new WP_User_Query(
      array(
         'fields' => 'ID',
         'role' => 'customer',         
      )
   );
   return $customer_query->get_results();
}

foreach ( bbloomer_customer_list() as $customer_id ) {
   $customer = new WC_Customer( $customer_id );
   view($customer);
}
die;

   $customer_query = new WP_User_Query(
      array(
         'fields' => 'ID',
         'role' => 'customer',         
      )
   );
   $res =  $customer_query->get_results();
view($res);
die;

$product = wc_get_product(6853);  
echo $product->get_id().'<br/>';
  
// Get Product General Info
  
echo '1: '.$product->get_type().'<br/>';
echo $product->get_name().'<br/>';
echo $product->get_slug().'<br/>';
echo $product->get_date_created().'<br/>';
echo $product->get_date_modified().'<br/>';
echo $product->get_status().'<br/>';
echo $product->get_featured().'<br/>';
echo $product->get_catalog_visibility().'<br/>';
echo $product->get_description().'<br/>';
echo $product->get_short_description().'<br/>';
echo $product->get_sku().'<br/>';
echo $product->get_menu_order().'<br/>';
echo $product->get_virtual().'<br/>';
echo get_permalink( $product->get_id() ).'<br/>';
  
// Get Product Prices
  
echo '2: '.$product->get_price().'<br/>';
echo $product->get_regular_price().'<br/>';
echo $product->get_sale_price().'<br/>';
echo $product->get_date_on_sale_from().'<br/>';
echo $product->get_date_on_sale_to().'<br/>';
echo $product->get_total_sales().'<br/>';
  
// Get Product Tax, Shipping & Stock
  
echo '3: '.$product->get_tax_status().'<br/>';
echo $product->get_tax_class().'<br/>';
echo $product->get_manage_stock().'<br/>';
echo $product->get_stock_quantity().'<br/>';
echo $product->get_stock_status().'<br/>';
echo $product->get_backorders().'<br/>';
echo $product->get_sold_individually().'<br/>';
echo $product->get_purchase_note().'<br/>';
echo $product->get_shipping_class_id().'<br/>';
  
// Get Product Dimensions
  
echo '4: '.$product->get_weight().'<br/>';
echo $product->get_length().'<br/>';
echo $product->get_width().'<br/>';
echo $product->get_height().'<br/>';
echo $product->get_dimensions().'<br/>';
  
// Get Linked Products
  
echo '5: '.$product->get_upsell_ids().'<br/>';
echo $product->get_cross_sell_ids().'<br/>';
echo $product->get_parent_id().'<br/>';
  
// Get Product Variations and Attributes
 
echo '6: '.$product->get_children().'<br/>'; // get variations
echo $product->get_attributes().'<br/>';
echo $product->get_default_attributes().'<br/>';
echo $product->get_attribute( 'attributeid' ).'<br/>'; //get specific attribute value
  
// Get Product Taxonomies
  
echo '7: '.$product->get_categories().'<br/>';
view ($product->get_category_ids());
view ($product->get_tag_ids());
  
// Get Product Downloads
  
echo '8: '.$product->get_downloads().'<br/>';
echo $product->get_download_expiry().'<br/>';
echo $product->get_downloadable().'<br/>';
echo $product->get_download_limit().'<br/>';
  
// Get Product Images
echo 'Get Product Images'.'<br/>';  
echo $product->get_image_id().'<br/>';
echo $product->get_image().'<br/>';
echo $product->get_gallery_image_ids().'<br/>';
  
// Get Product Reviews
echo ' Get Product Reviews'.'<br/>';  
 
echo $product->get_reviews_allowed().'<br/>';
echo $product->get_rating_counts().'<br/>';
echo $product->get_average_rating().'<br/>';
echo $product->get_review_count().'<br/>';
?>