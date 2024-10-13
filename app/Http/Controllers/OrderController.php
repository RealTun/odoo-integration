<?php

namespace App\Http\Controllers;

use EcomPHP\TiktokShop\Client;
use Exception;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    private $client;
    public function __construct()
    {
        $app_key = env('TIKTOK_APP_KEY');
        $app_secret = env('TIKTOK_APP_PRIVATE_KEY');
        $this->client = new Client($app_key, $app_secret);

        $shopCipher = cache('shop_cipher');
        $accessToken = cache('access_token');

        if (!$shopCipher || !$accessToken) {
            throw new Exception('Need auth again');
        }

        // Set the access token to the client
        $this->client->setAccessToken($accessToken);
        $this->client->setShopCipher($shopCipher);
    }

    public function getOrders()
    {
        $orders = $this->client->Order->getOrderList([
            'order_status' => 'UNPAID', // ON_HOLD, PARTIALLY_SHIPPING, AWAITING_SHIPMENT, AWAITING_COLLECTION, IN_TRANSIT, DELIVERED, COMPLETED, CANCELLED
            'page_size' => 50,
        ]);

        return $orders;
    }

    public function getOrder($order_id)
    {
        $order = $this->client->Order->getOrderDetail($order_id);
        return $order;
    }
}
