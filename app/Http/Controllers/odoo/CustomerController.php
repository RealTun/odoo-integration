<?php

namespace App\Http\Controllers\odoo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Ripcord\Ripcord;
use Ripoo\OdooClient;

class CustomerController extends Controller
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

    public function getAllCustomer()
    {
        $criteria = [
            ['is_company', '=', false],
        ];

        $fields = ['id', 'name', 'email', 'phone', 'mobile', 'street', 'city', 'zip'];

        $customers = $this->client->search_read('res.partner', $criteria, $fields);

        return response($customers);
    }

    public function getCustomer(string $id)
    {
        $criteria = [
            ['id', '=', $id],
        ];

        $fields = ['id', 'name', 'email', 'phone', 'mobile', 'street', 'city', 'zip'];

        $customers = $this->client->search_read('res.partner', $criteria, $fields, 1);

        return response($customers);
    }

    public function createCustomer()
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'foo@bar.com',
            'phone' => '0019193213',
            'street' => 'Nguyen An Ninh',
            'city' => 'HN'
        ];

        try {
            $id = $this->client->create('res.partner', $data);
            $response = $this->getCustomer($id);
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

    public function updateCustomer(Request $request, int $id)
    {
        try {
            $this->client->write('res.partner', [$id], $request->all());
            $response = $this->getCustomer($id);
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

    public function deleteCustomer(int $id)
    {
        try {
            // $ids = $this->client->search('res.partner', [['email', '=', 'foo@bar.com']], 0, 1);
            $this->client->unlink('res.partner', [$id]);
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

    public function createCustomeViaTiktok($raw)
    {
        $raw = json_decode($raw, true);
        $data = [
            'name' => $raw['name'],
            'email' => '',
            'phone' => $raw['phone_number'],
            'street' => $raw['district'],
            'city' => $raw['city'],
        ];
     
        try {
            $flag = $this->isExisted($raw);
            if($flag) {
                return;
            }

            $this->client->create('res.partner', $data);
        } catch (\Exception $e) {
            return response([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function isExisted($raw)
    {
        $criteria = [
            ['name', '=', $raw['name']],
            ['phone', '=', $raw['phone_number']],
        ];

        $fields = ['id', 'name', 'email', 'phone', 'mobile', 'street', 'city', 'zip'];

        $customer = $this->client->search_read('res.partner', $criteria, $fields, 1);

        if($customer) {
            return true;
        }

        return false;
    }
}
