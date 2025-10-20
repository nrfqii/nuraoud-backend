<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Voucher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoucherController extends Controller
{
    public function applyVoucher(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required|string',
            'subtotal' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $voucher = Voucher::where('code', $request->code)->first();

        if (!$voucher) {
            return response()->json([
                'message' => 'Kode voucher tidak ditemukan'
            ], 404);
        }

        if (!$voucher->isValidForOrder($request->subtotal)) {
            return response()->json([
                'message' => 'Voucher tidak valid atau sudah kadaluarsa'
            ], 400);
        }

        $subtotal = $request->subtotal;
        $discount = $voucher->calculateDiscount($subtotal);

        if ($discount <= 0) {
            return response()->json([
                'message' => 'Voucher tidak dapat digunakan untuk pesanan ini'
            ], 400);
        }

        return response()->json([
            'voucher' => [
                'id' => $voucher->id,
                'code' => $voucher->code,
                'name' => $voucher->name,
                'type' => $voucher->type,
                'value' => $voucher->value,
            ],
            'discount' => $discount,
            'subtotal' => $subtotal,
            'total_after_discount' => $subtotal - $discount,
        ]);
    }
}
