<?php

namespace App\Http\Controllers;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConfigController extends Controller
{
    public function index()
    {
        $config = Config::all();

        return response()->json(['data' => $config]);
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'XENDIT_API_KEY' => 'required',
            'XENDIT_CALLBACK_TOKEN' => 'required',
            'REDIRECT_URL' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'error' => $validator->errors(),
            ], 422);
        }

        $xendit_redirect_url = $request->REDIRECT_URL;

        if (! str_ends_with($xendit_redirect_url, '/')) {
            $xendit_redirect_url .= '/';
        }

        try {
            $configs = [
                [
                    'name' => 'xendit',
                    'value' => [
                        'XENDIT_API_KEY' => $request->XENDIT_API_KEY,
                        'XENDIT_CALLBACK_TOKEN' => $request->XENDIT_CALLBACK_TOKEN,
                        'REDIRECT_URL' => $xendit_redirect_url,
                    ],
                ],
            ];

            foreach ($configs as $config) {
                $data = Config::where('name', $config['name'])->first();

                $data->update([
                    'name' => $data['name'],
                    'value' => $data['value'],
                ]);
            }

            return response()->json([
                'status' => true,
                'message' => 'Config updated successfully',
                'data' => $configs,
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
            ], 500);
        }
    }
}
