<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://cedcommerce.com
 * @since      1.0.0
 *
 * @package    Ced_Tiktok_Integration_By_CedCommerce
 * @subpackage Ced_Tiktok_Integration_By_CedCommerce/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Ced_Tiktok_Integration_By_CedCommerce
 * @subpackage Ced_Tiktok_Integration_By_CedCommerce/includes
 */
class Ced_Tiktok_Integration_By_CedCommerce {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @var      Ced_Tiktok_Integration_By_CedCommerce_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
		if ( defined( 'CCTS_VERSION' ) ) {
			$this->version = CCTS_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'CCTS';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - CedCommerce_TikTok_Shop_Connector_Loader. Orchestrates the hooks of the plugin.
	 * - CedCommerce_TikTok_Shop_Connector_i18n. Defines internationalization functionality.
	 * - CedCommerce_TikTok_Shop_Connector_Admin. Defines all hooks for the admin area.
	 * - CedCommerce_TikTok_Shop_Connector_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-CCTS-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'includes/class-CCTS-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( __DIR__ ) . 'admin/class-CCTS-admin.php';

		$this->loader = new Ced_Tiktok_Integration_By_CedCommerce_Loader();
	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Ced_Tiktok_Integration_By_CedCommerce_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 */
	private function set_locale() {

		$plugin_i18n = new Ced_Tiktok_Integration_By_CedCommerce_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Ced_Tiktok_Integration_By_CedCommerce_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'ced_tiktok_add_menus', 25 );
		$this->loader->add_filter( 'ced_add_marketplace_menus_array', $plugin_admin, 'ced_tiktok_add_marketplace_menus_to_array', 13 );
		$this->loader->add_action( 'rest_api_init', $plugin_admin, 'ced_tiktok_add_callback_url_endpoint_authorization', 10 );
		$this->loader->add_filter( 'woocommerce_rest_api_get_rest_namespaces', $plugin_admin, 'ced_tiktok_woocommerce_rest_api_set_rest_namespaces' );
		$this->loader->add_filter( 'woocommerce_api_permissions_in_scope', $plugin_admin, 'ced_tiktok_change_app_permission', 10 );
		$this->loader->add_action( 'woocommerce_product_options_pricing', $plugin_admin, 'ced_tiktok_create_custom_field', 999 );
		$this->loader->add_action( 'save_post', $plugin_admin, 'ced_tiktok_save_data_on_simple_product', 10, 2 );
		$this->loader->add_action( 'woocommerce_variation_options_pricing', $plugin_admin, 'ced_tiktok_add_custom_field_to_variations', 10, 3 );
		$this->loader->add_action( 'woocommerce_save_product_variation', $plugin_admin, 'ced_tiktok_save_custom_field_variations', 10, 2 );
		$this->loader->add_action( 'save_post', $plugin_admin, 'ced_tiktok_save_metadata', 24, 2 );
		$this->loader->add_action( 'add_meta_boxes', $plugin_admin, 'ced_tiktok_add_order_metabox', 24 );
		$this->loader->add_filter( 'manage_edit-shop_order_columns', $plugin_admin, 'ced_tiktok_add_column_order_section', 20 );
		$this->loader->add_action( 'manage_shop_order_posts_custom_column', $plugin_admin, 'ced_tiktok_column_order_section_callback', 20, 2 );
		$this->loader->add_action( 'wp_ajax_ced_tiktok_connect_account', $plugin_admin, 'ced_tiktok_connect_account' );
		$this->loader->add_action( 'wp_ajax_ced_tiktok_manual_connect_account', $plugin_admin, 'ced_tiktok_manual_connect_account' );
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Ced_Tiktok_Integration_By_CedCommerce_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
}
