<?php
/**
 * Проверка кода при смене телефона (аккаунт). Выдаёт подписанный handoff-токен вместо сырого user ID в cookie.
 */
require_once dirname( __FILE__ ) . '/../../../wp-load.php';

nocache_headers();
header( 'Content-Type: application/json; charset=UTF-8' );

if ( ! isset( $_POST['text'], $_POST['code'] ) ) {
	echo wp_json_encode( array( 'success' => 0 ) );
	exit;
}

if ( (string) $_POST['text'] !== (string) $_POST['code'] ) {
	echo wp_json_encode( array( 'success' => 0 ) );
	exit;
}

$response = array( 'success' => 1 );

if ( function_exists( 'is_user_logged_in' ) && is_user_logged_in() && function_exists( 'ferma_snemanomera_handoff_issue' ) ) {
	$handoff = ferma_snemanomera_handoff_issue( get_current_user_id() );
	if ( $handoff !== '' ) {
		$response['handoff'] = $handoff;
	}
}

echo wp_json_encode( $response );
