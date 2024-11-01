<?php

namespace App\Http\Controllers\odoo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ripoo\OdooClient;

class ProductController extends Controller
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

    public function getAllProduct()
    {
        $criteria = [
            // ['is_company', '=', true],
        ];

        $fields = ['id', 'name', 'type', 'categ_id', 'description_ecommerce', 'list_price', 'barcode', 'weight', 'is_published'];

        $products = $this->client->search_read('product.template', $criteria, $fields);

        return response($products);
    }

    public function getProduct(string $id)
    {
        $criteria = [
            ['id', '=', $id]
        ];

        $fields = ['id', 'name', 'type', 'categ_id', 'description_ecommerce', 'list_price', 'barcode', 'weight', 'is_published'];

        $products = $this->client->search_read('product.template', $criteria, $fields);

        return response($products);
    }
}
