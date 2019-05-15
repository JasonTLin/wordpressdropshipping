<?php

require __DIR__ . '/vendor/autoload.php';

use Automattic\WooCommerce\Client;

use Automattic\WooCommerce\HttpClient\HttpClientException;


$woocommerce = new Client('http://localhost',

                         'ck_720ff7cdad2b834255adec59147fd477ea9225af',

                         'cs_e75a77393900259e96cc64417fbfad89208a891a',

                         [

                         'wp_api' => true, 'version' => 'wc/v1',

]);
echo "<h2>Hi</h2>";

$results = $woocommerce->get('orders');

echo "<h2>The product is currently unavailable</h2>";

header('Location: ../../../wp-admin');

