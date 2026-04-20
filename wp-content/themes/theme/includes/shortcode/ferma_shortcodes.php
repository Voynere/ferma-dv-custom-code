<?php

if(!function_exists('ferma_goods_shortcode')) {

	function ferma_goods_shortcode($atts) {
		$atts = shortcode_atts(array(
			'art' => [],
		), $atts, 'ferma_goods');
		
		$arts = explode(",", $atts['art']);
		
		return do_shortcode('[products skus="' . $atts['art'] . '"]');
	}
	
	add_shortcode('ferma_goods', 'ferma_goods_shortcode');
}