<?php

namespace App\Http\Controllers\odoo;

use App\Http\Controllers\Controller;
use App\Http\Controllers\tiktok\ProductController as ProductTiktok;
use App\Http\Controllers\tiktok\OrderController as OrderTiktok;
use App\Http\Controllers\odoo\ProductController as ProductOdoo;
use App\Http\Controllers\odoo\OrderController as OrderOdoo;
use App\Http\Controllers\odoo\CustomerController as CustomerOdoo;
use Illuminate\Http\Request;

class SyncDataController extends Controller
{
    //
    public function syncProductsFromTiktok()
    {
        ini_set('max_execution_time', 500);
        $productTiktok = new ProductTiktok();
        $productOdoo = new ProductOdoo();

        $productsFromTiktok = $productTiktok->getProducts();
        $barcodes = $productOdoo->getAllBarcode();

        foreach ($productsFromTiktok as $p) {
            if(in_array($p['id'], $barcodes)){
                continue;
            }
            $p_encode = json_encode($p);
            $productOdoo->createProductViaTiktok($p_encode);
        }
        return response([
            'status' => 'success'
        ]);
    }

    public function syncOrdersFromTiktok()
    {
        ini_set('max_execution_time', 500);
        $orderTiktok = new OrderTiktok();
        $orderOdoo = new OrderOdoo();

        $ordersFromTiktok = $orderTiktok->getOrders();
        dd($ordersFromTiktok);
        foreach ($ordersFromTiktok as $o) {
            // $orderOdoo->createProductViaTiktok($p);
        }
        return response([
            'status' => 'success'
        ]);
    }

    public function syncCustomersFromTiktok()
    {
        ini_set('max_execution_time', 500);
        $orderTiktok = new OrderTiktok();
        $customerOdoo = new CustomerOdoo();

        $ordersFromTiktok = $orderTiktok->getOrders();
        foreach ($ordersFromTiktok as $customer) {
            $customer = json_encode($customer['customer']);
            $customerOdoo->createCustomeViaTiktok($customer);
        }
        return response([
            'status' => 'success'
        ]);
    }
}
