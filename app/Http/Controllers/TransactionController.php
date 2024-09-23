<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
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

        $tableUrl = Table::findOrFail($order->table_id)->url;

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
            "success_redirect_url" => env('FRONTEND_URL') . 'orders/' . $tableUrl,
            "failure_redirect_url" => env('FRONTEND_URL') . 'orders/' . $tableUrl
        ]);

        try {
            $result = $apiInstance->createInvoice($createInvoiceRequest);

            Transaction::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'status' => $result['status']
                ],
                [
                    'checkout_url' => $result['invoice_url'],
                    'external_id' => $uuid
                ]
            );

            // $transaction = new Transaction();
            // $transaction->order_id = $order->id;
            // $transaction->checkout_url = $result['invoice_url'];
            // $transaction->external_id = $uuid;
            // $transaction->status = $result['status'];
            // $transaction->save();

            return response()->json(['data' => $result['invoice_url']]);
        } catch (\Xendit\XenditSdkException $e) {
            return response()->json(['message' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);
        return response()->json(['data' => $transaction]);
    }

    public function paidStatus($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->firstOrFail();
        $status = $transaction->status;

        return response()->json(['status' => $status]);
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
