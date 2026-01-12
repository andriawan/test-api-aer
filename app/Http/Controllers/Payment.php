<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CekLaporanQrService;
use App\Models\PaymentHistory;
use Inertia\Inertia;

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
            if(!$qrResponse['status']) {
                \Log::error('QR Code Generation Failed:', $qrResponse);
                throw new \Exception('Failed to generate QR code: please contact support');
            }
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
            return redirect()->back()->withErrors(['error' => 'Payment processing failed: ' . $e->getMessage()]);
        }
    }

    public function getAllPayments()
    {
        $payments = PaymentHistory::all();
        return response()->json($payments);
    }

    public function getAllPaymentsBlade()
    {
        $queryPayment = PaymentHistory::query();
        $queryPayment->when(request('transaction_status'), function ($query, $status) {
            $query->whereIn('transaction_status', explode(',', $status));
        });
        $queryPayment->when(request('ref_id'), function ($query, $refId) {
            $query->whereIn('ref_id', explode(',', $refId));
        });
        $queryPayment->when(request('trx_id'), function ($query, $trxId) {
            $query->whereIn('trx_id', explode(',', $trxId));
        });
        applyMultiNumericFilterIfValid(
            $queryPayment,
            'amount',
            request('amount')
        );
        $payments = $queryPayment->paginate(10)->appends(request()->query());
        return Inertia::render('Payments/Index', ['transactions' => $payments]);
    }

    public function viewPaymentByRefId(Request $request)
    {
        $qrInfo = [
            'data' => null
        ];
        $refId = $request->route('ref_id');
        $payment = PaymentHistory::where('ref_id', $refId)->firstOrFail();
        $success = false;
        if($payment->transaction_status !== '00') {
            $qrInfo = $this->qrService->getQrInfo($payment);
        }
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

        if(isset($qrInfo['data']) && $qrInfo['data']['transaction_status']) {
            $success = true;
            session()->flash('success', $success);
        }
        
        \Log::debug('QR Info Retrieved:', $qrInfo);
        return Inertia::render('Payments/Show', ['transaction' => $payment]);
    }

    public function processCallbackQr(Request $request)
    {
        $data = $request->all();
        \Log::debug('QR Callback Data:', $data);

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