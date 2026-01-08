<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CekLaporanQrService;
use App\Models\PaymentHistory;

class Payment extends Controller
{
    private $qrService;

    public function __construct(CekLaporanQrService $qrService)
    {
        $this->qrService = $qrService;
    }

    public function processPayment(Request $request)
    {
        try {
            $request->validate([
                'amount' => 'required|integer|min:1000',
            ]);

            $data = $request->only('amount');

            // Generate QR Code untuk pembayaran
            $refId = $refId = now()->format('YmdHis') . rand(100,999);
            $amount = $data['amount'];
            $expiredAt = now()->addMinutes(15);
            $qrResponse = $this->qrService->generateQr($refId, $amount, $expiredAt);
            \Log::info('Generated QR Code:', $qrResponse);
            PaymentHistory::create([
                'ref_id'     => $refId,
                'trx_id'     => $qrResponse['trx_id'],
                'qr_code'    => $qrResponse['qr_code'],
                'amount'     => $amount,
                'status'     => 'pending',
                'expired_at' => $expiredAt,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to generate QR code: ' . $e->getMessage(),
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'qr_data' => $qrResponse,
        ]);
    }

    public function getAllPayments()
    {
        $payments = PaymentHistory::all();
        return response()->json($payments);
    }

    public function getAllPaymentsBlade()
    {
        $payments = PaymentHistory::paginate(10);
        return view('payments.index', ['transactions' => $payments]);
    }

    public function viewPaymentByRefId(Request $request)
    {
        $refId = $request->route('ref_id');
        $payment = PaymentHistory::where('ref_id', $refId)->firstOrFail();
        return view('payments.show', ['transaction' => $payment]);
    }
}