<?php

namespace App\Http\Controllers;

use EcomPHP\TiktokShop\Client;
use EcomPHP\TiktokShop\Errors\TiktokShopException;
use EcomPHP\TiktokShop\Webhook;
use Exception;
use Illuminate\Http\Request;

class WebhookController extends Controller
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

    public function connection(Request $request)
    {
        $webhook = new Webhook($this->client);
        try {
            $requestBody = '{"shop_id": "1234567890", "event_type": "order_created", "data": {"order_id": "987654321", "amount": 100.0, "currency": "USD"}, "timestamp": 1631674800}';

            // Verify the signature (Authorization header)
            $signature = $this->createSignature($requestBody);
            $webhook->verify($signature, $requestBody);

            // Capture the POST data from the webhook
            $webhook->capture();

            // Extract information from the webhook
            $eventType = $webhook->getType();
            $timestamp = $webhook->getTimestamp();
            $shopId = $webhook->getShopId();
            $data = $webhook->getData(); // Data array

            // Process the webhook data (customize this part)
            // e.g., Store data in the database, trigger actions based on event type

            return response()->json([
                'status' => 'success',
                'type' => $eventType,
                'timestamp' => $timestamp,
                'shop_id' => $shopId,
                'data' => $data,
            ]);
        } catch (TiktokShopException $e) {
            // Handle exceptions
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function createSignature($requestBody)
    {
        $app_key = env('TIKTOK_APP_KEY');
        $app_secret = env('TIKTOK_APP_PRIVATE_KEY');

        // Concatenate app key and request body
        $signatureBaseString = $app_key . $requestBody;

        // Generate the signature using HMAC-SHA256
        $calculatedSignature = hash_hmac('sha256', $signatureBaseString, $app_secret);

        return $calculatedSignature;
    }
}
