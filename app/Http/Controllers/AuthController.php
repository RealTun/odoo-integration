<?php

namespace App\Http\Controllers;

use EcomPHP\TiktokShop\Client;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    private $client;

    public function __construct()
    {
        $app_key = env('TIKTOK_APP_KEY');
        $app_secret = env('TIKTOK_APP_PRIVATE_KEY');
        $this->client = new Client($app_key, $app_secret);
    }

    public function createAuth()
    {
        // Create the authentication request
        $auth = $this->client->auth();
        $_SESSION['state'] = $state = bin2hex(random_bytes(20));
        $auth->createAuthRequest($state);

        // Get authentication code when redirected back to Redirect callback URL after app authorization and exchange it for access token
        $authorization_code = $_GET['code'];
        $token = $auth->getToken($authorization_code);

        $access_token = $token['access_token'];
        $refresh_token = $token['refresh_token'];

        // Get authorized Shop cipher
        $access_token = $token['access_token'];
        $this->client->setAccessToken($access_token);

        $authorizedShopList = $this->client->Authorization->getAuthorizedShop();
    }
}
