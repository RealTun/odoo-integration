<?php

namespace App\Http\Controllers\tiktok;

use App\Http\Controllers\Controller;
use EcomPHP\TiktokShop\Client;
use EcomPHP\TiktokShop\Errors\TiktokShopException;
use EcomPHP\TiktokShop\Webhook;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

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
        // $webhook = new Webhook($this->client);
        $webhook = $this->client->webhook();
        Log::info('Webhook tiktok received:', [
            // 'headers' => $request->headers->all(),
            'eventType' => $webhook->getType(),
            'timestamp' => $webhook->getTimestamp(),
            'shopId' => $webhook->getShopId(),
            'data' => $webhook->getData() // Data array
        ]);

        switch($webhook->getType())
        {
            case 1:
                $this->OrderCancellationStatusChane();
                break;
            case 5: 
                $this->ProductStatusChange();
                break;
            case 11: 
                $this->OrderStatusChange();
                break;
            case 15: 
                $this->ProductInformationChange();
                break;
            case 16: 
                $this->ProductCreation();
                break;
            case 18: 
                $this->ProductCategoryChange();
                break;
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

    public function OrderStatusChange()
    {
        // type 11
    }

    public function OrderCancellationStatusChane()
    {
        // type 1
    }

    public function ProductCategoryChange()
    {
        // type 18
    }

    public function ProductCreation()
    {
        // type 16
    }

    public function ProductInformationChange()
    {
        // type 15
    }

    public function ProductStatusChange()
    {
        // type 5
    }
}