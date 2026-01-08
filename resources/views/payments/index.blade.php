<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Daftar Transaksi</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

<body class="bg-gray-100 min-h-screen p-6">

    <div class="bg-white rounded-xl shadow-md overflow-hidden">

        <div class="p-4 border-b">
            <h1 class="text-lg font-semibold text-gray-800">
                Daftar Transaksi
            </h1>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Ref ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Transaction ID</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">QR</th>
                        <th class="px-4 py-3 text-right font-medium text-gray-600">Amount</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Status</th>
                        <th class="px-4 py-3 text-left font-medium text-gray-600">Expired At</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @foreach ($transactions as $trx)
                        <tr class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-800">
                                {{ $trx['id'] }}
                            </td>

                            <td class="px-4 py-3 font-mono text-xs break-all">
                                <a class="text-blue-600 hover:underline" href="{{ url('/payments/view/' . $trx['ref_id']) }}">
                                    {{ $trx['ref_id'] }}
                                </a>
                            </td>

                            <td class="px-4 py-3 font-mono text-xs break-all">
                                {{ $trx['trx_id'] }}
                            </td>

                            <td class="px-4 py-3">
                                <div data-qr="{{ $trx['qr_code'] }}" class="qr-code-container hidden"></div>
                            </td>

                            <td class="px-4 py-3 text-right font-semibold">
                                Rp {{ number_format($trx['amount'], 0, ',', '.') }}
                            </td>

                            <td>
                                @if ($trx['transaction_status'] === 'completed')
                                    <span class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">
                                        Selesai
                                    </span>
                                @elseif ($trx['transaction_status'] === 'pending')
                                    <span class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-medium">
                                        Pending
                                    </span>
                                @else
                                    <span class="px-2 py-1 rounded bg-gray-300 text-gray-800 text-xs font-medium">
                                        {{ $trx['transaction_status'] }} | {{ $trx['transaction_desc'] }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-gray-700">
                                {{ \Carbon\Carbon::parse($trx['expired_at'])->format('d M Y H:i') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <!-- PAGINATION -->
    <div class="px-4 py-3 border-t flex items-center justify-between text-sm">
        <div class="text-gray-600">
            Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }}
        </div>

        <div class="flex gap-2">
            @if ($transactions->onFirstPage())
                <span class="px-3 py-1 rounded bg-gray-200 text-gray-400 cursor-not-allowed">
                    Previous
                </span>
            @else
                <a
                    href="{{ $transactions->previousPageUrl() }}"
                    class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200"
                >
                    Previous
                </a>
            @endif

            @if ($transactions->hasMorePages())
                <a
                    href="{{ $transactions->nextPageUrl() }}"
                    class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200"
                >
                    Next
                </a>
            @else
                <span class="px-3 py-1 rounded bg-gray-200 text-gray-400 cursor-not-allowed">
                    Next
                </span>
            @endif
        </div>
    </div>

    </div>

</body>
<script>
        // Generate QR codes
        const qrContainers = document.querySelectorAll('.qr-code-container');
        qrContainers.forEach(container => {
            const qrData = container.getAttribute('data-qr');
            if (qrData) {
                const qrCode = new QRCode(container, {
                    text: qrData,
                    width: 80,
                    height: 80,
                });
                // Show the QR code container
                container.classList.remove('hidden');
            }
        });
</script>
</html>
