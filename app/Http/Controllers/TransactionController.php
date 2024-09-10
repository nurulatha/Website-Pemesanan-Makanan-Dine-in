<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class TransactionController extends Controller
{
    public function __construct()
    {
        Configuration::setXenditKey(env("XENDIT_API_KEY"));
    }

    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required'
        ]);

        $order = Order::findOrFail($request->order_id);
        $amount = OrderItem::where('order_id', $order->id)->sum('total_price');

        $uuid = (string) Str::uuid();

        $apiInstance = new InvoiceApi();

        $createInvoiceRequest = new CreateInvoiceRequest([
            'external_id' => $uuid,
            // 'description' => 'Test Invoice',
            'amount' => $amount,
            'currency' => 'IDR',
            "customer" => [
                "given_names" => $order->customer_name,
            ],
            "success_redirect_url" => env('NGROK_URL'),
            "failure_redirect_url" => env('NGROK_URL'),
        ]);

        try {
            $result = $apiInstance->createInvoice($createInvoiceRequest);

            $transaction = new Transaction();
            $transaction->order_id = $order->id;
            $transaction->checkout_url = $result['invoice_url'];
            $transaction->external_id = $uuid;
            $transaction->status = 'pending';
            $transaction->save();

            return response()->json(['data' => $result['invoice_url']]);
        } catch (\Xendit\XenditSdkException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function notificationCallback(Request $request)
    {
        $externalId = $request->external_id;
        $status = $request->status;

        $transaction = Transaction::where('external_id', $externalId)->firstOrFail();

        if ($transaction->status == 'settled') {
            return response()->json(['data' => 'Payment anda telah berhasil di proses']);
        }

        $transaction->status = $status;
        $transaction->save();

        return response()->json(['message' => 'Success'], 200);
    }
}
