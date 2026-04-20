<?php
require_once( realpath(dirname(__FILE__)) . "/wp-load.php" );

$good_ids = [];
$good_ids_with_discount = [];
		
/*while( have_rows('zp_goods', 'option') ) {
	the_row();
	$zp_goods_items = get_sub_field('zp_goods_items');
	$zp_goods_procent = get_sub_field('zp_goods_procent');
			
	foreach($zp_goods_items as $zp_goods_item) {
		if(!in_array($zp_goods_item, $good_ids)) {
			$good_ids[] = $zp_goods_item;
		}
		if(!in_array($zp_goods_item, $good_ids_with_discount[$zp_goods_procent])) {
			$good_ids_with_discount[$zp_goods_procent][] = $zp_goods_item;
		}
	}
}
		
while( have_rows('zp_cats', 'option') ) {
	the_row();
	$zp_cats_items = get_sub_field('zp_cats_items');
	$zp_cats_procent = get_sub_field('zp_cats_procent');
	
	print_r($zp_cats_items);
	
	foreach($zp_cats_items as $zp_cats_item) {
		$products = wc_get_products(array(
			'limit' => -1,
			'tax_query'             => array(
				array(
					'taxonomy'      => 'product_cat',
					'field'         => 'term_id',
					'terms'         => $zp_cats_item,
					'operator'      => 'IN'
				),
			),
		));

		foreach($products as $product) {
			$product_id = $product->get_id();
			if(!in_array($product_id, $good_ids)) {
				$good_ids[] = $product_id;
				
			}
			if(!in_array($product_id, $good_ids_with_discount[$zp_cats_procent])) {
				$good_ids_with_discount[$zp_cats_procent][] = $product_id;
			}
		}
	}
}*/
	
$goods = [
	'good_ids' => $good_ids,
	'good_ids_with_discount' => $good_ids_with_discount
];

file_put_contents(realpath(dirname(__FILE__)) . "/green-friday.json", json_encode($goods));