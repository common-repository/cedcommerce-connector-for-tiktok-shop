<?php
/**
 *
 * @link              https://cedcommerce.com
 * @since             1.0.0
 * @package           CedCommerce_TikTok_Shop_Connector
 *
 * @wordpress-plugin
 * Plugin Name:       CedCommerce Connector for TikTok Shop
 * Plugin URI:        https://wordpress.org/plugins/search/cedcommerce/
 * Description:       CedCommerce Connector for TikTok Shop allows merchants to list their products on Tiktok marketplaces and manage all the orders from their WooCommerce store.
 * Version:           1.0.0
 * Author:            CedCommerce
 * Author URI:        https://cedcommerce.com
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       cedcommerce-connector-for-tiktok-shop
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'CCTS_VERSION', '1.0.0' );
define( 'CCTS_PREFIX', 'ced_tiktok_integration' );
define( 'CCTS_DIRPATH', plugin_dir_path( __FILE__ ) );
define( 'CCTS_URL', plugin_dir_url( __FILE__ ) );
define( 'CCTS_ABSPATH', untrailingslashit( plugin_dir_path( __DIR__ ) ) );
define( 'CCTS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-CCTS-activator.php
 */
function CCTS_activate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-CCTS-activator.php';
	Ced_Tiktok_Integration_By_CedCommerce_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-CCTS-deactivator.php
 */
function CCTS_deactivate() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-CCTS-deactivator.php';
	Ced_Tiktok_Integration_By_CedCommerce_Deactivator::deactivate();
}

register_deactivation_hook( __FILE__, 'CCTS_deactivate' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-CCTS.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function CCTS_run() {

	$plugin = new Ced_Tiktok_Integration_By_CedCommerce();
	$plugin->run();
}

/**
 * Function to show admin notice
 *
 * @return void
 */
function ced_tiktok_admin_notice_activation() {
	if ( get_transient( 'ced-connector-admin-notice' ) ) {?>
		<div class="updated notice is-dismissible">
			<p><?php esc_html_e( 'Welcome to Tiktok Integration For WooCommerce.', 'cedcommerce-connector-for-tiktok-shop' ); ?></p>
		</div>
		<?php
	}
}

/**
 * Woocommerce active plugins hook
 *
 * @since 1.0.0
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	CCTS_run();
	register_activation_hook( __FILE__, 'CCTS_activate' );
	add_action( 'admin_notices', 'ced_tiktok_admin_notice_activation' );
} else {
	add_action( 'admin_init', 'CCTS_deactivate_woo_missing' );
}


function CCTS_deactivate_woo_missing() {
	deactivate_plugins( CCTS_PLUGIN_BASENAME );
	add_action( 'admin_notices', 'CCTS_woo_missing_notice' );
	if ( isset( $_GET['activate'] ) ) {
		unset( $_GET['activate'] );
	}
}

function CCTS_woo_missing_notice() {
	// translators: %s: search term !!
	echo '<div class="notice notice-error is-dismissible"><p>' . sprintf( esc_html( __( 'Tiktok Integration For Woocommerce requires WooCommerce to be installed and active. You can download %s from here.', 'cedcommerce-connector-for-tiktok-shop' ) ), '<a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce</a>' ) . '</p></div>';
}
