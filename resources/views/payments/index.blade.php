<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Daftar Transaksi</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
</head>

@php
$operator = null;
$number = null;
if (request('amount')!= null && preg_match('/^(gt|gte|lt|lte|eq):(\d+)$/', request('amount'), $m)) {
    $operator = $m[1];
    $number   = (int) $m[2];
}
@endphp
<body class="bg-gray-100 min-h-screen p-6">

    <div class="bg-white rounded-xl shadow-md overflow-hidden">

        <div class="p-4 border-b flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">
                Daftar Transaksi
            </h1>
            <div class="flex gap-4 justify-end flex-1 space-x-2 px-3">
                <a href="{{ route('payments.index') }}" class="bg-red-500 text-white p-2 px-3 rounded">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                        viewBox="0 0 24 24"><!-- Icon from TDesign Icons by TDesign - https://github.com/Tencent/tdesign-icons/blob/main/LICENSE -->
                        <path fill="currentColor"
                            d="m22.122 13.465l1.414 1.414L21.415 17l2.121 2.122l-1.414 1.414L20 18.414l-2.12 2.122l-1.415-1.415l2.121-2.12l-2.121-2.122l1.414-1.414L20 15.586zM3 3h18l-7 9.817V21h-4v-8.183z" />
                    </svg>
                </a>
                <form method="GET" action="{{ route('payments.index') }}" class="flex gap-2">
                    <select id="filter_by"
                        class="border rounded-lg border-gray-300 outline-none focus:border-indigo-500 focus:ring-indigo-500 px-3">
                        <option value="ref_id" {{ request('ref_id') !== null ? 'selected' : '' }}>Ref ID</option>
                        <option value="amount" {{ request('amount') !== null ? 'selected' : '' }}>Amount</option>
                        <option value="trx_id" {{ request('trx_id') !== null ? 'selected' : '' }}>Transaction ID
                        </option>
                    </select>
                    <select id="amount_compare"
                        class="{{ request('amount') === null ? 'hidden' : '' }} border rounded-lg border-gray-300 outline-none focus:border-indigo-500 focus:ring-indigo-500 px-3">
                        <option value="lt" {{ $operator === 'lt' ? 'selected' : ''}}>kurang dari</option>
                        <option value="lte" {{ $operator === 'lte' ? 'selected' : ''}}>kurang dari sama dengan</option>
                        <option value="gt" {{ $operator === 'gt' ? 'selected' : ''}}>lebih dari</option>
                        <option value="gte" {{ $operator === 'gte' ? 'selected' : ''}}>lebih dari sama dengan</option>
                    </select>
                    <input type="text" id="filter_input" value="{{ request('ref_id') ?? request('trx_id') ?? $number}}" placeholder="Cari..." onchange=""
                        class="rounded-lg border-gray-300 border outline-none focus:border-indigo-500 focus:ring-indigo-500 px-3">
                    <form>
                        <input type="hidden" name="trx_id" value="{{ request('trx_id') }}">
                        <input type="hidden" name="ref_id" value="{{ request('ref_id') }}">
                        <input type="hidden" name="amount" value="{{ request('amount') }}">
                        <button type="submit" class="bg-indigo-600 text-white px-3 py-2 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16"
                                viewBox="0 0 24 24"><!-- Icon from Google Material Icons by Material Design Authors - https://github.com/material-icons/material-icons/blob/master/LICENSE -->
                                <path fill="currentColor"
                                    d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5A6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5S14 7.01 14 9.5S11.99 14 9.5 14" />
                            </svg>
                        </button>
                    </form>
            </div>
            <a href="{{ url('/checkout') }}" class="text-sm text-indigo-600 hover:underline">
                + Buat Transaksi Baru
            </a>
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
                            <a class="text-blue-600 hover:underline"
                                href="{{ url('/payments/view/' . $trx['ref_id']) }}">
                                {{ $trx['ref_id'] }}
                            </a>
                        </td>

                        <td class="px-4 py-3 font-mono text-xs break-all">
                            {{ $trx['trx_id'] }}
                        </td>

                        <td class="px-4 py-3">
                            <div data-qr="{{ $trx['qr_code'] }}" class="qr-code-container">
                                <div class="qr-img hidden"></div>
                                <div class="qr-loader w-[80px] h-[80px] bg-gray-400 animate-pulse"></div>
                            </div>
                        </td>

                        <td class="px-4 py-3 text-right font-semibold">
                            Rp {{ number_format($trx['amount'], 0, ',', '.') }}
                        </td>

                        <td>
                            @if ($trx['transaction_status'] === '00')
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
                Page {{ $transactions->currentPage() }} of {{ $transactions->lastPage() }} ({{ $transactions->total() }} transaksi)
            </div>

            <div class="flex gap-2">
                @if ($transactions->onFirstPage())
                <span class="px-3 py-1 rounded bg-gray-200 text-gray-400 cursor-not-allowed">
                    Previous
                </span>
                @else
                <a href="{{ $transactions->previousPageUrl() }}"
                    class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200">
                    Previous
                </a>
                @endif

                @if ($transactions->hasMorePages())
                <a href="{{ $transactions->nextPageUrl() }}" class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200">
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
            setTimeout(() => {
                let qrContainer = container.querySelector(".qr-img")
                let qrLoader = container.querySelector(".qr-loader")
                const qrCode = new QRCode(qrContainer, {
                    text: qrData,
                    width: 80,
                    height: 80,
                });
                // Show the QR code container
                qrContainer.classList.remove('hidden');
                qrLoader.classList.add('hidden');

            }, 800)
        }
    });
    
    const input = document.getElementById('filter_input');
    const filter_by = document.getElementById('filter_by');
    const amount = document.getElementById('amount_compare');

    amount.addEventListener('change', function () {
        refreshQueryParams()
    })

    filter_by.addEventListener('change', function () {
        document.querySelector(`[name='ref_id']`).value = '';
        document.querySelector(`[name='trx_id']`).value = '';
        document.querySelector(`[name='amount']`).value = '';
        if(this.value === 'amount') {
            amount.classList.remove('hidden')
            input.type = 'number'
        } else {
            amount.classList.add('hidden')
            input.type = 'text'
        }
        input.value = ''
    })


    input.addEventListener('keyup', function () {
       refreshQueryParams()
    });

    function refreshQueryParams() {
        const params = new URLSearchParams(window.location.search);
        const filterBy = filter_by.value;
        const amountCompare = amount.value;
        const finalValue = filterBy === 'amount' ? `${amountCompare}:${input.value}` : input.value

        if (input.value) {
            params.set(filterBy, finalValue);
            document.querySelector(`[name=${filterBy}]`).value = finalValue;
        } else {
            params.delete(filterBy);
        }

        const newUrl = window.location.pathname + '?' + params.toString();
        history.pushState({}, '', newUrl);

    }
</script>

</html>