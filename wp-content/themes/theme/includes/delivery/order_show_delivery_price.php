<?php

if(!function_exists('ferma_order_show_delivery_price')) {
	//add_action( 'woocommerce_review_order_before_order_total', 'ferma_order_show_delivery_price' );
	
	function ferma_order_show_delivery_price() {
		
		$coords = ($_COOKIE['coords']) ? $_COOKIE['coords'] : '43.111787507251414,131.88327396290603';
		
		$time = ($_COOKIE['delivery_time']) ? $_COOKIE['delivery_time'] : 'evening';
		
		$delivery_price = ferma_get_delivery_price($coords, $time);
		
		if($delivery_price == 0) {
			$delivery_price = 'Бесплатно';
		} else {
			$delivery_price = $delivery_price . ' ₽';
		}
		
		echo '<div class="ferma-shipping-total"><p class="left-corner">Доставка</p>
			<span class="right-corner">' . $delivery_price. '</span></div>';
	}
}