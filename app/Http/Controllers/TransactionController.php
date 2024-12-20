<?php

namespace App\Http\Controllers;

use App\Models\Config;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Xendit\Configuration;
use Xendit\Invoice\InvoiceApi;
use Xendit\Invoice\CreateInvoiceRequest;

class TransactionController extends Controller
{
    protected $xenditConfig;

    public function __construct()
    {
        $this->xenditConfig = Config::where('name', 'xendit')->first();
        Configuration::setXenditKey($this->xenditConfig->value['XENDIT_API_KEY']);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $order = Order::findOrFail($request->order_id);

        $orderItems = OrderItem::where('order_id', $order->id)
            ->with('menu', function ($query) {
                $query->select('id', 'name', 'price');
            })
            ->orderBy('id')
            ->get();

        $amount = $orderItems->sum('total_price');
        $orderItemsNames = $orderItems->pluck('menu.name');
        $orderItemsPrices = $orderItems->pluck('menu.price');
        $orderItemsQuantities = $orderItems->pluck('quantity');

        $message = "Terima kasih anda telah melakukan pemesanan di restoran kami. Berikut adalah rincian pesanan Anda:";
        foreach ($orderItemsNames as $index => $orderItemName) {
            $message .= "\n - " . $orderItemName .
                " (IDR " . number_format($orderItemsPrices[$index], 0, ',', '.') .
                ") x " . $orderItemsQuantities[$index];
        }

        $tableUrl = Table::findOrFail($order->table_id)->url;

        $uuid = (string) Str::uuid();

        $apiInstance = new InvoiceApi();
        $createInvoiceRequest = new CreateInvoiceRequest([
            'external_id' => $uuid,
            'amount' => $amount,
            'description' => $message,
            "customer" => [
                "given_names" => $order->customer_name,
                "mobile_number" => $order->customer_phone,
            ],
            "customer_notification_preference" => [
                "invoice_created" => [
                    "whatsapp",
                ],
                "invoice_reminder" => [
                    "whatsapp",
                ],
                "invoice_paid" => [
                    "whatsapp",
                ]
            ],
            "success_redirect_url" => $this->xenditConfig->value['REDIRECT_URL'] . 'orders/' . $tableUrl,
            "failure_redirect_url" => $this->xenditConfig->value['REDIRECT_URL'] . 'orders/' . $tableUrl,
            'currency' => 'IDR',
        ]);

        try {
            $result = $apiInstance->createInvoice($createInvoiceRequest);

            Transaction::create(
                [
                    'order_id' => $order->id,
                    'status' => $result['status'],
                    'checkout_url' => $result['invoice_url'],
                    'external_id' => $uuid
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Transaction created successfully',
                'data' => $result['invoice_url']
            ], 201);
        } catch (\Xendit\XenditSdkException $e) {

            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function storeOffline(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $uuid = (string) Str::uuid();

        try {

            $transaction = Transaction::create([
                'order_id' => $request->order_id,
                'external_id' => $uuid,
                'status' => 'PENDING',
                'payment_method' => 'OFFLINE',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Transaction created successfully',
                'data' => $transaction
            ], 201);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);
        return response()->json(['data' => $transaction]);
    }

    public function transactionStatusMethod($orderId)
    {
        $transaction = Transaction::where('order_id', $orderId)->firstOrFail();
        $status = $transaction->status;
        $payment_method = $transaction->payment_method;

        return response()->json([
            'status' => $status,
            'payment_method' => $payment_method
        ]);
    }

    public function confirmOfflinePayment(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        try {

            $transaction->update([
                'status' => 'PAID',
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Offline payment confirmed',
                'data' => $transaction
            ], 200);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }

    public function notificationCallback(Request $request)
    {
        $callbackToken = $this->xenditConfig->value['XENDIT_CALLBACK_TOKEN'];

        if ($request->header('x-callback-token') !== $callbackToken) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid callback token'
            ], 400);
        }

        try {

            $externalId = $request->external_id;

            $transaction = Transaction::where('external_id', $externalId)->firstOrFail();

            if ($transaction->status === 'SETTLED') {
                return response()->json(['data' => 'Payment anda telah berhasil di proses']);
            }

            $transaction->update([
                'status' => $request->status,
                'payment_method' => $request->payment_method
            ]);

            return response()->json([
                'message' => 'Success'
            ], 200);
        } catch (\Xendit\XenditSdkException $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ], 500);
        }
    }
}
