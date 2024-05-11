<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\PaymentLogs;
use GuzzleHttp\Client;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function midtransHandler(Request $request)
    {
        $data = $request->all();
        $signatureKey = $data['signature_key'];
        $orderId = $data['order_id'];
        $statusCode = $data['status_code'];
        $groossAmount = $data['gross_amount'];
        $serverKey = env('MIDTRANS_SERVER_KEY');

        $mySignature = hash('sha512', $orderId . $statusCode . $groossAmount . $serverKey);

        $transactionStatus = $data['transaction_status'];
        $type = $data['payment_type'];
        $fraudStatus = $data['fraud_status'];

        if ($signatureKey !== $mySignature) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid signature key'
            ], 400);
        }
        $orderIdsSplit = explode('-', $orderId);
        $order = Order::find($orderIdsSplit[0]);
        if (!$order) {
            return response()->json([
                'status' => false,
                'message' => 'Order IDs not found'
            ], 404);
        }

        // Perform operations on each order
        if ($order->status === 'success') {
            return response()->json([
                'status' => true,
                'message' => 'Operation not permitted'
            ], 400);
        }

        // Rest of the code...

        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'accept') {

                $order->status = 'success';
            }
        } else if ($transactionStatus == 'settlement') {

            $order->status = 'success';
        } else if (
            $transactionStatus == 'cancel' ||
            $transactionStatus == 'deny' ||
            $transactionStatus == 'expire'
        ) {
            $order->status = 'failure';
        } else if ($transactionStatus == 'pending') {
            $order->status = 'pending';
        }
        $historyData = [
            'status' => $transactionStatus,
            'raw_response' => json_encode($data),
            'order_id' => $orderIdsSplit[0],
            'payment_type' => $type
        ];
        PaymentLogs::create($historyData);
        $order->save();

        if ($order->status === 'success') {
            $myCourseController = new MyCourseController();
            $response = $myCourseController->createPremiumAccess(new Request([
                'user_id' => $order->user_id,
                'course_id' => $order->course_id

            ]));
        }
        return response()->json([
            'status' => true,
            'message' =>  'success'
        ]);
    }
}
