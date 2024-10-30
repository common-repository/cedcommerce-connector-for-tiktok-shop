<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://cedcommerce.com
 * @since      1.0.0
 *
 * @package    Ced_Tiktok_Integration_By_CedCommerce
 * @subpackage Ced_Tiktok_Integration_By_CedCommerce/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Ced_Tiktok_Integration_By_CedCommerce
 * @subpackage Ced_Tiktok_Integration_By_CedCommerce/admin
 */
class Ced_Tiktok_Integration_By_CedCommerce_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param    string $plugin_name       The name of this plugin.
	 * @param    string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version     = $version;
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ced_Tiktok_Integration_By_CedCommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ced_Tiktok_Integration_By_CedCommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( ! empty( $_GET['page'] ) && ( 'ced_tiktok_integration' == sanitize_text_field( $_GET['page'] ) || 'cedcommerce-integrations' === sanitize_text_field( $_GET['page'] ) ) ) {
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/CCTS-admin.css', array(), $this->version . time(), 'all' );
		}
	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Ced_Tiktok_Integration_By_CedCommerce_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Ced_Tiktok_Integration_By_CedCommerce_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		if ( ! empty( $_GET['page'] ) && ( 'ced_tiktok_integration' == sanitize_text_field( $_GET['page'] ) || 'cedcommerce-integrations' === sanitize_text_field( $_GET['page'] ) ) ) {
			wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/CCTS-admin.js', array( 'jquery' ), $this->version, false );
			$ajax_nonce     = wp_create_nonce( 'ced-tiktok-ajax-seurity-string' );
			$localize_array = array(
				'ajax_url'   => admin_url( 'admin-ajax.php' ),
				'ajax_nonce' => $ajax_nonce,

			);
			wp_localize_script( $this->plugin_name, 'ced_tiktok_obj', $localize_array );
		}
	}


	/**
	 * Function to add menu in woocommerce
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ced_tiktok_add_menus() {
		global $submenu;
		if ( empty( $GLOBALS['admin_page_hooks']['cedcommerce-integrations'] ) ) {
			add_menu_page( __( 'CedCommerce', 'cedcommerce-connector-for-tiktok-shop' ), __( 'CedCommerce', 'cedcommerce-connector-for-tiktok-shop' ), 'manage_woocommerce', 'cedcommerce-integrations', array( $this, 'ced_marketplace_listing_page' ), CCTS_URL . 'admin/images/logo.png', 12 );
			/**
			 * Filter to add more marketplace indexes of cedcommerce
			 *
			 * @since 1.0.0
			 */
			$menus = apply_filters( 'ced_add_marketplace_menus_array', array() );
			if ( is_array( $menus ) && ! empty( $menus ) ) {
				foreach ( $menus as $key => $value ) {
					add_submenu_page( 'cedcommerce-integrations', $value['name'], $value['name'], 'manage_woocommerce', $value['menu_link'], array( $value['instance'], $value['function'] ) );
				}
			}
		}
	}

	/**
	 * Function to add listing page
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ced_marketplace_listing_page() {
		/**
		 * Filter to add more marketplace indexes of cedcommerce
		 *
		 * @since 1.0.0
		 */
		$active_marketplaces = apply_filters( 'ced_add_marketplace_menus_array', array() );
		if ( is_array( $active_marketplaces ) && ! empty( $active_marketplaces ) ) {
			require CCTS_DIRPATH . 'admin/partials/CCTS-marketplaces.php';
		}
	}

	/**
	 * Function to add marketplace Menu.
	 *
	 * @param array $menus menus list array.
	 * @return array
	 */
	public function ced_tiktok_add_marketplace_menus_to_array( $menus = array() ) {
		$menus[] = array(
			'name'            => 'TikTok Shop',
			'slug'            => 'ced-connector-woocommerce-integration',
			'menu_link'       => 'ced_tiktok_integration',
			'instance'        => $this,
			'function'        => 'ced_tiktok_configuration_page',
			'card_image_link' => CCTS_URL . 'admin/images/tiktok.svg',
		);
		return $menus;
	}

	/**
	 * Function to include file to the menu array.
	 *
	 * @return void
	 */
	public function ced_tiktok_configuration_page() {
		if ( isset( $_GET['page'] ) && 'ced_tiktok_integration' === $_GET['page'] ) {
			include_once CCTS_DIRPATH . 'admin/partials/CCTS-main.php';
		}
	}

	/**
	 * Function to add custom endpoints
	 *
	 * @return void
	 */
	public function ced_tiktok_add_callback_url_endpoint_authorization() {
		// register custom endpoint class.
		require_once CCTS_DIRPATH . 'includes/ced/CCTS-rest-ced-api-controller.php';
	}

	/**
	 * WIll register custom rest api namespace to woocommerce.
	 *
	 * @param array $controllers array containing the existsing controllers.
	 * @since 1.0.0
	 * @return array
	 */
	public function ced_tiktok_woocommerce_rest_api_set_rest_namespaces( $controllers ) {
		$controllers['wc/v3']['ced'] = 'CCTS_REST_Ced_Api_Controller';
		return $controllers;
	}

	/**
	 * Function to change app permission
	 *
	 * @since 1.0.0
	 * @return array
	 */
	public function ced_tiktok_change_app_permission() {
		$permissions[] = __( 'Create webhooks', 'cedcommerce-connector-for-tiktok-shop' );
		$permissions[] = __( 'View and manage orders and sales reports', 'cedcommerce-connector-for-tiktok-shop' );
		$permissions[] = __( 'View and manage products', 'cedcommerce-connector-for-tiktok-shop' );
		return $permissions;
	}

	/**
	 * Function to crate custom fields at product level
	 *
	 * @return void
	 */
	public function ced_tiktok_create_custom_field() {
		global $post;
		if ( is_object( $post ) ) {
			$product = wc_get_product( $post->ID );
			if ( is_object( $product ) ) {
				$connector_ean       = get_post_meta( $post->ID, 'ced_connector_ean', true );
				$ced_connector_brand = get_post_meta( $post->ID, 'ced_connector_brand', true );
				wp_nonce_field( 'ced_tiktok_product_nonce', 'ced_tiktok_product_nonce' );

				if ( 'simple' === $product->get_type() ) {
					?>
					<p class="form-field _sale_price_field wjd-tiktok-simple" style="width: 50%;">
						<label for="ced_connector_ean"> <?php esc_html_e( 'EAN', 'cedcommerce-connector-for-tiktok-shop' ); ?> : </label><input type="text" class="short" style="" name="ced_connector_ean" id="ced_connector_ean" value="<?php echo esc_html( $connector_ean ); ?>" placeholder="<?php esc_html_e( 'Enter EAN', 'cedcommerce-connector-for-tiktok-shop' ); ?>">
					</p>
					<p class="form-field _sale_price_field wjd-tiktok-simple" style="width: 50%;">
						<label for="ced_connector_brand"> <?php esc_html_e( 'Brand', 'cedcommerce-connector-for-tiktok-shop' ); ?> : </label><input type="text" class="short" style="" name="ced_connector_brand" id="ced_connector_brand" value="<?php echo esc_html( $ced_connector_brand ); ?>" placeholder="Enter Brand">
					</p>
					<?php
				} else {
					?>
					<p class="form-field _sale_price_field wjd-tiktok-simple show_if_simple" style="width: 50%;">
						<label for="ced_connector_ean"><?php esc_html_e( 'EAN', 'cedcommerce-connector-for-tiktok-shop' ); ?> : </label><input type="text" class="short" style="" name="ced_connector_ean" id="ced_connector_ean" value="<?php echo esc_html( $connector_ean ); ?>" placeholder="<?php esc_html_e( 'Enter EAN', 'cedcommerce-connector-for-tiktok-shop' ); ?>">
					</p>
					<p class="form-field _sale_price_field wjd-tiktok-simple show_if_simple" style="width: 50%;">
						<label for="ced_connector_brand"> <?php esc_html_e( 'Brand', 'cedcommerce-connector-for-tiktok-shop' ); ?> : </label><input type="text" class="short" style="" name="ced_connector_brand" id="ced_connector_brand" value="<?php echo esc_html( $ced_connector_brand ); ?>" placeholder="<?php esc_html_e( 'Enter Brand', 'cedcommerce-connector-for-tiktok-shop' ); ?>">
					</p>
					<?php
				}
			}
		}
	}

	/**
	 * Function to add custom fields at variation product level
	 *
	 * @param string $loop an iteration count of a variation.
	 * @param array  $variation_data  an array with all variation fields settings.
	 * @param object $variation object of this specific variation.
	 * @return void
	 */
	public function ced_tiktok_add_custom_field_to_variations( $loop, $variation_data, $variation ) {
		$connector_ean       = get_post_meta( $variation->ID, 'ced_connector_ean', true );
		$ced_connector_brand = get_post_meta( $variation->ID, 'ced_connector_brand', true );
		?>
			<p class="form-field variable_regular_price_0_field form-row form-row-first">
				<label for="ced_connector_ean"> <?php esc_html_e( 'EAN', 'cedcommerce-connector-for-tiktok-shop' ); ?> : </label><input type="text" class="short " name="ced_connector_ean[<?php echo esc_attr( $variation->ID ); ?>]" id="ced_connector_ean" value="<?php echo esc_attr( $connector_ean ); ?>" placeholder="<?php esc_html_e( 'Enter EAN', 'cedcommerce-connector-for-tiktok-shop' ); ?>"></input>
			</p>
			<?php wp_nonce_field( 'ced_tiktok_product_nonce', 'ced_tiktok_product_nonce' ); ?>
			<p class="form-field form-row form-row-first">
				<label for="ced_connector_brand"> <?php esc_html_e( 'Brand', 'cedcommerce-connector-for-tiktok-shop' ); ?> : </label><input type="text" class="short" name="ced_connector_brand[<?php echo esc_attr( $variation->ID ); ?>]" id="ced_connector_brand" value="<?php echo esc_attr( $ced_connector_brand ); ?>" placeholder="<?php esc_html_e( 'Enter Brand', 'cedcommerce-connector-for-tiktok-shop' ); ?>"></input>
			</p>
		<?php
	}

	/**
	 * Fucntion to save simple product meta fields
	 *
	 * @param string $post_ID post id.
	 * @return void
	 */
	public function ced_tiktok_save_data_on_simple_product( $post_ID = '' ) {
		if ( empty( $post_ID ) ) {
			return;
		}
		$product = wc_get_product( $post_ID );
		if ( is_object( $product ) ) {
			if ( 'simple' === $product->get_type() ) {
				if ( ! empty( $_POST['ced_tiktok_product_nonce'] ) ) {
					$ced_tiktok_product_nonce = sanitize_text_field( wp_unslash( $_POST['ced_tiktok_product_nonce'] ) );
					if ( wp_verify_nonce( $ced_tiktok_product_nonce, 'ced_tiktok_product_nonce' ) ) {
						$connector_ean       = ! empty( $_POST['ced_connector_ean'] ) ? sanitize_text_field( wp_unslash( $_POST['ced_connector_ean'] ) ) : '';
						$ced_connector_brand = ! empty( $_POST['ced_connector_brand'] ) ? sanitize_text_field( wp_unslash( $_POST['ced_connector_brand'] ) ) : '';
						if ( $connector_ean ) {
							update_post_meta( $post_ID, 'ced_connector_ean', $connector_ean );
						}
						if ( $ced_connector_brand ) {
							update_post_meta( $post_ID, 'ced_connector_brand', $ced_connector_brand );
						}
					}
				}
			}
		}
	}

	/**
	 * Function to save variation fields values
	 *
	 * @param int $variation_id variation product id.
	 * @param int $i an iteration.
	 * @return void
	 */
	public function ced_tiktok_save_custom_field_variations( $variation_id, $i ) {
		if ( ! empty( $_POST['ced_tiktok_product_nonce'] ) ) {
			$ced_tiktok_product_nonce = sanitize_text_field( wp_unslash( $_POST['ced_tiktok_product_nonce'] ) );
			if ( wp_verify_nonce( $ced_tiktok_product_nonce, 'ced_tiktok_product_nonce' ) ) {
				$connector_ean       = array_key_exists( 'ced_connector_ean', $_POST ) ? map_deep( wp_unslash( $_POST['ced_connector_ean'] ), 'sanitize_text_field' ) : '';
				$ced_connector_brand = array_key_exists( 'ced_connector_brand', $_POST ) ? map_deep( wp_unslash( $_POST['ced_connector_brand'] ), 'sanitize_text_field' ) : '';
				if ( ! empty( $connector_ean ) ) {
					foreach ( $connector_ean as $key => $value ) {
						$parent_id = wp_get_post_parent_id( $key );
						update_post_meta( $key, 'ced_connector_ean', $value );
					}
				}
				if ( ! empty( $ced_connector_brand ) ) {
					foreach ( $ced_connector_brand as $key => $value ) {
						$parent_id = wp_get_post_parent_id( $key );
						update_post_meta( $key, 'ced_connector_brand', $value );
					}
				}
			}
		}
	}

	/**
	 * Function to add order metabox.
	 *
	 * @return void
	 */
	public function ced_tiktok_add_order_metabox() {
		global $post;
		$post_id     = $post->ID;
		$marketplace = get_post_meta( $post_id, '_ced_marketplace', true );
		if ( ( 'tiktok' === $marketplace ) ) {
			add_meta_box(
				'ced_tiktok_manage_orders_metabox',
				__( 'Manage Tiktok Orders', 'cedcommerce-connector-for-tiktok-shop' ) . wc_help_tip( __( 'Please save tracking information of order.', 'cedcommerce-connector-for-tiktok-shop' ) ),
				array( $this, 'ced_tiktok_render_order_metabox' ),
				'shop_order',
				'advanced',
				'high'
			);
		}
	}

	/**
	 *  Function ced_tiktok_render_order_metabox.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function ced_tiktok_render_order_metabox() {
		global $post;
		$order_id         = isset( $post->ID ) ? intval( $post->ID ) : '';
		$tracking_company = get_post_meta( $order_id, '_trackingCompany', true );
		$tracking_number  = get_post_meta( $order_id, '_trackingNumber', true );
		$tracking_errors  = get_post_meta( $order_id, 'tracking_errors', true );
		$marketplace      = get_post_meta( $order_id, '_ced_marketplace', true );
		if ( ( 'tiktok' === $marketplace ) ) {
			$tracking_company = ! empty( $tracking_company ) ? $tracking_company : '';
			$tracking_number  = ! empty( $tracking_number ) ? $tracking_number : '';
			$provider         = get_option( 'CCTS' . $marketplace . '_shipping_carriers' );
			?>
		<table class="ced_bol_submit_shipment">
			<?php
			if ( ! empty( $tracking_errors ) ) {
				echo '<tr><td>There are following errors while saving the tracking details :<ul style="list-style:square">';
				foreach ( $tracking_errors as $k => $v ) {
					echo '<li><span style="color:red;">' . esc_html( $v ) . '</span></li>';
				}
				echo '</ul></td></tr>';
			}
			?>
			<?php wp_nonce_field( 'ced_tiktok_order_nonce', 'ced_tiktok_order_nonce' ); ?>
			<tr>
				<td style="color:#444 !important"> <?php esc_html_e( 'Shipping Provider', 'cedcommerce-connector-for-tiktok-shop' ); ?></td>
				<td style="color:#444 !important">
				<select name="trackingCompany" required ="required" >
					<option value=""><?php esc_html_e( 'Select', 'cedcommerce-connector-for-tiktok-shop' ); ?></option>
					<?php

					foreach ( $provider as $key => $value ) {

						if ( (string) $key === (string) $tracking_company ) {
							$ship_class = 'selected';
						} else {
							$ship_class = '';
						}
						echo "<option value='" . esc_attr( $key ) . "' " . esc_attr( $ship_class ) . '>' . esc_attr( $value ) . '</option>';
					}

					?>
				</select>
				</td>
			</tr>
			<tr>
				<td style="color:#444 !important"><?php esc_html_e( 'Tracking Number', 'cedcommerce-connector-for-tiktok-shop' ); ?></td>
				<td style="color:#444 !important"><input type='text' name='trackingNumber' id='trackingNumber' required ="required" value='<?php echo esc_html( $tracking_number ); ?>'></td>
			</tr>
		</table>
			<?php
		}
	}
	/**
	 * Will save order meta data.
	 *
	 * @param string $post_id current order ID.
	 * @param object $post current post object.
	 * @since 1.0.0
	 * @return void
	 */
	public function ced_tiktok_save_metadata( $post_id = '', $post = '' ) {
		if ( ! $post_id || ! isset( $post->post_type ) || 'shop_order' !== $post->post_type ) {
			return;
		}
		if ( ! empty( $_POST['ced_tiktok_order_nonce'] ) ) {
			$ced_tiktok_order_nonce = sanitize_text_field( wp_unslash( $_POST['ced_tiktok_order_nonce'] ) );
			if ( wp_verify_nonce( $ced_tiktok_order_nonce, 'ced_tiktok_order_nonce' ) ) {
				if ( $post_id ) {
					if ( ! empty( $_POST['trackingCompany'] ) && ! empty( $_POST['trackingNumber'] ) ) {
						if ( ! empty( $_POST['trackingCompany'] ) ) {
							update_post_meta( $post_id, '_trackingCompany', sanitize_text_field( wp_unslash( $_POST['trackingCompany'] ) ) );
						}
						if ( ! empty( $_POST['trackingNumber'] ) ) {
							update_post_meta( $post_id, '_trackingNumber', sanitize_text_field( wp_unslash( $_POST['trackingNumber'] ) ) );
						}
						delete_post_meta( $post_id, 'tracking_errors' );
					} else {
						$errors = array();
						if ( isset( $_POST['_ced_marketplace'] ) && 'tiktok' === $_POST['_ced_marketplace'] ) {
							if ( empty( $_POST['trackingCompany'] ) ) {
								$errors[] = 'Carrier Name Can not be empty.';
							}
							if ( empty( $_POST['trackingNumber'] ) ) {
								$errors[] = 'Tracking Number Can not be empty.';
							}
							update_post_meta( $post_id, 'tracking_errors', $errors );
						}
					}
				}
			}
		}
	}

	/**
	 * Function to add column in order section woocommerce
	 *
	 * @param array $columns woocommerce order section columns.
	 * @return array
	 */
	public function ced_tiktok_add_column_order_section( $columns ) {
		$reordered_columns = array();

		// Inserting columns to a specific location.
		foreach ( $columns as $key => $column ) {
			$reordered_columns[ $key ] = $column;
			if ( 'order_status' === $key ) {
				$reordered_columns['order_type']  = __( 'Order Type', 'cedcommerce-connector-for-tiktok-shop' );
				$reordered_columns['marketplace'] = __( 'Marketplace', 'cedcommerce-connector-for-tiktok-shop' );
			}
		}
		return $reordered_columns;
	}

	/**
	 * Function to show values in order scetion custom columns
	 *
	 * @param string $column column name.
	 * @param string $post_id post id.
	 * @return void
	 */
	public function ced_tiktok_column_order_section_callback( $column, $post_id ) {
		switch ( $column ) {
			case 'marketplace':
				// Get custom post meta data.
				$ced_marketplace = get_post_meta( $post_id, '_ced_marketplace', true );
				if ( ! empty( $ced_marketplace ) ) {
					echo esc_html( strtoupper( $ced_marketplace ) );
				} else {
					echo '-';
				}
				break;
		}
	}

	public function ced_tiktok_connect_account() {

		$check_ajax = check_ajax_referer( 'ced-tiktok-ajax-seurity-string', 'ajax_nonce' );

		if ( $check_ajax ) {
			$domain_url   = home_url();
			$redirect_url = admin_url( 'admin.php?page=ced_tiktok_integration' );
			$callback_url = CCTS_URL.'/includes/ced/ced_tiktok_woo_callback.php';
			$auth_url     = $domain_url . '/wc-auth/v1/authorize?app_name=CedCommerce Connector for TikTok Shop&scope=read_write&user_id=tiktok_woo_connection&return_url=' . $redirect_url . '&callback_url=' . $callback_url;
			echo wp_json_encode(
				array(
					'status'   => 200,
					'auth_url' => $auth_url,
				)
			);
			wp_die();

		}
	}
	public function ced_tiktok_manual_connect_account() {

		global $wp_filesystem;

		// Check if WP_Filesystem is available
		if ( ! function_exists( 'WP_Filesystem' ) ) {
			require_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Initialize the WP_Filesystem
		if ( ! $wp_filesystem ) {
			WP_Filesystem();
		}

		$check_ajax = check_ajax_referer( 'ced-tiktok-ajax-seurity-string', 'ajax_nonce' );

		if ( $check_ajax ) {
			$consumer_key    = isset( $_POST['consumer_key'] ) ? sanitize_text_field( $_POST['consumer_key'] ) : '';
			$consumer_secret = isset( $_POST['consumer_secret'] ) ? sanitize_text_field( $_POST['consumer_secret'] ) : '';

			if ( ! empty( $consumer_key ) && ! empty( $consumer_secret ) ) {

				$data['consumer_key']    = $consumer_key;
				$data['consumer_secret'] = $consumer_secret;
				$data['user_id']         = 'tiktok_woo_connection';

				$site_url                 = get_site_url() . '/wp-json/wc/v3/products';
				$headers                  = array();
				$headers['Authorization'] = 'Basic ' . base64_encode( $consumer_key . ':' . $consumer_secret );

				$response      = wp_remote_get(
					$site_url,
					array(
						'method'      => 'GET',
						'timeout'     => 45,
						'redirection' => 10,
						'httpversion' => '1.0',
						'sslverify'   => false,
						'headers'     => $headers,
					)
				);
				$response_code = wp_remote_retrieve_response_code( $response );
				$response_data = json_decode( wp_remote_retrieve_body( $response ), true );
				if ( ! isset( $response_data['message'] ) && ! empty( $response_data ) && is_array( $response_data ) ) {

					$api_keys = wp_json_encode( $data );
					$file     = CCTS_DIRPATH . 'includes/ced/ced_tiktok_api_details.txt';
					$wp_filesystem->put_contents( $file, $api_keys );
					$auth_url = admin_url( 'admin.php?page=ced_tiktok_integration&user_id=tiktok_woo_connection' );
					echo wp_json_encode(
						array(
							'status'   => 200,
							'auth_url' => $auth_url,
						)
					);
					wp_die();
				} else {
					$message = ! empty( $response_data['message'] ) ? $response_data['message'] : 'Auth credentials are not valid';
					echo wp_json_encode(
						array(
							'status'  => 204,
							'message' => $message,
						)
					);
					wp_die();
				}
			} else {
				echo wp_json_encode(
					array(
						'status'  => 204,
						'message' => "Required fields can't be blank ",
					)
				);
				wp_die();
			}
		}
	}
}
