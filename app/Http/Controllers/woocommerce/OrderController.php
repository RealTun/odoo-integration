<?php

namespace App\Http\Controllers\woocommerce;

use App\Http\Controllers\Controller;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    private $woo;
    public function __construct() {
        $this->woo = new Client(env('WOO_URL'), env('CONSUMER_KEY'), env('CONSUMER_PRIVATE'));;
    }

    public function getOrders()
    {
        return $this->woo->get('orders');
    }

    public function getOrder($id)
    {
        return $this->woo->get('orders/' . $id);
    }

    public function createOrder(Request $request)
    {
        try {
            // Create the Order in WooCommerce
            $response = $this->woo->post('orders', $request->all());
            return response()->json($response, 201);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function updateOrder($id, Request $request)
    {
        try {
            // Create the Order in WooCommerce
            $response = $this->woo->put('orders/' . $id, $request->all());
            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteOrder($id)
    {
        try {
            $response = $this->woo->delete('orders/' . $id, ['force' => true]);
            return response()->json([], 204);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
