<?php

namespace App\Http\Controllers\woocommerce;

use App\Http\Controllers\Controller;
use Automattic\WooCommerce\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WooController extends Controller
{
    //
    private $woo;
    public function __construct()
    {
        $this->woo = new Client(env('WOO_URL'), env('CONSUMER_KEY'), env('CONSUMER_PRIVATE'));;
    }

    public function getAllWebhook()
    {
        return $this->woo->get('webhooks');
    }

    public function handleWebhook(Request $request)
    {
        // Log::info('Webhook received:', [
        //     'headers' => $request->headers->all(),
        //     'body' => $request->getContent(),
        // ]);

        $data = json_decode($request->getContent(), true);

        $eventType = $request->header('x-wc-webhook-topic');

        return response()->json([
            'message' => 'success',
            'data' => $data
        ], status: 200);
    }

    private function verify(Request $request)
    {
        $secret = env('WOO_WEBHOOK_SECRET');

        $signature = $request->header('X-WC-Webhook-Signature');

        // Tính toán chữ ký
        $computedSignature = base64_encode(hash_hmac('sha256', $request->getContent(), $secret, true));

        // So sánh chữ ký
        if ($signature !== $computedSignature) {
            abort(403, 'Unauthorized webhook signature.');
        }
        // Nếu xác thực thành công, xử lý dữ liệu tiếp theo
    }
}
