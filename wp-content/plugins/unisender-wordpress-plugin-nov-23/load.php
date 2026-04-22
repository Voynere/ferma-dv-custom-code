<?php

require_once WPUNISENDER_PLUGIN_DIR . '/includes/UnisenderApi.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/View.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/UnisenderFormFieldTypes.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/UnisenderFormFieldStyle.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/l10n.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/capabilities.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/functions.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/formatting.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/form-functions.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/form-template.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/form.php';
require_once WPUNISENDER_PLUGIN_DIR . '/includes/controller.php';

if ( is_admin() ) {
	require_once WPUNISENDER_PLUGIN_DIR . '/admin/admin.php';
}

class WPUNISENDER {


	/**
	 * Retrieves a named entry from the option array of Unisender.
	 *
	 * @param string $name Array item key.
	 * @param mixed $default Optional. Default value to return if the entry
	 *                       does not exist. Default false.
	 * @return mixed Array value tied to the $name key. If nothing found,
	 *               the $default value will be returned.
	 */
	public static function get_option( $name, $default = false ) {
		$option = get_option( 'wpunisender' );

		if ( false === $option ) {
			return $default;
		}

		if ( isset( $option[$name] ) ) {
			return $option[$name];
		} else {
			return $default;
		}
	}


	/**
	 * Update an entry value on the option array of Unisender.
	 *
	 * @param string $name Array item key.
	 * @param mixed $value Option value.
	 */
	public static function update_option( $name, $value ) {
		$option = get_option( 'wpunisender' );
		$option = ( false === $option ) ? array() : (array) $option;
		$option = array_merge( $option, array( $name => $value ) );
		update_option( 'wpunisender', $option );
	}
}


add_action( 'plugins_loaded', 'wpunisender', 10, 0 );

/**
 * Registers WordPress shortcodes.
 */
function wpunisender() {
	add_shortcode( 'unisender-form', 'wpunisender_form_tag_func' );
}


add_action( 'init', 'wpunisender_init', 10, 0 );

/**
 * Registers post types for forms.
 */
function wpunisender_init() {
	wpunisender_get_request_uri();
	wpunisender_register_post_types();

	do_action( 'wpunisender_init' );
}


add_action( 'admin_init', 'wpunisender_upgrade', 10, 0 );

/**
 * Upgrades option data when necessary.
 */
function wpunisender_upgrade() {
	$old_ver = WPUNISENDER::get_option( 'version', '0' );
	$new_ver = WPUNISENDER_VERSION;

	if ( $old_ver == $new_ver ) {
		return;
	}

	do_action( 'wpunisender_upgrade', $new_ver, $old_ver );

	WPUNISENDER::update_option( 'version', $new_ver );
}


add_action( 'activate_' . WPUNISENDER_PLUGIN_BASENAME, 'wpunisender_install', 10, 0 );

/**
 * Callback tied to plugin activation action hook. Attempts to create
 * initial user dataset.
 */
function wpunisender_install() {
	if ( $opt = get_option( 'wpunisender' ) ) {
		return;
	}

	wpunisender_register_post_types();
	wpunisender_upgrade();

	if ( get_posts( array( 'post_type' => 'wpunisender_form' ) ) ) {
		return;
	}

	$form = WPUNISENDER_Form::get_template(
		array(
			'title' =>
				/* translators: title of your first form. %d: number fixed to '1' */
				sprintf( __( 'Форма %d', 'unisender' ), 1 ),
		)
	);

	$form->save();
}
