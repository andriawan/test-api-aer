<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Detail Transaksi</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

<div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">

    <div class="p-4 border-b flex items-center justify-between">
        <h1 class="text-lg font-semibold text-gray-800">
            Detail Transaksi
        </h1>

        <a
            href="{{ url('/payments/view') }}"
            class="text-sm text-indigo-600 hover:underline"
        >
            ← Kembali
        </a>
    </div>

    <div class="p-6">
        <table class="w-full text-sm">
            <tbody class="divide-y">

                <tr>
                    <th class="py-3 text-left font-medium text-gray-600 w-1/3">Ref ID</th>
                    <td class="py-3 font-mono text-xs break-all">
                        {{ $transaction['ref_id'] }}
                    </td>
                </tr>

                <tr>
                    <th class="py-3 text-left font-medium text-gray-600">Transaction ID</th>
                    <td class="py-3 font-mono text-xs break-all">
                        {{ $transaction['trx_id'] }}
                    </td>
                </tr>

                <tr>
                    <th class="py-3 text-left font-medium text-gray-600">QR Code</th>
                    <td class="py-3">
                        <div id="qrcode"></div>

                        <script>
                            // Generate QR Code
                            var qrcode = new QRCode(document.getElementById("qrcode"), {
                                text: "{{ $transaction['qr_code'] }}",
                                width: 300,
                                height: 300,
                            });
                        </script>
                    </td>
                </tr>

                <tr>
                    <th class="py-3 text-left font-medium text-gray-600">Amount</th>
                    <td class="py-3 font-semibold">
                        Rp {{ number_format($transaction['amount'], 0, ',', '.') }}
                    </td>
                </tr>

                <tr>
                    <th class="py-3 text-left font-medium text-gray-600">Status</th>
                    <td class="py-3 capitalize">
                        @if ($transaction['status'] === 'completed')
                            <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">
                                Selesai
                            </span>
                        @elseif ($transaction['status'] === 'pending')
                            <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-medium">
                                Pending
                            </span>
                        @else
                            <span class="px-2 py-1 rounded bg-red-100 text-red-800 text-xs font-medium">
                                Gagal
                            </span>
                        @endif
                    </td>

                <tr>
                    <th class="py-3 text-left font-medium text-gray-600">Expired At</th>
                    <td class="py-3">
                        {{ \Carbon\Carbon::parse($transaction['expired_at'])->format('d M Y H:i') }}
                    </td>
                </tr>

                <tr>
                    <th class="py-3 text-left font-medium text-gray-600">Created At</th>
                    <td class="py-3">
                        {{ \Carbon\Carbon::parse($transaction['created_at'])->format('d M Y H:i') }}
                    </td>
                </tr>

            </tbody>
        </table>
    </div>

</div>

</body>
</html>
