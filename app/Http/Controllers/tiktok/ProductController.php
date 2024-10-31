<?php

namespace App\Http\Controllers\tiktok;

use App\Http\Controllers\Controller;
use EcomPHP\TiktokShop\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProductController extends Controller
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

    public function getProducts()
    {
        $products = $this->client->Product->checkListingPrerequisites();

        return $products;
    }

    public function getProduct($product_id)
    {
        $product = $this->client->Product->getProduct($product_id);

        return $product;
    }

    public function createProduct($rawData)
    {
        try {
            $data = $this->formatData($rawData);

            $this->client->useVersion('202309');
            $product = $this->client->Product->createProduct($data);

            Log::info("Product created after received hook odoo", [
                'product' => $product
            ]);
            return $product;
        } catch (Exception $e) {
            Log::error("Product created after received hook odoo", [
                'error' => $e->getMessage()
            ]);
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    private function formatData($rawData)
    {
        $data = [
            "description" => $rawData['description_ecommerce'] ?? "khong biet nua",
            "category_id" => "601226",
            "main_images" => [
                [
                    "uri" => $rawData['image_uri'] ?? "tos-maliva-i-o3syd03w52-us/150c6b69e3d84ed39013d67214d9a44a"
                ]
            ],
            "size_chart" => [
                "template" => [
                    "id" => "7423782145280984837"
                ]
            ],
            "skus" => [
                [
                    "inventory" => [
                        [
                            "warehouse_id" => $rawData['warehouse_id'] ?? "7422950182005114629",
                            "quantity" => $rawData['quantity'] ?? 10
                        ]
                    ],
                    "price" => [
                        "amount" => strval($rawData['list_price']) ?? '10000',
                        "currency" => "VND"
                    ],
                    "sales_attributes" => [],
                    "combined_skus" => [],
                    "external_urls" => [],
                    "extra_identifier_codes" => []
                ]
            ],
            "title" => $rawData['name'] ?? "San pham test khong ro ten nhu nao",
            "package_weight" => [
                "value" => strval($rawData['weight']) ?? '0.3',
                "unit" => $rawData['weight_unit'] ?? "KILOGRAM"
            ]
        ];

        return $data;
    }
}
