<?php

namespace App\Http\Controllers\erpnext;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    //
    public function handleWebhook(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);
        Log::info('Webhook received from erpnext:', [
            'data' => $data
        ]);
    }
}
