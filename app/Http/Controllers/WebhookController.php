<?php

namespace App\Http\Controllers;

use EcomPHP\TiktokShop\Client;
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

    public function connection()
    {
        $webhook = $this->client->webhook();
        dd($webhook);
    }
}
