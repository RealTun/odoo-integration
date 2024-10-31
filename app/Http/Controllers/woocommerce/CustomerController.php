<?php

namespace App\Http\Controllers\woocommerce;

use App\Http\Controllers\Controller;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        try {
            return $this->woo->get('customers/' . $id);
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
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

    public function createCustomerViaHook($rawData)
    {
        try {
            $data = $this->jsonBody($rawData);
            $customer = $this->woo->post('customers', $data);
            Log::info("Customer created via received hook odoo", [
                'customer' => $customer
            ]);
            // return response()->json($customer, 201);
        } catch (\Exception $e) {
            Log::error("Customer create error", [
                'error' => $e->getMessage()
            ]);
            return response()->json([
                'error' => $e->getMessage()
            ], 400);
        }
    }

    private function jsonBody($rawData)
    {
        $data = [
            "email" => $rawData['email'] ?? "",
            "first_name" => explode(" ", $rawData['name'])[0] ?? "",
            "last_name" => explode(" ", $rawData['name'])[1] ?? "",
            "username" => strtolower(str_replace(' ', '.', $rawData['name'] ?? "")),
            "billing" => [
                "first_name" => explode(" ", $rawData['name'])[0] ?? "",
                "last_name" => explode(" ", $rawData['name'])[1] ?? "",
                "company" => $rawData['function'] ?? "group1",
                "address_1" => $rawData['street'] ?? "VN",
                "address_2" => $rawData['street'] ?? "VN", 
                "city" => $rawData['city'] ?? "Ha Noi",
                "state" => $rawData['state_id'] == 1073 ? "Ha Noi" : "Hung Yen",
                "postcode" => $rawData['zip'] ?? "16000",
                "country" => $rawData['country_id'] == 241 ? "Viet Nam" : "Khong ro",
                "email" => $rawData['email'] ?? "example@mail.com",
                "phone" => $rawData['phone'] ?? "0000000"
            ],
            "shipping" => [
                "first_name" => explode(" ", $rawData['name'])[0] ?? "",
                "last_name" => explode(" ", $rawData['name'])[1] ?? "",
                "company" => $rawData['function'] ?? "group1",
                "address_1" => $rawData['street'] ?? "VN",
                "address_2" => $rawData['street'] ?? "VN",
                "city" => $rawData['city'] ?? "Ha Noi",
                "state" => $rawData['state_id'] == 1073 ? "Ha Noi" : "Hung Yen",
                "postcode" => $rawData['zip'] ?? "16000",
                "country" => $rawData['country_id'] == 241 ? "Viet Nam" : "Khong ro"
            ]
        ];

        return $data;
    }
}
