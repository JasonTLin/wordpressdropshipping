<?php

/*function unavailable_product(){
	echo "<h2>The Product is unavailable</h2>";
}*/

function get_SKU($url){
	$link = parse_url($url, PHP_URL_PATH);

	for($i = strlen($link) - 8; $i > strlen($link) - 15; $i--){
		$sku = $sku . $link[$i];
	}
	$sku = strrev($sku);

	$sku = $sku . '0';

	return $sku;
}

/*
function get_shipping($sku, $warehouse){
	$shipping = array('CNEMS', 'SGAM4PX', 'NLAMHQ', 'CAEXP');
	for($i = 0; $i < 5; $i++){
		$goods = array(
		array(
		    'sku' => $sku,
		    'quantity' => '1',
		),
		);
		$post_data = array(
		    'token' => get_token(),
		    'country' => 'CA',
		    'warehouse' => '$warehouse',
		    'goods' => json_encode($goods),
		    'shipping' => $shipping,
		    'zip_code' => 'Y1A0A2',
		    'platform_id' => 1
		);
		$api_url = 'https://gloapi.chinabrands.com/v2/shipping/cost';
		$curl = curl_init($api_url);
		curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POST, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
		$result = curl_exec($curl); //返回结果
		curl_close($curl);

		$decoded = json_decode($result, true);

		if($decoded['status'] == 1){
			return $shipping[$i];
		}
	}
	unavailable_product();
}

function get_product_data($sku){
	for($i = 1; $i < 10; $i++){
			$status = 0;
			$goods_sn = get_SKU() . $i; 
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
			$status += $decoded['status'];

			if($status == 1){
				break;
			}
	}

	if($status == 0){
		unavailable_product();
	}

	$access = -1;

	for($i = 0; $i < sizeof($decoded['msg']); $i++){
		if($decoded['msg'][$i]['status'] = 1){
			$access = $i;
		}
	}

	if($access == -1){
		unavailable_product();
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
	$stock = $decoded['msg'][$access]['warehouse_list'][$warehouse]['goods_number'];
	$shipping = get_shipping($SKU, $warehouse);

}*/

function to_woocommerce(){
	$data = [
    'name' => 'Premium Quality',
    'type' => 'simple',
    'regular_price' => '21.99',
    'description' => 'Test',
	];

	$request = new WP_REST_Request( 'POST' );
	$request->set_body_params( $data );
	$products_controller = new WC_REST_Products_Controller;
	$response = $products_controller->create_item( $request );

}


$url = $_POST["url"];

echo "<script>console.log(".json_encode('hi').")</script>";
