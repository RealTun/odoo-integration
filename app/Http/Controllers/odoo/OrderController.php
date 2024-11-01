<?php

namespace App\Http\Controllers\odoo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ripoo\OdooClient;

class OrderController extends Controller
{
    //
    private $client;
    public function __construct()
    {
        $host = env('ODOO_HOST');
        $db = env('ODOO_DB');
        $user = env('ODOO_USER');
        $password = env('ODOO_PWD');

        $this->client = new OdooClient($host, $db, $user, $password);
    }
}
