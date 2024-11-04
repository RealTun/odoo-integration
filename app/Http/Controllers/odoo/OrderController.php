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

    public function getAllOrder()
    {
        $criteria = [
            // ['is_company', '=', true],
        ];

        $fields = ['id', 'partner_id', 'date_order', 'commitment_date', 'order_line', 'amount_total', 'invoice_status', 'delivery_status', 'state'];

        $orders = $this->client->search_read('sale.order', $criteria, $fields);
        $data = [];
        foreach ($orders as $order) {
            $pids = $this->getProductFromOrderLine($order['order_line']);
            $order['product_id'] = $pids; 
            $data[] = $order;
        }

        return response($data);
    }

    private function getProductFromOrderLine($order_line_ids)
    {
        $orders_line = $this->client->search_read('sale.order.line', [], ['product_id']);
        $pids = [];

        foreach ($orders_line as $line) {
            array_push($pids, $line['product_id'][0]);
        }

        return $pids;
    }

    public function getOrder(string $id)
    {
        $criteria = [
            ['id', '=', $id]
        ];

        $fields = ['id', 'partner_id', 'date_order', 'commitment_date', 'order_line', 'amount_total', 'invoice_status', 'delivery_status', 'state'];

        $order = $this->client->search_read('sale.order', $criteria, $fields);

        return response($order);
    }

    public function createOrder(Request $request)
    {
        try {
            $id = $this->client->create('sale.order', $request->all());
            $response = $this->getOrder($id);
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

    public function updateOrder(Request $request, int $id)
    {
        try {
            $this->client->write('sale.order', [$id], $request->all());
            $response = $this->getOrder($id);
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

    public function deleteOrder(int $id)
    {
        try {
            // $ids = $this->client->search('res.partner', [['email', '=', 'foo@bar.com']], 0, 1);
            $this->client->unlink('sale.order', [$id]);
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
}
