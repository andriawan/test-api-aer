<script setup>
import { useForm, Head, Link } from '@inertiajs/vue3';
import { watch } from 'vue';

const form = useForm({
    amount: '',
    amount_display: '',
});

watch(() => form.amount_display, (newValue) => {
    let value = newValue.replace(/[^\d]/g, '');
    
    if (!value) {
        form.amount = '';
        form.amount_display = '';
        return;
    }

    form.amount = value;
    // Format display
    form.amount_display = new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
});

const submit = () => {
    form.post('/process-payment', {
        onSuccess: () => form.reset(),
    });
};
</script>

<template>
    <Head title="Pembayaran" />

    <div class="bg-gray-100 min-h-screen flex items-center justify-center p-4">
        <div class="bg-white p-6 rounded-xl shadow-md w-full max-w-md space-y-4">
            
            <div v-if="Object.keys(form.errors).length > 0" class="mb-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-lg">
                <ul class="list-disc list-inside">
                    <li v-for="error in form.errors" :key="error">{{ error }}</li>
                </ul>
            </div>

            <h1 class="text-xl font-semibold text-gray-800">
                Form Pembayaran
            </h1>

            <form @submit.prevent="submit">
                <div>
                    <label class="block text-sm font-medium text-gray-700">
                        Nominal Pembayaran
                    </label>

                    <input 
                        v-model="form.amount_display"
                        required 
                        type="text" 
                        inputmode="numeric" 
                        placeholder="Rp 0"
                        class="mt-1 block w-full rounded-lg outline-none border border-gray-300 p-2 focus:border-indigo-500 focus:ring-indigo-500"
                        autocomplete="off"
                    >
                    
                    <!-- Hidden input not strictly needed as we send form.amount, but kept for logic parity if needed -->
                </div>

                <div class="mt-6">
                    <button 
                        type="submit" 
                        :disabled="form.processing"
                        class="w-full bg-indigo-600 hover:bg-indigo-700 text-white font-medium py-2.5 rounded-lg transition disabled:opacity-50"
                    >
                        Proses Pembayaran
                    </button>
                </div>
            </form>

            <div class="pt-4">
                <Link href="/payments/view" class="text-sm text-indigo-600 hover:underline">
                    ← Lihat Daftar Transaksi
                </Link>
            </div>
        </div>
    </div>
</template>
