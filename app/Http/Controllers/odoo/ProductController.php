<?php

namespace App\Http\Controllers\odoo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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

    public function getAllBarcode()
    {
        $criteria = [
            // ['is_company', '=', true],
        ];

        $fields = ['barcode'];

        $products = $this->client->search_read('product.template', $criteria, $fields);
        $barcodes = [];
        foreach ($products as $product) {
            if($product['barcode'] != false){
                $barcodes[] = $product['barcode'];
            }
        }

        return $barcodes;
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

    public function createProduct(Request $request)
    {
        // $data = [
        //     'name' => 'San pham dau buoi',
        //     'type' => 'consu',
        //     'categ_id' => 1,
        //     'description_ecommerce' => 'san pham nhu cc dung co mua',
        //     'list_price' => 2100.5,
        //     'barcode' => '21062003',
        //     'weight' => 0.3,
        //     'is_published' => true
        // ];

        try {
            $id = $this->client->create('product.template', $request->all());
            $response = $this->getProduct($id);
            return response([
                'status' => 'success',
                'data' => json_decode($response->content(), true)
            ], 201);
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateProduct(Request $request, int $id)
    {
        try {
            $this->client->write('product.template', [$id], $request->all());
            $response = $this->getProduct($id);
            $data = json_decode($response->content(), true);
            return response([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function deleteProduct(int $id)
    {
        try {
            // $ids = $this->client->search('res.partner', [['email', '=', 'foo@bar.com']], 0, 1);
            $this->client->unlink('product.template', [$id]);
            return response([
                'status' => 'success',
                'data' => []
            ], 200);
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    // via tiktok
    public function createProductViaTiktok($raw)
    {
        $raw = json_decode($raw, true);
        $data = [
            'name' => $raw['title'],
            'type' => 'consu',
            'categ_id' => 1,
            // 'description_ecommerce' => 'san pham nhu cc dung co mua',
            'list_price' => $raw['price'],
            'barcode' => $raw['id'],
            'weight' => 0.3,
            'is_published' => $raw['status'] == "ACTIVE" ? true : false
        ];

        try {
            $id = $this->client->create('product.template', $data);
            // $response = $this->getProduct($id);
            // return response([
            //     'status' => 'success',
            //     'data' => json_decode($response->content(), true)
            // ], 201);
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    public function updateProductViaHook($body)
    {
        try {
            $id = cache()->get('product_id');;
            $this->client->write('product.template', [$id], $body);
            // $response = $this->getProduct($id);
            // $data = json_decode($response->content(), true);
            Log::info('Update barcode success');
            cache()->delete('product_id');
        } catch (\Exception $e) {
            Log::error('Update barcode error');
            // return response([
            //     'status' => 'error',
            //     'message' => $e->getMessage()
            // ], 400);
        }
    }
}
