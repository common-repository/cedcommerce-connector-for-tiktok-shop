<?php
/**
 * Fired during plugin activation
 *
 * @link       https://cedcommerce.com
 * @since      1.0.0
 *
 * @package    Ced_Tiktok_Integration_By_CedCommerce
 * @subpackage Ced_Tiktok_Integration_By_CedCommerce/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Ced_Tiktok_Integration_By_CedCommerce
 * @subpackage Ced_Tiktok_Integration_By_CedCommerce/includes
 */
class Ced_Tiktok_Integration_By_CedCommerce_Activator {
 
	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		global $wp_filesystem;

		// Check if WP_Filesystem is available
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Initialize the WP_Filesystem
		if ( ! $wp_filesystem ) {
			WP_Filesystem();
		}
		
		$wooconnection_data = get_option( 'ced_woo_cedcommerce_connection_data', '' );
		if ( ! empty( $wooconnection_data ) ) {
			update_option( 'ced_tiktok_woo_connection_data', $wooconnection_data );
		}

		$content = '<?php $data = file_get_contents( "php://input" );
		file_put_contents( "ced_tiktok_api_details.txt", $data );';
		$content_api = '';
		$file = plugin_dir_path(__FILE__) . '/ced/ced_tiktok_woo_callback.php';
		$wp_filesystem->put_contents( $file, $content,0755 );
		$file = plugin_dir_path(__FILE__) . '/ced/ced_tiktok_api_details.txt';
		$wp_filesystem->put_contents( $file, $content_api,0777 );
	}
}
