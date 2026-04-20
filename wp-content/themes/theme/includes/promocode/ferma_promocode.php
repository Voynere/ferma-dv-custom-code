<?php
/* Работа с промокодом */

if(!function_exists('ferma_check_promocode')) {
	add_action('woocommerce_checkout_update_order_review', 'ferma_check_promocode');

	function ferma_check_promocode() {
		global $woocommerce;

		$promocode = (isset($_COOKIE['ferma_promo_code']) && $_COOKIE['ferma_promo_code'] != '') ? $_COOKIE['ferma_promo_code'] : false;

		if ( ! $promocode ) {
			WC()->session->set( 'promocode_items', null );
			return;
		}

		$local = q_get_local_promocode( $promocode );

		if ( $local ) {
			$results = array();
			$items   = $woocommerce->cart->get_cart();

			foreach ( $items as $cart_item_key => $values ) {
				/** @var WC_Product $product */
				$product = $values['data'];
				$sku     = $product->get_sku();
				if ( ! $sku ) {
					$sku = get_field('kodtovara', $product->get_id());
				}

				if ( $sku && $sku === $local['gift_sku'] ) {
					$orig_price = (float) $product->get_regular_price();
					if ( $local['discount_type'] === 'percent' ) {
						$new_price = round( $orig_price * (100 - $local['discount_val']) / 100, 2 );
					} else {
						$new_price = $local['discount_val'];
					}

					$results[] = array(
						'product_id'      => $product->get_id(),
						'old_price'       => $orig_price,
						'new_price'       => $new_price,
						'discount_type'   => $local['discount_type'],
						'discount_value'  => $local['discount_val'],
					);
				}
			}

			WC()->session->set( 'promocode_items', $results );



			return;
		}
		if($promocode) {
			$data_of_items = array();
			$items = $woocommerce->cart->get_cart();

			foreach($items as $item => $values) {
				$product =  wc_get_product( $values['data']->get_id());
				$price = get_post_meta($values['product_id'] , '_price', true);
				$newprice = (float)$price;
				$total_for_head += $newprice * $values['quantity'];
				$categories = $product->get_category_ids();
				$category_id = ! empty( $categories ) ? $categories[0] : 0;
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
				$data_of_items[] = $new_query;
			}

			$data = ferma_get_kilbil_promo($promocode, $data_of_items);

			$results = [];

			foreach ($data->_bill_data->items as $item) {
				$code = $item->code;
				$discount = $item->discounted_price;

				$result = array(
				  'code' => $code,
				  'discounted_price' => $discount,
				);

				// Добавляем этот массив в результаты
				$results[] = $result;
			}

			WC()->session->set( 'promocode_items', $results );
		} else {
			WC()->session->set( 'promocode_items', null );
		}
	}

	function ferma_get_kilbil_promo($promocode, $items)
	{
		$arr = array (
			"client_id" => 16880312,
			"type" => 0,
			"bonus_out" => "0",
			"max_bonus_out" => 0,
			"move_id" => "341343153",
			"shift_number" => 563,
			"doc_open_dt" => "16.11.2023 17:27:35",
			"doc_open_dt" => time(),

			"goods_data" => json_encode($items),
			'promo_codes' => json_encode(array(
				'coupons' => array(
					array(
						'coupon' => $promocode,
					),
				),
			)),
		);

		$url = "https://bonus.kilbil.ru/load/processsale?h=02a425e4c45cec2e816d41a872898a23";
		$content = json_encode($arr);
		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_HEADER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, array("Content-type: application/json"));
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $content);

		$json_response = curl_exec($curl);
		$obj = json_decode($json_response);

		curl_close($curl);

		return $obj;
	}


	if(!is_admin()) {
		add_filter('woocommerce_product_get_price', 'ferma_promo_code_price', 99, 2);
	}
	//add_filter('woocommerce_get_price', 'ferma_promo_code_price', 99, 2);

	function ferma_promo_code_price( $price, $product )
	{
		if ( ! isset( WC()->session ) ) {
			return $price;
		}

		$items = WC()->session->get( 'promocode_items' );
		if ( ! $items ) {
			return $price;
		}

		$product_id = $product->get_id();
		$kodtovara  = get_field( 'kodtovara', $product_id );
		if ( empty( $kodtovara ) ) {
			$kodtovara = $product->get_sku();
		}

		foreach ( $items as $item ) {

			// Локальный промо Qxxx
			if ( isset( $item['product_id'] ) ) {
				if ( intval( $item['product_id'] ) === $product_id ) {
					$price = $item['new_price'];
					break;
				}
			}

			// Старый формат KilBil
			if ( isset( $item['code'] ) && isset( $item['discounted_price'] ) ) {
				if ( $item['code'] == $kodtovara ) {
					$price = $item['discounted_price'];
					break;
				}
			}
		}

		return $price;
	}
}