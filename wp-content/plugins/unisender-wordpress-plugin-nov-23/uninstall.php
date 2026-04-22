<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit();
}

function wpunisender_delete_plugin() {
	global $wpdb;

	delete_option( 'wpunisender' );

	$posts = get_posts(
		array(
			'numberposts' => -1,
			'post_type' => 'wpunisender_form',
			'post_status' => 'any',
		)
	);

	foreach ( $posts as $post ) {
		wp_delete_post( $post->ID, true );
	}

	$wpdb->query( sprintf(
		"DROP TABLE IF EXISTS %s",
		$wpdb->prefix . 'unisender'
	) );
}

if ( ! defined( 'WPUNISENDER_VERSION' ) ) {
	wpunisender_delete_plugin();
}
