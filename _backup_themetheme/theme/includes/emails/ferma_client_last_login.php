<?php

// Запоминаем последний вход

if(!function_exists('ferma_client_last_login')) {
	
	function ferma_client_last_login( $user_login, $user ) {
		update_user_meta( $user->ID, 'last_login', time() );
	}
	
	add_action( 'wp_login', 'ferma_client_last_login', 10, 2 );
	
}