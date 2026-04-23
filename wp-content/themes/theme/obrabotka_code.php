<?php
/*
Template Name: Мой шаблон страницы1345
Template Post Type: post, page, product
*/
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';

if ( ! session_id() && ! headers_sent() ) {
	session_start();
}

header( 'Content-Type: application/json; charset=UTF-8' );

global $wpdb;

$user = isset( $_POST['phone'] ) ? sanitize_text_field( wp_unslash( $_POST['phone'] ) ) : '';
$code = isset( $_POST['text'] ) ? preg_replace( '/\D+/', '', (string) $_POST['text'] ) : '';
$session_code = isset( $_SESSION['phone_code'] ) ? preg_replace( '/\D+/', '', (string) $_SESSION['phone_code'] ) : '';

if ( strlen( preg_replace( '/\D+/', '', $user ) ) < 10 ) {
	echo wp_json_encode( array( 'success' => 0 ) );
	exit;
}

if ( $session_code === '' || $code === '' || $code !== $session_code ) {
	echo wp_json_encode( array( 'success' => 0 ) );
	exit;
}

unset( $_SESSION['phone_code'] );

$hash = wp_hash_password( $code );

if ( username_exists( $user ) ) {
	$data = get_user_by( 'login', $user );
	wp_set_auth_cookie( $data->ID );
	update_user_meta( $data->ID, 'billing_phone', sanitize_text_field( $user ) );
	update_user_meta( $data->ID, 'last_login', time() );
} else {
	$wpdb->insert(
		'wp_users',
		array(
			'user_login'    => $user,
			'user_pass'     => $hash,
			'user_market'   => isset( $_COOKIE['market'] ) ? sanitize_text_field( wp_unslash( $_COOKIE['market'] ) ) : '',
			'user_nicename' => $user,
			'user_url'      => 'none',
			'display_name'  => $user,
		),
		array( '%s', '%s', '%s', '%s', '%s', '%s' )
	);

	$data = get_user_by( 'login', $user );
	if ( $data ) {
		wp_set_auth_cookie( $data->ID );
		update_user_meta( $data->ID, 'billing_phone', sanitize_text_field( $user ) );
		update_user_meta( $data->ID, 'last_login', time() );
	}
}

echo wp_json_encode( array( 'success' => 1 ) );
exit;