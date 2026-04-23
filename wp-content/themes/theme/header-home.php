<?php
/**
 * Proxy header to keep non-home pages in sync with base header layout/styles.
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require get_template_directory() . '/header.php';
