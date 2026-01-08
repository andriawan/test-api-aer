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
            $expiredAt = now('Asia/Jakarta')->addMinutes(15);
            $qrResponse = $this->qrService->generateQr($refId, $amount, $expiredAt);
            \Log::info('Generated QR Code:', $qrResponse);
            $qrResponse = $qrResponse['data'];
            PaymentHistory::create([
                'ref_id'     => $qrResponse['refid'],
                'trx_id'     => $qrResponse['trxid'],
                'qr_code'    => $qrResponse['qr_code'],
                'amount'     => $amount,
                'status'     => 'pending',
                'expired_at' => $expiredAt,
            ]);
            return \redirect()->route('payment.view.single', ['ref_id' => $refId]);
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
        $qrInfo = $this->qrService->getQrInfo($payment);
        if(isset($qrInfo['data'])) {
            $payment->update([
                'transaction_status' => $qrInfo['data']['transaction_status'] ?? null,
                'transaction_desc'   => $qrInfo['data']['transaction_desc'] ?? null,
                'brand_name'         => $qrInfo['data']['brand_name'] ?? null,
                'buyer_ref'          => $qrInfo['data']['buyer_ref'] ?? null,
                'status'             => strtolower($qrInfo['data']['transaction_status'] ?? 'pending'),
                'trx_date'           => isset($qrInfo['data']['trx_date']) ? 
                                        \Carbon\Carbon::parse($qrInfo['data']['trx_date']) : null,
            ]);
        }
        
        \Log::info('QR Info Retrieved:', $qrInfo);
        return view('payments.show', ['transaction' => $payment]);
    }

    public function processCallbackQr(Request $request)
    {
        $data = $request->all();
        \Log::info('QR Callback Data:', $data);

        $payment = PaymentHistory::where('ref_id', $data['refid'])->firstOrFail();
        if ($payment) {
            $payment->update([
                'transaction_status' => $data['transaction_status'] ?? null,
                'transaction_desc'   => $data['transaction_desc'] ?? null,
                'brand_name'         => $data['brand_name'] ?? null,
                'buyer_ref'          => $data['buyer_ref'] ?? null,
                'status'             => strtolower($data['transaction_status'] ?? 'pending'),
                'trx_date'           => isset($data['trx_date']) ? 
                                        \Carbon\Carbon::parse($data['trx_date']) : null,
            ]);
        }

        return response()->json(['status' => 'success']);
        
    }
}