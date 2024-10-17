<?php

namespace App\Http\Controllers\woocommerce;

use App\Http\Controllers\Controller;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    //
    private $woo;

    public function __construct() {
        $this->woo = new Client(env('WOO_URL'), env('CONSUMER_KEY'), env('CONSUMER_PRIVATE'));;
    }

    public function getCustomers()
    {
        return $this->woo->get('customers');
    }

    public function getCustomer($id)
    {
        return $this->woo->get('customers/' . $id);
    }

    public function createCustomer(Request $request)
    {
        try {
            // Create the customer in WooCommerce
            $response = $this->woo->post('customers', $request->all());
            return response()->json($response, 201);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function updateCustomer($id, Request $request)
    {
        try {
            // Create the customer in WooCommerce
            $response = $this->woo->put('customers/' . $id, $request->all());
            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteCustomer($id)
    {
        try {
            $response = $this->woo->delete('customers/' . $id, ['force' => true]);
            return response()->json([], 204);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
