<?php

namespace App\Http\Controllers;

use EcomPHP\TiktokShop\Client;
use GuzzleHttp\RequestOptions;
use Illuminate\Http\Request;
use GuzzleHttp\Client as GuzzleHttpClient;

class AuthController extends Controller
{
    //
    private $client;
    private $auth;
    private $accessToken;
    private $refreshToken;
    private $shopCipher; 
    private $shopId = '7495954110325557258';
    private $authCode;

    public function __construct()
    {
        $app_key = env('TIKTOK_APP_KEY');
        $app_secret = env('TIKTOK_APP_PRIVATE_KEY');
        $this->client = new Client($app_key, $app_secret);
    }

    public function createAuth() 
    {
        // Initialize authentication
        $this->auth = $this->client->auth();
        
        // Generate a random state and store it in session for later validation
        $_SESSION['state'] = $state = bin2hex(random_bytes(20)); // 40-character random string
        
        // Create the authentication request and return the URL
        $authUrl = $this->auth->createAuthRequest($state, true);

        // Redirect the user to the authentication URL
        return redirect($authUrl);
    }

    public function handleCallback(Request $request)
    {
        // Initialize authentication
        if ($request->has(key: 'code')) {
            $authorization_code = $request->get('code');

            // Exchange the authorization code for an access token
            $token = $this->handleToken($authorization_code);

            // Store the access and refresh tokens
            $access_token = $token['access_token'];
            $refresh_token = $token['refresh_token'];

            // Set the access token for future requests
            $this->client->setAccessToken($access_token);

            // Get the list of authorized shops (or other resources)
            $authorizedShopList = $this->client->Authorization->getAuthorizedShop();
            $cipher = $authorizedShopList['shops'][0]['cipher'];

            $this->client->setShopCipher($cipher);

            $this->setToken($access_token, $refresh_token, $cipher, $authorization_code);

            return [
                'shop' => $authorizedShopList,
                'token' => $token
            ];
        } else {
            return response()->json(data: ['error' => 'Authorization code or state is missing'], status: 400);
        }
    }

    private function handleToken($code)
    {
        $httpClient = new GuzzleHttpClient();
        $response = $httpClient->get(uri: 'https://auth.tiktok-shops.com/api/v2/token/get', options: [
            RequestOptions::QUERY => [
                'app_key' => $this->client->getAppKey(),
                'app_secret' => $this->client->getAppSecret(),
                'auth_code' => $code,
                'grant_type' => 'authorized_code',
            ]
        ]);
        $json = json_decode($response->getBody(), true);
        if ($json['code'] !== 0) {
            return [];
        }

        return $json['data'];
    }

    private function setToken($accessToken, $refreshToken, $shopCipher, $authCode)
    {
        cache()->put('access_token', $accessToken, 3600);
        cache()->put('refresh_token', $refreshToken, 3600);
        cache()->put('shop_cipher', $shopCipher, 3600);

        $this->accessToken = $accessToken;
        $this->refreshToken = $refreshToken;
        $this->shopCipher = $shopCipher;
        $this->authCode = $authCode;
    }

    public function handleRefreshToken()
    {
        $this->refreshToken = cache('refresh_token');
        $new_token = $this->auth->refreshNewToken($this->refreshToken);

        cache()->put('access_token', $new_token['access_token'], 3600);
        cache()->put('refresh_token', $new_token['refresh_token'], 3600);
        return [
            'message' => 'success',
            'access_token' => $this->accessToken,
            'refresh_token' => $this->refreshToken
        ];
    }

    public function getAuthorizedShopCipher()
    {
        // $this->client->setAccessToken($access_token);

        // $authorizedShopList = $this->client->Authorization->getAuthorizedShop();
    }
}
