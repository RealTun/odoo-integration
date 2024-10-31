<?php

namespace App\Http\Controllers\woocommerce;

use App\Http\Controllers\Controller;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    //
    private $woo;
    public function __construct() {
        $this->woo = new Client(env('WOO_URL'), env('CONSUMER_KEY'), env('CONSUMER_PRIVATE'));;
    }

    public function getProducts()
    {
        return $this->woo->get('products');
    }

    public function getProduct($id)
    {
        try {
            return $this->woo->get('products/' . $id);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function createProduct(Request $request)
    {
        try {
            // Create the Product in WooCommerce
            $response = $this->woo->post('products', $request->all());
            return response()->json($response, 201);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function updateProduct($id, Request $request)
    {
        try {
            // Create the Product in WooCommerce
            $response = $this->woo->put('products/' . $id, $request->all());
            return response()->json($response, 200);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteProduct($id)
    {
        try {
            $response = $this->woo->delete('products/' . $id, ['force' => true]);
            return response()->json([], 204);
        } catch (\Exception $e) {
            // Handle exceptions and return error response
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }
}
