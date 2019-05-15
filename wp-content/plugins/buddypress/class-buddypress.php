<?php
/**
* @package CBProducts
* Template Name: class-buddypress
*/
/*
Plugin Name: CBProducts
Description: Import Products from ChinaBrands
Version: 5.0
*/

defined( 'ABSPATH' ) || exit;

$data = -1;
$results = array();
$id = -1;
$export_id = -1;

function product_unavailable(){
	echo "<h2>The product is currently unavailable</h2>";
}

function to_woocommerce(){
	global $woocommerce;
	global $data;
	
	$wp_rest_request = new WP_REST_Request( 'POST' );

    $wp_rest_request->set_body_params( $data );

    $products_controller = new WC_REST_Products_Controller;

    $wp_rest_response = $products_controller->create_item( $wp_rest_request );
}

function cancel_Order(){
	global $id;

	$order = new WC_Order($id);

	$order->update_status('cancelled');

	$order->save();

}

function export_to_chinabrands(){
global $export_id;

$instance = new WC_Order($export_id);

$items = $instance->get_items();

foreach($items as $item){

$product = wc_get_product($item->get_product_id());

$info = explode( '-', $product->get_sku());

$sku = array_shift($info);

$warehouse_shipping = implode( '-', $info );

$order[$warehouse_shipping] = array(
		'user_order_sn' => $instance->get_id() . $warehouse_shipping,
        'country' => $instance->get_shipping_country(),
        'warehouse' => $info[0],
        'firstname' => $instance->get_billing_first_name(),
        'lastname' => $instance->get_billing_last_name(),
        'addressline1' => $instance->get_shipping_address_1(),
        'addressline2' => '',
        'shipping_method' => $info[1],
        'tel' => '4207752',
        'state' => $instance->get_shipping_state(),
        'city' => $instance->get_shipping_city(),
        'zip' => $instance->get_shipping_postcode(),
        'order_remark' => 'Imported from Woocommerce',
        'insure_fee' => '0',
        'use_point' => '0',
);

$order[$warehouse_shipping]['goods_info'][] = array(
		'goods_sn'=> $sku,
		'goods_number' => $item['quantity']
);
}


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

echo "<script>console.log(".json_encode($decoded).")</script>";

//$instance->update_status('completed');

}

function list_Order()
{

	global $results;
	global $wpdb;

	$orders_statuses = "'wc-pending', 'wc-processing', 'wc-on-hold'";

	$querys = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}posts WHERE post_type LIKE 'shop_order' AND post_status IN ($orders_statuses) ORDER BY ID DESC LIMIT 10");

// Loop through each order post object
	foreach( $querys as $query ){
	    // Get an instance of the WC_Order Object
	    $order    = wc_get_order( $query->ID );
	    $results[] = $order;
	}

}

add_action( 'admin_menu', 'CB_admin_menu' );
add_action( 'admin_init' , 'list_Order');


function CB_admin_menu() {
	add_menu_page( 'CB Product Import', 'CB Products', 'manage_options', 'CBplugin/CBproducts-admin-page.php', 'CB_admin_page', 'dashicons-cart', 6  );
}

if($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['someAction']))
{
	
	$sku = get_SKU($_POST["url"]);
	$upscale = $_POST["myRange"];
	$data = get_product_data($sku, $upscale);

	if($data == -1){
		product_unavailable();
	} else {
		add_action( 'woocommerce_init', 'to_woocommerce');
	}
}


if (isset($_POST['btn-delete'])) {
	global $id;
	$id = $_POST['btn-delete'];
	add_action( 'woocommerce_after_register_post_type' , 'cancel_Order');
}

if (isset($_POST['btn-export'])) {
	global $export_id;
	$export_id = $_POST['btn-export'];
	echo "<script>console.log(".json_encode($export_id).")</script>";
	add_action( 'woocommerce_after_register_post_type' , 'export_to_chinabrands');
}

function CB_admin_page() {
	global $results;

    $file = plugin_dir_path( __FILE__ ) . "CBProduct.html";

    if ( file_exists( $file ) )
        require $file;
}


function get_SKU($url){
	require 'simple_html_dom.php';

	$html = file_get_html($url);
	$sku = $html->find('span.sku', 0);

	$sku = strip_tags($sku);

	$sku = explode( ':' , $sku)[1];

	return trim($sku);
}

function get_token(){
	global $wpdb;
	$token = $wpdb->get_results("SELECT value FROM wp_token WHERE token = 'token'");
	$token = $token[0]->value;

	return $token;
}

function get_product_data($sku, $upcharge){
	$goods_sn = $sku; 
			//$goods_sn = array('DDFF0001','DDFF0002'); //数组
			//$goods_sn = 'DDFF0001,DDFF0002'; //以英文逗号分隔
	$post_data = array(
			'token' => get_token(),
			'goods_sn' => json_encode($goods_sn)
			);
			$api_url = 'https://gloapi.chinabrands.com/v2/product/index';
			$curl = curl_init($api_url);
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($curl, CURLOPT_POST, 1);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
			$result = curl_exec($curl);
			curl_close($curl);

			$decoded = json_decode($result, true);


	if($decoded['status'] == 0){
		return -1;
	}

	$access = -1;

	for($i = 0; $i < sizeof($decoded['msg']); $i++){
		if($decoded['msg'][$i]['status'] == 1 and $decoded['msg'][$access]['sku'] == $goods_sn){
			$access = $i;
			break;
		}
	}

	if($access == -1){
		return -1;
	}

	$SKU = $decoded['msg'][$access]['sku'];
	$name = $decoded['msg'][$access]['title'];
	$description = $decoded['msg'][$access]['goods_desc'];
	$parent_id = $decoded['msg'][$access]['parent_id'];
	$image = $decoded['msg'][$access]['original_img'][0];
	$image2 = $decoded['msg'][$access]['original_img'][1];

	if($image2 == NULL){
		$image2 = $image;
	}

	if(array_key_exists('YB', $decoded['msg'][$access]['warehouse_list'])){
		$warehouse = 'YB';
	} else {
		reset($decoded['msg'][$access]['warehouse_list']);
		$warehouse = key($decoded['msg'][$access]['warehouse_list']);
	}


	$price = ($decoded['msg'][$access]['warehouse_list'][$warehouse]['price'] + $decoded['msg'][$access]['warehouse_list'][$warehouse]['handling_fee']);
	$price = convertCurrency($price) * $upcharge;
	$stock = $decoded['msg'][$access]['warehouse_list'][$warehouse]['goods_number'];
	//$shipping = get_shipping($SKU, $warehouse);

$temp = [
	'sku' => $SKU . '-' . $warehouse . '-',
    'name' => $name,
    'type' => 'simple',
    'backorders' => 'no',
    'manage_stock' => 'true',
    'stock_quantity' => $stock,
    'tax_status' => 'taxable',
    'parent_id' => $parent_id,
    'regular_price' => $price,
    'description' => $description,
    'images' => [
        [
            'src' => $image
        ],
        [
            'src' => $image2
        ]
    ]
];

return $temp;
}

function convertCurrency($amount){
	global $wpdb;

	$currency = $wpdb->get_results("SELECT * FROM wp_rates");
	$USD = $currency[0]->value;

	return $amount * $USD;
}


