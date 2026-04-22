<?php
/*
Plugin Name: Unisender
Plugin URI: https://unisender.com/
Description:
Author:
Author URI:
Text Domain: unisender
Domain Path: /languages/
Version: 1.0.0
*/

define( 'WPUNISENDER_VERSION', '1.0.0' );

define( 'WPUNISENDER_REQUIRED_WP_VERSION', '5.7' );

define( 'WPUNISENDER_TEXT_DOMAIN', 'unisender' );

define( 'WPUNISENDER_PLUGIN', __FILE__ );

define( 'WPUNISENDER_PLUGIN_BASENAME', plugin_basename( WPUNISENDER_PLUGIN ) );

define( 'WPUNISENDER_PLUGIN_NAME', trim( dirname( WPUNISENDER_PLUGIN_BASENAME ), '/' ) );

define( 'WPUNISENDER_PLUGIN_DIR', untrailingslashit( dirname( WPUNISENDER_PLUGIN ) ) );

if ( ! defined( 'WPUNISENDER_LOAD_JS' ) ) {
	define( 'WPUNISENDER_LOAD_JS', true );
}

if ( ! defined( 'WPUNISENDER_LOAD_CSS' ) ) {
	define( 'WPUNISENDER_LOAD_CSS', true );
}

if ( ! defined( 'WPUNISENDER_AUTOP' ) ) {
	define( 'WPUNISENDER_AUTOP', true );
}

if ( ! defined( 'WPUNISENDER_USE_PIPE' ) ) {
	define( 'WPUNISENDER_USE_PIPE', true );
}

if ( ! defined( 'WPUNISENDER_ADMIN_READ_CAPABILITY' ) ) {
	define( 'WPUNISENDER_ADMIN_READ_CAPABILITY', 'edit_posts' );
}

if ( ! defined( 'WPUNISENDER_ADMIN_READ_WRITE_CAPABILITY' ) ) {
	define( 'WPUNISENDER_ADMIN_READ_WRITE_CAPABILITY', 'publish_pages' );
}

if ( ! defined( 'WPUNISENDER_VERIFY_NONCE' ) ) {
	define( 'WPUNISENDER_VERIFY_NONCE', false );
}

// Deprecated, not used in the plugin core. Use wpunisender_plugin_url() instead.
define( 'WPUNISENDER_PLUGIN_URL',
	untrailingslashit( plugins_url( '', WPUNISENDER_PLUGIN ) )
);

require_once WPUNISENDER_PLUGIN_DIR . '/load.php';