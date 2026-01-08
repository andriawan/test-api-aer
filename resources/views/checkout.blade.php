<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pembayaran</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'ui-sans-serif', 'system-ui'],
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-100 min-h-screen flex items-center justify-center">

    <form
        method="POST"
        action="{{ url('/process-payment') }}"
        class="bg-white p-6 rounded-xl shadow-md w-full max-w-md space-y-4"
    >
        @csrf

        <h1 class="text-xl font-semibold text-gray-800">
            Form Pembayaran
        </h1>

        <!-- Amount Input -->
        <div>
            <label class="block text-sm font-medium text-gray-700">
                Nominal Pembayaran
            </label>

            <input
                id="amount_display"
                type="text"
                inputmode="numeric"
                placeholder="Rp 0"
                class="mt-1 block w-full rounded-lg outline-none border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"
                autocomplete="off"
            >

            <!-- Hidden numeric input -->
            <input type="hidden" name="amount" id="amount">
        </div>

        <!-- Submit Button -->
        <button
            type="submit"
            class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition"
        >
            Proses Pembayaran
        </button>

        <div>
        <a href="{{ url('/payments/view') }}" class="text-sm text-indigo-600 hover:underline">
            ← Lihat Daftar Transaksi
    </div>

    </form>

    <!-- Currency Formatter -->
    <script>
        const displayInput = document.getElementById('amount_display');
        const hiddenInput = document.getElementById('amount');

        displayInput.addEventListener('input', function () {
            let value = this.value.replace(/[^\d]/g, '');

            if (!value) {
                hiddenInput.value = '';
                this.value = '';
                return;
            }

            hiddenInput.value = value;

            this.value = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(value);
        });
    </script>

</body>
</html>
