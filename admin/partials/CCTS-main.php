<?php
/**
 * Ced_Connector Main
 *
 * @package  Ced_Connector_Integration_For_Woocommerce
 * @version  1.0.0
 * @link     https://cedcommerce.com
 * @since    1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	die;
}
//delete_option( 'ced_tiktok_woo_connection_data' );
if ( isset( $_GET['user_id'] ) && 'tiktok_woo_connection' === $_GET['user_id'] && ( isset( $_GET['success'] ) && '0' === $_GET['success'] ) ) {
	$url = admin_url( 'admin.php?page=cedcommerce-integrations' );
	header( 'Location: ' . $url );
	die();
}
if ( isset( $_GET['page'] ) && 'ced_tiktok_integration' === $_GET['page'] && isset( $_GET['shop'] ) && 'woocommerce' === $_GET['shop'] ) {
	$response = array(
		'token' => ! empty( $_GET['user_token'] ) ? sanitize_text_field( wp_unslash( $_GET['user_token'] ) ) : false,
		'shop'  => ! empty( $_GET['shop'] ) ? sanitize_text_field( wp_unslash( $_GET['shop'] ) ) : false,
	);
	update_option( 'ced_tiktok_woo_connection_data', wp_json_encode( $response ) );
	$url = admin_url( 'admin.php?page=ced_tiktok_integration' );
	header( 'Location: ' . $url );
	die();
}
if ( isset( $_GET['user_id'] ) && 'tiktok_woo_connection' === $_GET['user_id'] ) {

	global $wp_filesystem;

	// Check if WP_Filesystem is available
	if ( ! function_exists( 'WP_Filesystem' ) ) {
		require_once ABSPATH . 'wp-admin/includes/file.php';
	}

	// Initialize the WP_Filesystem
	if ( ! $wp_filesystem ) {
		WP_Filesystem();
	}

	$file         = CCTS_DIRPATH . 'includes/ced/ced_tiktok_api_details.txt';
	$woo_api_data = $wp_filesystem->get_contents( $file );
	if ( ! empty( $woo_api_data ) ) {
		$admin_data                  = wp_get_current_user();
		$admin_data                  = (array) $admin_data->data;
		$user_id                     = $admin_data['ID'];
		$first_name                  = ! empty( get_user_meta( $user_id, 'first_name', true ) ) ? get_user_meta( $user_id, 'first_name', true ) : get_user_meta( $user_id, 'nickname', true );
		$last_name                   = ! empty( get_user_meta( $user_id, 'last_name', true ) ) ? get_user_meta( $user_id, 'last_name', true ) : '';
		$phone_number                = ! empty( get_user_meta( $user_id, 'billing_phone', true ) ) ? get_user_meta( $user_id, 'billing_phone', true ) : get_user_meta( $user_id, 'shipping_phone', true );
		$store_raw_country           = get_option( 'woocommerce_default_country' );
		$split_country               = explode( ':', $store_raw_country );
		$store_country               = $split_country[0];
		$store_state                 = $split_country[1];
		$user_data                   = array();
		$user_data['email']          = $admin_data['user_email'];
		$user_data['username']       = home_url();
		$user_data['domain']         = home_url();
		$user_data['first_name']     = $first_name;
		$user_data['last_name']      = $last_name;
		$user_data['name']           = get_bloginfo( 'name' ) ?? home_url();
		$user_data['phone']          = $phone_number;
		$user_data['address1']       = get_option( 'woocommerce_store_address' );
		$user_data['address2']       = get_option( 'woocommerce_store_address_2' );
		$user_data['city']           = get_option( 'woocommerce_store_city' );
		$user_data['zip']            = get_option( 'woocommerce_store_postcode' );
		$user_data['province']       = $store_state;
		$user_data['country']        = $store_country;
		$user_data['currency']       = get_woocommerce_currency();
		$user_data['weight_unit']    = get_option( 'woocommerce_weight_unit' );
		$user_data['dimension_unit'] = get_option( 'woocommerce_dimension_unit' );
		$decoded_data                = json_decode( $woo_api_data, true );
		$params                      = array();
		$params['domain']            = home_url();
		$params['consumer_key']      = $decoded_data['consumer_key'];
		$params['consumer_secret']   = $decoded_data['consumer_secret'];
		$params['user_detail']       = $user_data;
		$params['time']              = time();
		$state                       = array(
			'frontend_redirect_uri' => admin_url( 'admin.php?page=ced_tiktok_integration' ),
		);
		$params['state']             = wp_json_encode( $state );
		$url                         = 'https://tiktok-api-backend.cifapps.com/apiconnect/request/auth?sAppId=15&' . http_build_query( $params );

		if ( wp_redirect( $url ) ) {
			exit;
		}
	}
}

$wooconnection_data = get_option( 'ced_tiktok_woo_connection_data', '' );
// $wooconnection_data = array();
if ( ! empty( $wooconnection_data ) ) {
		$admin_data         = wp_get_current_user();
		$admin_data         = (array) $admin_data->data;
		$wooconnection_data = json_decode( $wooconnection_data, true );
		$param              = array();
		$param['username']  = home_url();
		$param['target']    = 'tiktok';
		$headers            = array();
		$headers[]          = 'Content-Type: application/json';
		$endpoint           = 'https://tiktok-app-backend.cifapps.com/woocommercehome/request/getUserToken';
		$response           = wp_remote_post(
			$endpoint,
			array(
				'method'      => 'POST',
				'timeout'     => 45,
				'redirection' => 10,
				'httpversion' => '1.0',
				'sslverify'   => false,
				'headers'     => $headers,
				'body'        => $param,
			)
		);

		$response_code = wp_remote_retrieve_response_code( $response );
		$response_data = json_decode( wp_remote_retrieve_body( $response ), true );
	if ( 200 === $response_code ) {
		$data               = array(
			'token' => ! empty( $response_data['token_res'] ) ? $response_data['token_res'] : false,
		);
		$wooconnection_data = wp_json_encode( $data );
		if ( empty( $response_data['url'] ) ) {
			delete_option( 'ced_tiktok_woo_connection_data' );
			die( 'Oops!! Unexpected Error Occoured' );
		}
		update_option( 'ced_tiktok_woo_connection_data', $wooconnection_data );
	} elseif ( empty( $response_data['url'] ) ) {
			delete_option( 'ced_tiktok_woo_connection_data' );
			die( 'Oops!! Unexpected Error Occoured' );
	}


		$url = $response_data['url'];

	?>
			<iframe src="<?php echo esc_attr( $url ); ?>" style="width:100%;height:100vh;">
			</iframe>
			<?php
} else {

	?>
<div class="center-form">
	<div class="ced-user_container connect-form">
	<div class="pre-card">
		<div class="ced_connector_loader" style="display:none">
	<img src="<?php echo esc_url( CCTS_URL ) . 'admin/images/ajax-loader.gif'; ?>" width="50px" height="50px" class="ced_lazada_loading_img" >
	</div>
		<div class="pre-card__header card--border">
		<h2 class="pre-title">CedCommerce Connector for TikTok Shop</h2>
		</div>
		<div class="pre-card__body">
		<div class="pre-user-table">
		<table>
			<thead>
			<tr>
				<th>Click to connect With CedCommerce Connector for TikTok Shop</th>              
			</tr>
			</thead>
			<tbody>
			<tr>
				<td><button type="button" class="pre-connect-btn ced_tiktok_connect_button">Connect</button></td>
			</tr>
			</tbody>
		</table>
			<table class='ced_connector_tiktok_manual_table'>
			<thead>
			<tr>
				<th>
					<label for="connector_dev">
					<input type="checkbox" id="connector_dev" />
						<span> If facing issues in automated connection process click to go with manual process.</span>
					</label>
				</th>   
			</tr>
			</thead> 
		</table>
			</div>
		</div>
			<div>
			<div class="pre-connectForm" style='display:none'>
			<form action="">
				<!-- use this class when error occurs pre-connectForm--Error -->
				<div class="pre-connectForm__item">
				<label for="email">Consumer Key<span class="pre-required-sign">*</span></label>
				<input type="email" name="email" value="" class="ced_connector_consumer_key" placeholder="Enter here" />
				</div>
				<div class="pre-connectForm__item">
				<label for="id">Consumer Secret<span class="pre-required-sign">*</span></label>
				<input type="text" name="id" value="" class="ced_connector_consumer_secret" placeholder="Enter here" />
				</div>
			</form>
			<!-- to show div when error occurs start -->
			<div class="error-banner">
				<span class="error-icon"><svg width="20" height="20" viewBox="0 0 20 20" fill="none"
					xmlns="http://www.w3.org/2000/svg">
					<path
					d="M8.57514 3.21635L1.51681 14.9997C1.37128 15.2517 1.29428 15.5374 1.29346 15.8284C1.29265 16.1195 1.36805 16.4056 1.51216 16.6585C1.65627 16.9113 1.86408 17.122 2.1149 17.2696C2.36571 17.4171 2.65081 17.4965 2.94181 17.4997H17.0585C17.3495 17.4965 17.6346 17.4171 17.8854 17.2696C18.1362 17.122 18.344 16.9113 18.4881 16.6585C18.6322 16.4056 18.7076 16.1195 18.7068 15.8284C18.706 15.5374 18.629 15.2517 18.4835 14.9997L11.4251 3.21635C11.2766 2.97144 11.0674 2.76895 10.8178 2.62842C10.5682 2.48789 10.2866 2.41406 10.0001 2.41406C9.71369 2.41406 9.43208 2.48789 9.18248 2.62842C8.93287 2.76895 8.7237 2.97144 8.57514 3.21635V3.21635Z"
					stroke="#C4281C" stroke-width="1.66667" stroke-linecap="round" stroke-linejoin="round" />
					<path d="M10 7.5V10.8333" stroke="#C4281C" stroke-width="1.66667" stroke-linecap="round"
					stroke-linejoin="round" />
					<path d="M10 14.167H10.0083" stroke="#C4281C" stroke-width="1.66667" stroke-linecap="round"
					stroke-linejoin="round" />
				</svg>
				</span>
				<span class="error-text">All fields must be filled</span>
			</div>
			<!-- show div when error occurs end-->
			<div class="pre-instructions">
				<h3 class="pre-subtitle">Instructions</h2>
				<ul class="pre-points">
					<li>Go to WooCommerce->Settings->Advanced->REST API & click on add key button</li>
					<li>Fill up the description , select user & provide Read/Write permission & then click on generate API key button</li>
					<li>Copy consumer key & secret and paste above.</li>
				</ul>
			</div>
				<div class="pre-card__footer">
			<hr class="custom-hr" />
			<button type="button" class="pre-connect-btn ced_tiktok_manual_connect_button">Validate</button>
		</div>
			</div>
		</div>
		</div>
	</div>
</div>
	<?php
}
?>