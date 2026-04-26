<?php
/**
 * Q promocode post type registration.
 *
 * @package Theme
 */

// CPT "Промокоды".
add_action(
	'init',
	function () {
		register_post_type(
			'q_promocode',
			array(
				'labels'        => array(
					'name'          => 'Промокоды Q',
					'singular_name' => 'Промокод Q',
					'add_new'       => 'Добавить промокод',
					'add_new_item'  => 'Добавить промокод',
					'edit_item'     => 'Редактировать промокод',
				),
				'public'        => false,
				'show_ui'       => true,
				'menu_position' => 25,
				'menu_icon'     => 'dashicons-tickets-alt',
				'supports'      => array( 'title' ),
			)
		);
	}
);
