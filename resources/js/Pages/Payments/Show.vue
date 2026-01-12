<script setup>
import { Head, Link } from '@inertiajs/vue3';
import QrcodeVue from 'qrcode.vue';

const props = defineProps({
    transaction: Object,
});

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Detail Transaksi" />

    <div class="bg-gray-100 min-h-screen p-6">
        <div class="max-w-3xl mx-auto bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-4 border-b flex items-center justify-between">
                <h1 class="text-lg font-semibold text-gray-800">
                    Detail Transaksi
                </h1>

                <Link href="/payments/view" class="text-sm text-indigo-600 hover:underline">
                    ← Kembali
                </Link>
            </div>

            <div class="p-6">
                <table class="w-full text-sm">
                    <tbody class="divide-y">

                        <tr>
                            <th class="py-3 text-left font-medium text-gray-600 w-1/3">Ref ID</th>
                            <td class="py-3 font-mono text-xs break-all">
                                {{ transaction.ref_id }}
                            </td>
                        </tr>

                        <tr>
                            <th class="py-3 text-left font-medium text-gray-600">Transaction ID</th>
                            <td class="py-3 font-mono text-xs break-all">
                                {{ transaction.trx_id }}
                            </td>
                        </tr>

                        <tr>
                            <th class="py-3 text-left font-medium text-gray-600">QR Code</th>
                            <td class="py-3">
                                <QrcodeVue :value="transaction.qr_code" :size="300" level="L" />
                            </td>
                        </tr>

                        <tr>
                            <th class="py-3 text-left font-medium text-gray-600">Amount</th>
                            <td class="py-3 font-semibold">
                                {{ formatCurrency(transaction.amount) }}
                            </td>
                        </tr>

                        <tr>
                            <th class="py-3 text-left font-medium text-gray-600">Status</th>
                            <td class="py-3 capitalize">
                                <span v-if="transaction.transaction_status === '00'" class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">
                                    Selesai
                                </span>
                                <span v-else-if="transaction.transaction_status === 'pending'" class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-medium">
                                    Pending
                                </span>
                                <span v-else class="px-2 py-1 rounded bg-gray-300 text-gray-800 text-xs font-medium">
                                    {{ transaction.transaction_status }} | {{ transaction.transaction_desc }}
                                </span>
                            </td>
                        </tr>

                        <tr>
                            <th class="py-3 text-left font-medium text-gray-600">Brand </th>
                            <td class="py-3">
                                {{ transaction.brand_name || "-" }}
                            </td>
                        </tr>

                        <tr>
                            <th class="py-3 text-left font-medium text-gray-600">Buyer </th>
                            <td class="py-3">
                                {{ transaction.buyer_ref || "-" }}
                            </td>
                        </tr>

                        <tr>
                            <th class="py-3 text-left font-medium text-gray-600">Expired At</th>
                            <td class="py-3">
                                {{ formatDate(transaction.expired_at) }}
                            </td>
                        </tr>

                        <tr>
                            <th class="py-3 text-left font-medium text-gray-600">Created At</th>
                            <td class="py-3">
                                {{ formatDate(transaction.created_at) }}
                            </td>
                        </tr>

                    </tbody>
                </table>
                
                <div v-if="transaction.transaction_status !== '00'" class="mt-6 text-center">
                    <Link :href="`/payments/view/${transaction.ref_id}`" class="inline-block bg-indigo-600 text-white px-4 py-2 rounded-md hover:bg-indigo-700">
                        Check Status Pembayaran
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>
