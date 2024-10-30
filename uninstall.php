<?php

/**
 * Fired when the plugin is uninstalled.
 *
 *
 * @link       https://cedcommerce.com
 * @since      1.0.0
 *
 * @package    Ced_Tiktok_Integration_By_CedCommerce
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}
delete_option( 'ced_tiktok_woo_connection_data' );
