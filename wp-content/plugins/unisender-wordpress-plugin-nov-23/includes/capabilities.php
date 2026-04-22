<?php

add_filter( 'map_meta_cap', 'wpunisender_map_meta_cap', 10, 4 );

function wpunisender_map_meta_cap( $caps, $cap, $user_id, $args ) {
	$meta_caps = array(
		'wpunisender_edit_form' => WPUNISENDER_ADMIN_READ_WRITE_CAPABILITY,
		'wpunisender_edit_forms' => WPUNISENDER_ADMIN_READ_WRITE_CAPABILITY,
		'wpunisender_read_form' => WPUNISENDER_ADMIN_READ_CAPABILITY,
		'wpunisender_read_forms' => WPUNISENDER_ADMIN_READ_CAPABILITY,
		'wpunisender_delete_form' => WPUNISENDER_ADMIN_READ_WRITE_CAPABILITY,
		'wpunisender_delete_forms' => WPUNISENDER_ADMIN_READ_WRITE_CAPABILITY,
		'wpunisender_manage_options' => 'manage_options',
		'wpunisender_submit' => 'read',
	);

	$meta_caps = apply_filters( 'wpunisender_map_meta_cap', $meta_caps );

	$caps = array_diff( $caps, array_keys( $meta_caps ) );

	if ( isset( $meta_caps[$cap] ) ) {
		$caps[] = $meta_caps[$cap];
	}

	return $caps;
}
