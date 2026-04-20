<?php

if(!function_exists('ferma_get_current_shops')) {
	
	function ferma_get_current_shops() {
		static $shops_cache = null;

		if ($shops_cache !== null) {
			return $shops_cache;
		}

		$shops = [];
		
		if ( is_user_logged_in() ) {
			$delivery = get_user_meta( get_current_user_id(), 'delivery' , true );
			
			if($delivery == 1) {
				if(isset($_COOKIE['key_market']) && $_COOKIE['key_market'] != '') {
					$shops = [
						$_COOKIE['key_market']
					];
				} else {
					$point = get_user_meta( get_current_user_id(), 'billing_point' , true );
					$shops = [
						$point
					];
				}
			} else {
				if(isset($_COOKIE['coords']) && $_COOKIE['coords'] != '') {
					$coords = $_COOKIE['coords'];
				} else {
					$coords = get_user_meta( get_current_user_id(), 'coords' , true );
				}
				
				$shops = ferma_get_shops_by_coords($coords);
			}
		} else {
			$delivery = $_COOKIE['delivery'];
			
			if($delivery == 1) {
				if(isset($_COOKIE['key_market']) && $_COOKIE['key_market'] != '') {
					$shops = [
						$_COOKIE['key_market']
					];
				}
			} else {
				if(isset($_COOKIE['coords']) && $_COOKIE['coords'] != '') {
					$coords = $_COOKIE['coords'];
					
					$shops = ferma_get_shops_by_coords($coords);
				}
			}
		}
		
		$shops_cache = $shops;

		return $shops_cache;
	}
	
}

if(!function_exists('ferma_validate_add_cart_item')) {

	function ferma_validate_add_cart_item( $passed, $product_id, $quantity, $variation_id = '', $variations= '' ) {
		
		$shops = ferma_get_current_shops();
		
		if(count($shops) == 0) {
			return $passed;
		}
		
		$is_possible = false;
		
		$product = wc_get_product( $product_id );
		foreach($shops as $shop) {
			$balance = $product->get_meta($shop);
			if(floatval($balance) > 0) {
				$is_possible = true;
			}
		}
		
		if ( !$is_possible ) {
			$passed = false;	
			wc_add_notice( "Ошибка! " . $product->get_title() . " нет в наличии.", 'error' );
		}
		
		return $passed;
	}
	
	add_filter( 'woocommerce_add_to_cart_validation', 'ferma_validate_add_cart_item', 10, 5 );
}

if(!function_exists('limit_cart_item_quantity')) {
	
	function limit_cart_item_quantity( $cart_item_key, $quantity, $old_quantity, &$cart ){
		global $woocommerce;
		//$woocommerce->session->set( 'reload_checkout ', 'true' );
		
		$shops = ferma_get_current_shops();
		
		$is_possible = false;
		
		$product = wc_get_product( $cart->cart_contents[ $cart_item_key ]['product_id'] );
		$ratio = get_weight_ratio( $cart->cart_contents[ $cart_item_key ]['product_id'] );
		$quantity_with_ratio = $quantity * $ratio;
		
		$available = 0;
		
		foreach($shops as $shop) {
			$available = $available + $product->get_meta($shop);
		}
		
		if( $quantity_with_ratio > $available ) {
			//$cart->cart_contents[ $cart_item_key ]['quantity'] = $available;
			$cart->cart_contents[ $cart_item_key ]['quantity'] = $old_quantity;
			wc_add_notice( $cart->cart_contents[ $cart_item_key ]['data']->name . " - превышен остаток на складе", 'error' );
		}
		
		//wc_add_notice( "Превышен остаток на складе", 'error' );
		//WC()->cart->set_session();
		
	}

	add_action( 'woocommerce_after_cart_item_quantity_update', 'limit_cart_item_quantity', 20, 4 );
	
	
	//add_action( 'woocommerce_before_calculate_totals', 'ferma_modify_max_quantity' );
	function ferma_modify_max_quantity( $cart_object ) {
		
	}
}

if(!function_exists('ferma_product_is_available')) {
	function ferma_product_is_available( $product_id ) {
		static $availability_cache = [];

		$product_id = (int) $product_id;
		if (isset($availability_cache[$product_id])) {
			return $availability_cache[$product_id];
		}

		$shops = ferma_get_current_shops();
		
		if(!isset($shops[0]) || $shops[0] == '') {
			$availability_cache[$product_id] = true;
			return true;
		}
		
		$is_available = false;
		
		$store_stocks = function_exists('ferma_get_store_stocks_with_fallback')
			? ferma_get_store_stocks_with_fallback($product_id, $shops)
			: [];

		$product = null;
		foreach($shops as $shop) {
			if (array_key_exists($shop, $store_stocks)) {
				$balance = $store_stocks[$shop];
			} else {
				if ($product === null) {
					$product = wc_get_product( $product_id );
				}
				$balance = $product ? $product->get_meta($shop) : 0;
			}
			if(floatval($balance) > 0) {
				$is_available = true;
				break;
			}
		}
		
		$availability_cache[$product_id] = $is_available;

		return $is_available;
	}
}