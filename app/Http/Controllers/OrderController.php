<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\Notification; // Make sure you have imported the Notification model
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    // Step 1: Store sender information
    public function storeSenderInfo(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sender_name' => 'required|string',
            'sender_address' => 'required|string',
            'sender_phone' => 'required|string',
            'item' => 'required|string',
            'package_size' => 'required|string',
            'delivery_type' => 'required|string',
            'delivery_time' => 'required|string',
            'pickup_time' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Create a new order (initially only sender info)
        $order = Order::create($request->only([
            'sender_name',
            'sender_address',
            'sender_phone',
            'item',
            'package_size',
            'delivery_type',
            'delivery_time',
            'pickup_time',
        ]));

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    // Step 2: Store receiver information
    public function storeReceiverInfo(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'receiver_name' => 'required|string',
            'receiver_address' => 'required|string',
            'receiver_phone' => 'required|string',
            'additional_info' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Update the order with receiver info
        $order = Order::findOrFail($id);
        $order->update($request->only([
            'receiver_name',
            'receiver_address',
            'receiver_phone',
            'additional_info'
        ]));

        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    // Step 3: Preview order (just show the current order details)
    public function show($id)
    {
        $order = Order::findOrFail($id);
        return response()->json([
            'success' => true,
            'order' => $order
        ]);
    }

    // Confirm the order
    public function confirmOrder(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // Validate payment method
        $validator = Validator::make($request->all(), [
            'payment_method' => 'required|in:wallet,paystack,cash_on_pickup',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 400);
        }

        // Update order status and payment method
        $order->status = 'confirmed';
        $order->payment_method = $request->payment_method;
        $order->save();

        // Create a notification for the user
        Notification::create([
            'user_id' => $order->user_id, // Assuming the order has a user_id field
            'type' => 'order_confirmation',
            'message' => 'Your order has been confirmed.',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Order confirmed. Proceed to payment.',
            'order_id' => $order->id
        ]);
    }

    // Search orders
    public function search(Request $request)
    {
        $query = Order::query();
        
        // Search by order ID or keywords in description, etc.
        if ($request->has('keyword')) {
            $query->where('order_id', 'like', '%' . $request->keyword . '%')
                  ->orWhere('description', 'like', '%' . $request->keyword . '%');
        }

        // Advanced filter by name
        if ($request->has('name')) {
            $query->where('customer_name', 'like', '%' . $request->name . '%');
        }

        // Advanced filter by address
        if ($request->has('address')) {
            $query->where('customer_address', 'like', '%' . $request->address . '%');
        }

        // Return the filtered results
        $orders = $query->get();
        
        return response()->json($orders);
    }
}
