<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderController extends Controller
{
    public function submit(Request $request)
    {
        $data = $request->validate([
            'order.name' => 'required|string',
            'order.email' => 'nullable|email',
            'order.phone' => 'required|string',
            'order.address' => 'required|string',
            'order.notes' => 'nullable|string',
            'products' => 'required|array',
        ]);

        $order = $request->order;
        $products = $request->products;

        // Build email text
        $messageText = "🛒 NEW ORDER RECEIVED\n\n";
        $messageText .= "Customer Details:\n";
        $messageText .= "Name: {$order['name']}\n";
        $messageText .= "Phone: {$order['phone']}\n";
        $messageText .= "Email: " . ($order['email'] ?? 'N/A') . "\n";
        $messageText .= "Address: {$order['address']}\n";
        $messageText .= "Notes: " . ($order['notes'] ?? 'None') . "\n\n";

        $messageText .= "Ordered Products:\n";

        foreach ($products as $p) {
            $messageText .= "- {$p['name']} | Qty: {$p['qty']} | Price: {$p['price']}\n";
        }

        Log::info('Sending order email to abdullahajmal1488@gmail.com with content: ' . $messageText);

        try {
            Mail::raw($messageText, function ($message) {
                $message->to('abdullahajmal1488@gmail.com')
                        ->subject('New Order - AB Pink Salt Decor');
            });
        } catch (\Exception $e) {
            Log::error('Failed to send order email: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'message' => 'Order submitted but email failed to send'
            ], 500);
        }

        return response()->json([
            'status' => true,
            'message' => 'Order sent successfully via email'
        ]);
    }
}
