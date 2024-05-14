<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->input('user_id');
        $orders = Order::query();
        $orders->when($userId, function ($query) use ($userId) {
            return $query->where('user_id', $userId);
        });
        return response()->json([
            'status' => true,
            'data' => $orders->get()
        ]);
    }
    public function create(Request $request)
    {
        $user = $request->input('user');
        $course = $request->input('course');
        $rules = [
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
        ];
        $messages = [
            'user_id.exists' => 'User not found',
            'user_id.required' => 'User id is required',
            'course_id.exists' => 'Course not found',
            'course_id.required' => 'Course id is required',

        ];
        $data = $request->all();
        $validator = validator($data, $rules, $messages);
        $order = Order::create([
            'user_id' => $user['id'],
            'course_id' => $course['id'],
        ]);
        $tansactionDetails = [
            'order_id' => $order->id . '-' . Str::random(5),
            'gross_amount' => $course['price'],
        ];
        $itemDetails = [
            [
                'id' => $course['id'],
                'price' => $course['price'],
                'quantity' => 1,
                'name' => $course['name'],
                'brand' => 'starscode',
                'category' => 'Online Course',
            ]
        ];
        $customerDetails = [
            'first_name' => $user['name'],
            'email' => $user['email']
        ];
        $midtransParams = [
            'transaction_details' => $tansactionDetails,
            'item_details' => $itemDetails,
            'customer_details' => $customerDetails

        ];
        $midtransSnapUrl = $this->getMidtransSnapUrl($midtransParams);
        $order->snap_url = $midtransSnapUrl;
        $order->metadata = [
            'course_id' => $course['id'],
            'course_price' => $course['price'],
            'course_name' => $course['name'],
            'course_thumbnail' => $course['thumbnail'],
            'course_level' => $course['level']
        ];
        $order->save();
        return  response()->json([
            'status' => true,
            'data' => $order
        ]);
    }
    private function getMidtransSnapUrl($params)
    {

        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = (bool) env('MIDTRANS_IS_PRODUCTION');
        \Midtrans\Config::$isSanitized = (bool) env('MIDTRANS_ISSANITIZED');
        \Midtrans\Config::$is3ds = (bool)  env('MIDTRANS_IS_3DS');

        $snapUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;
        return $snapUrl;
    }
}
