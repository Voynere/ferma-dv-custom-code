<?
/*
Template Name: Мой шаблон страницы1345
Template Post Type: post, page, product
*/
$path = $_SERVER['DOCUMENT_ROOT'];

include_once $path . '/wp-config.php';
include_once $path . '/wp-load.php';
include_once $path . '/wp-includes/wp-db.php';
include_once $path . '/wp-includes/pluggable.php';
global $wpdb;
$user = htmlspecialchars($_POST["phone"]);
if(strlen($user) < 10) {
	echo json_encode(array('success' => 0));
	exit();
}
if ($_POST['text'] != $_SESSION['phone_code']) {
    echo json_encode(array('success' => 0));
} else {
    $hash = wp_hash_password( $_POST['code'] );
    if ( username_exists( $user ) ) {
        $data = get_user_by('login', $user);
        wp_set_auth_cookie( $data->ID, $remember, $secure, $token );
        update_user_meta( $data->ID, 'billing_phone', sanitize_text_field($_POST['phone']) );
		update_user_meta( $data->ID, 'last_login', time() );
    }
    else {
        $wpdb->insert(
            'wp_users',
            array( 'user_login' => $user, 'user_pass' => $hash , 'user_market' => $_COOKIE["market"], 'user_nicename' => $user, 'user_url' => 'none', 'display_name' => $user),
            array( '%s', '%s' )
        );
        $data = get_user_by('login', $user);
        wp_set_auth_cookie( $data->ID, $remember, $secure, $token );
        update_user_meta( $data->ID, 'billing_phone', sanitize_text_field($_POST['phone']) );
		update_user_meta( $data->ID, 'last_login', time() );
    }
    echo json_encode(array('success' => 1));
}
?>