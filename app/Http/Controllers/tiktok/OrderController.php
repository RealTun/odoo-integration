<?php

namespace App\Http\Controllers\tiktok;

use App\Http\Controllers\Controller;
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
            // 'order_status' => 'UNPAID', // ON_HOLD, PARTIALLY_SHIPPING, AWAITING_SHIPMENT, AWAITING_COLLECTION, IN_TRANSIT, DELIVERED, COMPLETED, CANCELLED
            'page_size' => 50,
        ]);

        $data = [];
        foreach ($orders['orders'] as $order) {
            array_push($data, [
                'id' => $order['id'],
                "delivery_option_id" => $order['delivery_option_id'],
                "delivery_option_name" => $order['delivery_option_name'],
                "delivery_type" => $order['delivery_type'],
                "line_items" => $order['line_items'],
                "payment" => $order['payment'],
                "payment_method_name" => $order['payment_method_name'],
                // "recipient_address" => $order['recipient_address'],
                "customer" => [
                    'name' => $order['recipient_address']['name'],
                    'phone_number' => $order['recipient_address']['phone_number'],
                    // 'postal_code' => $order['recipient_address']['postal_code'],
                    'district' => $order['recipient_address']['district_info'][2]['address_name'],
                    'city' => $order['recipient_address']['district_info'][1]['address_name'],
                    'country' => $order['recipient_address']['district_info'][0]['address_name'],
                    'full_address' => $order['recipient_address']['full_address'],
                ],
                "warehouse_id" => $order['warehouse_id'],
                "user_id" => $order['user_id'],
                "status" => $order['status'],
            ]);
        }

        return $data;
    }

    public function getOrder($order_id)
    {
        $order = $this->client->Order->getOrderDetail($order_id);
        $data = $order['orders'][0];
        return [
            'id' => $data['id'],
            "delivery_option_id" => $data['delivery_option_id'],
            "delivery_option_name" => $data['delivery_option_name'],
            "delivery_type" => $data['delivery_type'],
            "line_items" => $data['line_items'],
            "payment" => $data['payment'],
            "payment_method_name" => $data['payment_method_name'],
            // "recipient_address" => $data['recipient_address'],
            "customer" => [
                'name' => $data['recipient_address']['name'],
                'address' => $data['recipient_address']['full_address'],
                'phone_number' => $data['recipient_address']['phone_number'],
                'postal_code' => $data['recipient_address']['postal_code'],
                'country' => $data['recipient_address']['region_code']
            ],
            "warehouse_id" => $data['warehouse_id'],
            "user_id" => $data['user_id'],
            "status" => $data['status']
        ];
    }
}
