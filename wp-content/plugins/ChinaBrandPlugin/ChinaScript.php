<?php
/**
* @package ChinaBrandsShippingPlugin
*/
/*
Plugin Name: ChinaBrandShippingSync
Description: Sync ChinaBrand with wordpress

*/

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	function your_shipping_method_init() {
		if ( ! class_exists( 'ChinaBrands_Shipping_Method' ) ) {
			class ChinaBrands_Shipping_Method extends WC_Shipping_Method {
				/**
				 * Constructor for your shipping class
				 *
				 * @access public
				 * @return void
				 */

				public function __construct() {
					$this->id                 = 'ChinaBrandsShipping'; // Id for your shipping method. Should be uunique.
					$this->method_title       = _( 'China Brands Shippings' );  // Title shown in admin
					$this->method_description = _( 'Grabs Shipping data from ChinaBrands' ); // Description shown in admin
					$this->enabled            = "yes"; // This can be added as an setting but for this example its forced enabled
					$this->title              = "Shipping"; // This can be added as an setting but for this example its forced.
					$this->init();
				}
				/**
				 * Init your settings
				 *
				 * @access public
				 * @return void
				 */
				function init() {
					// Load the settings API
					$this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
					$this->init_settings(); // This is part of the settings API. Loads settings you previously init.
					// Save settings in admin if you have any defined
					add_action( 'woocommerce_update_options_shipping_' . $this->id, array( $this, 'process_admin_options' ) );
				}
				
				private function get_token(){
					global $wpdb;
					$token = $wpdb->get_results("SELECT value FROM wp_token WHERE token = 'token'");
					$token = $token[0]->value;

					return $token;
				}

				private function get_shipping_costs($sku, $quantity, $city, $country, $warehouse, $zip_code){
					$order = array(
						    '0' => array(
							        'user_order_sn' => '1',
							        'country' => $country,
							        'warehouse' => $warehouse,
							        'firstname' => 'TEST',
							        'lastname' => 'WU',
							        'addressline1' => '11 Bradgate Rd',
							        'addressline2' => '',
							        'shipping_method' => 'CNEMS',
							        'tel' => '',
							        'city' => $city,
							        'zip' => $zip_code,
							        'order_remark' => 'Imported from Woocommerce',
							        'insure_fee' => '0',
							        'use_point' => '0',
							        'goods_info' => array(
							            0 => array(
							                'goods_sn' => $sku,
							                'goods_number' => $quantity
							            )
							        ),
							    ),
							);

						$api_url = 'https://gloapi.chinabrands.com/v2/order/create';

						$client_secret = '54aad513299f270bb5a97d5e4d193d6e';
						$post_data = array(
						    'token' => get_token(),
						    'signature' => md5($client_secret.json_encode($order)),
						    'order' => json_encode($order)
						);
						$curl = curl_init($api_url);
						curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
						curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
						curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($curl, CURLOPT_POST, 1);
						curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
						$result = curl_exec($curl); //返回结果
						curl_close($curl);

						$decoded = json_decode($result);

						return $decoded->msg->{'1'}->enable_shipping[0];
				}

				private function convertCurrency($amount,$currency){
					$USD = $currency[0]->value;
					return $amount * $USD;
				}
				/**
				 * calculate_shipping function.
				 *
				 * @access public
				 * @param mixed $package
				 * @return void
				 */
				public function calculate_shipping( $package = array()  ) {
					$shipping_country = WC()->customer->get_shipping_country();
					$shipping_city = WC()->customer->get_shipping_city();
					$postal_code = WC()->customer->get_shipping_postcode();
					$cost = 0;

					global $wpdb;
					$currency = $wpdb->get_results("SELECT * FROM wp_rates");
					
					foreach(WC()->cart->get_cart() as $cart_item){
						$product = wc_get_product( $cart_item['data']->get_id() );

						$sku_warehouse = explode( '-', $product->get_sku());

						$sku = $sku_warehouse[0];

						$warehouse = $sku_warehouse[1];

						$quantity = $cart_item['quantity'];

						$instance = $this->get_shipping_costs($sku, $quantity, $shipping_city, $shipping_country,$warehouse,$postal_code);

						$sku_warehouse[2] = $instance->shipping_code;

						$sku_warehouse = implode( '-' , $sku_warehouse);

						if($product->get_sku() != $sku_warehouse){
						$product->set_sku($sku_warehouse);

						$product->save();
						}

						$cost += $instance->shipping_fee;
						echo "<script>console.log(".json_encode($instance).")</script>";
					}

					$cost = $this->convertCurrency($cost,$currency);
					$cost = strval($cost);



					$rate = array(
						'id' => $this->id,
						'label' => $this->title,
						'cost' => $cost,
						'calc_tax' => 'per_item'
					);

					// Register the rate
					$this->add_rate( $rate );
				}
			}
		}
	}
	add_action( 'woocommerce_shipping_init', 'your_shipping_method_init' );

	function add_your_shipping_method( $methods ) {
		$methods['your_shipping_method'] = 'ChinaBrands_Shipping_Method';
		return $methods;
	}
	add_filter( 'woocommerce_shipping_methods', 'add_your_shipping_method' );
}