<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeController extends Controller
{
    public function generateQrCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'url' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        try {

            $qrCode = QrCode::size(300)->generate($request->url);

            return response()->json([
                'qr_code' => 'data:image/svg+xml;base64,' . base64_encode($qrCode)
            ]);
        } catch (\Throwable $th) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong'
            ]);
        }
    }
}
