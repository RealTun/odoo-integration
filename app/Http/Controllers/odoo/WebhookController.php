<?php

namespace App\Http\Controllers\odoo;

use App\Http\Controllers\Controller;
use App\Http\Controllers\tiktok\ProductController;
use App\Http\Controllers\tiktok\CustomerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    //
    public function handleWebhook(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);
        Log::info('Webhook received from odoo:', [
            'data' => $data
        ]);

        $type = $data['_model'];
        switch($type)
        {
            case 'product.template':
                $productController = new ProductController();

                if(array_key_exists('list_price', $data)){
                    cache()->put('product_id', $data['_id']);
                    $productController->createProduct($data);
                }
                else{
                    $id = $data['barcode'];
                    $productController->deleteProduct($id);
                }
                break;
            case 'res.partner':
                // $customerController = new CustomerController();
                // $customerController->createCustomerViaHook($data);
                break;
            default: break;
        }
    }
}
