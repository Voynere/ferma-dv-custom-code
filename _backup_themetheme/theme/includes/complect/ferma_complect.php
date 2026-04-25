<?php
function ferma_product_complect() {
	global $product;
    if( current_user_can( 'administrator' ) ) {
		$complect_second_product_id = get_field('complect_product_second', $product->get_id());
		$complect_third_product_id = get_field('complect_product_third', $product->get_id());
		
		if($complect_second_product_id && $complect_third_product_id) {
			
			$first_is_available = ferma_product_is_available($product->get_id());
			$second_is_available = ferma_product_is_available($complect_second_product_id);
			$third_is_available = ferma_product_is_available($complect_third_product_id);
			
			if(!$first_is_available || !$second_is_available || !$third_is_available) {
				return false;
			}
			
			$complect_second_product = wc_get_product( $complect_second_product_id );
			$complect_third_product = wc_get_product( $complect_third_product_id );
			
			$discount_first = get_field('complect_discount_first', 'option');
			$discount_second = get_field('complect_discount_second', 'option');
			$discount_third = get_field('complect_discount_third', 'option');
			
			$total = $product->get_price() + $complect_second_product->get_price() + $complect_third_product->get_price();
			$total_discount = ($product->get_price() - ($product->get_price() / 100 * $discount_first))
				+ ($complect_second_product->get_price() - ($complect_second_product->get_price() / 100 * $discount_second))
				+ ($complect_third_product->get_price() - ($complect_third_product->get_price() / 100 * $discount_third));
			
			require_once(get_template_directory() . "/includes/complect/template.php");
		}
	}
}
add_action( 'woocommerce_after_single_product_summary', 'ferma_product_complect', 15 );

//add_action('woocommerce_add_to_cart', 'product_complect_add_to_cart', 10, 6 );
function product_complect_add_to_cart() {
	$product_id = (isset($_POST['add-to-cart']) && $_POST['add-to-cart'] > 0) ? $_POST['add-to-cart'] : 0;
	if( current_user_can( 'administrator' ) ) {
		$is_complect = (isset($_POST['complect']) && $_POST['complect'] == 1) ? true : false;
		
		
		if($is_complect && $product_id > 0) {
			$product = wc_get_product($product_id);
			
			$complect_second_product_id = get_field('complect_product_second', $product->get_id());
			$complect_third_product_id = get_field('complect_product_third', $product->get_id());
			
			if($complect_second_product_id && $complect_third_product_id) {
				$first_is_available = ferma_product_is_available($product->get_id());
				$second_is_available = ferma_product_is_available($complect_second_product_id);
				$third_is_available = ferma_product_is_available($complect_third_product_id);
				
				if(!$first_is_available || !$second_is_available || !$third_is_available) {
					return false;
				}
				
				$discount_first = get_field('complect_discount_first', 'option');
				$discount_second = get_field('complect_discount_second', 'option');
				$discount_third = get_field('complect_discount_third', 'option');
				
				global $woocommerce;
				echo 'sdfsd';
				exit();
			}
			
		}
	}
}

add_filter( 'woocommerce_add_cart_item_data', 'ferma_add_complect_to_cart_item_data', 20, 2 );
function ferma_add_complect_to_cart_item_data( $cart_item_data, $product_id ){
	if( current_user_can( 'administrator' ) ) {
		$cart_item_data['is_complect'] = (isset($_POST['complect']) && $_POST['complect'] == 1) ? true : false;

		return $cart_item_data;
	}
}

add_action( 'woocommerce_before_calculate_totals', 'ferma_change_complect_cart_item_price', 30, 1 );
function ferma_change_complect_cart_item_price( $cart ) {
    foreach ( $cart->get_cart() as $cart_item ) {
        if($cart_item['is_complect']) {
			$complect_second_product_id = get_field('complect_product_second', $cart_item['data']->get_id());
			$complect_third_product_id = get_field('complect_product_third', $cart_item['data']->get_id());
			
			$complect_second_product = wc_get_product( $complect_second_product_id );
			$complect_third_product = wc_get_product( $complect_third_product_id );
			
			$discount_first = get_field('complect_discount_first', 'option');
			$discount_second = get_field('complect_discount_second', 'option');
			$discount_third = get_field('complect_discount_third', 'option');
			
			$total = $cart_item['data']->get_price() + $complect_second_product->get_price() + $complect_third_product->get_price();
			$total_discount = ($cart_item['data']->get_price() - ($cart_item['data']->get_price() / 100 * $discount_first))
				+ ($complect_second_product->get_price() - ($complect_second_product->get_price() / 100 * $discount_second))
				+ ($complect_third_product->get_price() - ($complect_third_product->get_price() / 100 * $discount_third));
			
            $cart_item['data']->set_price($total_discount);
		}
    }
}

//add_filter( 'woocommerce_cart_item_name', 'just_a_test', 10, 3 );
function just_a_test( $item_name,  $cart_item,  $cart_item_key ) {
	if($cart_item['is_complect']) {
		$complect_second_product_id = get_field('complect_product_second', $cart_item['data']->get_id());
		$complect_third_product_id = get_field('complect_product_third', $cart_item['data']->get_id());
			
		$complect_second_product = wc_get_product( $complect_second_product_id );
		$complect_third_product = wc_get_product( $complect_third_product_id );
		
		
		echo '<strong>Комплект</strong><br>' . $item_name.'<br>+ ' . $complect_second_product->get_title() . "<br>+ " . $complect_third_product->get_title();
	} else {
		echo $item_name;
	}
}