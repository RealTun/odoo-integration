<?php

namespace App\Http\Controllers\woocommerce;

use App\Http\Controllers\Controller;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;

class WooController extends Controller
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

    public function getProducts()
    {
        return $this->woo->get('products');
    }
}
