<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, watch, onMounted } from 'vue';
import QrcodeVue from 'qrcode.vue';
import { debounce } from 'lodash'; 

const props = defineProps({
    transactions: Object,
});

// Parse initial query params
const params = new URLSearchParams(window.location.search);
const initialFilterBy = params.has('amount') ? 'amount' : (params.has('trx_id') ? 'trx_id' : 'ref_id');
const initialAmountVal = params.get('amount') || '';
let initialOperator = 'gte';
let initialAmountNum = '';

if (initialAmountVal && initialAmountVal.includes(':')) {
    const parts = initialAmountVal.split(':');
    initialOperator = parts[0];
    initialAmountNum = parts[1];
}

const filterBy = ref(initialFilterBy);
const amountOperator = ref(initialOperator);
const searchInput = ref(params.get('ref_id') || params.get('trx_id') || initialAmountNum || '');

const updateSearch = () => {
    const query = {};
    if (searchInput.value) {
        if (filterBy.value === 'amount') {
            query.amount = `${amountOperator.value}:${searchInput.value}`;
        } else {
            query[filterBy.value] = searchInput.value;
        }
    }
    
    router.get(window.location.pathname, query, {
        preserveState: true,
        replace: true,
    });
};

// Debounce search
const debouncedSearch = debounce(updateSearch, 500);

watch([filterBy, amountOperator], () => {
    // If filter changes, maybe clear input or keep it? Blade kept it.
    // Changing filter type immediately expects search adjustment
    if(searchInput.value) updateSearch();
});

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium', timeStyle: 'short' }).format(date);
};

const formatCurrency = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Daftar Transaksi" />

    <div class="bg-gray-100 min-h-screen p-6">
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="p-4 border-b flex flex-col md:flex-row items-center justify-between gap-4">
                <h1 class="text-lg font-semibold text-gray-800">
                    Daftar Transaksi
                </h1>
                
                <div class="flex flex-wrap gap-2 justify-end flex-1">
                     <Link href="/payments/view" class="bg-red-500 text-white p-2 px-3 rounded flex items-center">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="m22.122 13.465l1.414 1.414L21.415 17l2.121 2.122l-1.414 1.414L20 18.414l-2.12 2.122l-1.415-1.415l2.121-2.12l-2.121-2.122l1.414-1.414L20 15.586zM3 3h18l-7 9.817V21h-4v-8.183z"/></svg>
                    </Link>

                    <select v-model="filterBy" class="border rounded-lg border-gray-300 outline-none focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">
                        <option value="ref_id">Ref ID</option>
                        <option value="amount">Amount</option>
                        <option value="trx_id">Transaction ID</option>
                    </select>

                    <select v-if="filterBy === 'amount'" v-model="amountOperator" class="border rounded-lg border-gray-300 outline-none focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2">
                        <option value="lt">kurang dari</option>
                        <option value="lte">kurang dari sama dengan</option>
                        <option value="gt">lebih dari</option>
                        <option value="gte">lebih dari sama dengan</option>
                    </select>

                    <input 
                        v-model="searchInput" 
                        @input="debouncedSearch"
                        type="text" 
                        :type="filterBy === 'amount' ? 'number' : 'text'"
                        placeholder="Cari..." 
                        class="rounded-lg border-gray-300 border outline-none focus:border-indigo-500 focus:ring-indigo-500 px-3 py-2"
                    >
                    
                    <button @click="updateSearch" class="bg-indigo-600 text-white px-3 py-2 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M15.5 14h-.79l-.28-.27A6.47 6.47 0 0 0 16 9.5A6.5 6.5 0 1 0 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5S14 7.01 14 9.5S11.99 14 9.5 14"/></svg>
                    </button>
                </div>
            </div>
            
            <div class="p-4 border-b flex justify-end">
                <Link href="/checkout" class="text-sm text-indigo-600 hover:underline">
                    + Buat Transaksi Baru
                </Link>
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
                        <tr v-for="trx in transactions.data" :key="trx.id" class="hover:bg-gray-50">
                            <td class="px-4 py-3 text-gray-800">{{ trx.id }}</td>
                            <td class="px-4 py-3 font-mono text-xs break-all">
                                <Link :href="`/payments/view/${trx.ref_id}`" class="text-blue-600 hover:underline">{{ trx.ref_id }}</Link>
                            </td>
                            <td class="px-4 py-3 font-mono text-xs break-all">{{ trx.trx_id }}</td>
                            <td class="px-4 py-3">
                                <QrcodeVue :value="trx.qr_code" :size="80" level="L" />
                            </td>
                            <td class="px-4 py-3 text-right font-semibold">{{ formatCurrency(trx.amount) }}</td>
                            <td class="px-4 py-3">
                                <span v-if="trx.transaction_status === '00'" class="px-2 py-1 rounded bg-green-100 text-green-800 text-xs font-medium">Selesai</span>
                                <span v-else-if="trx.transaction_status === 'pending'" class="px-2 py-1 rounded bg-yellow-100 text-yellow-800 text-xs font-medium">Pending</span>
                                <span v-else class="px-2 py-1 rounded bg-gray-300 text-gray-800 text-xs font-medium">
                                    {{ trx.transaction_status }} | {{ trx.transaction_desc }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ formatDate(trx.expired_at) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="px-4 py-3 border-t flex items-center justify-between text-sm">
                <div class="text-gray-600">
                    Page {{ transactions.current_page }} of {{ transactions.last_page }} ({{ transactions.total }} transaksi)
                </div>
                <div class="flex gap-2">
                    <Link 
                        v-if="transactions.prev_page_url" 
                        :href="transactions.prev_page_url" 
                        class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200"
                    >
                        Previous
                    </Link>
                    <span v-else class="px-3 py-1 rounded bg-gray-200 text-gray-400 cursor-not-allowed">Previous</span>

                    <Link 
                        v-if="transactions.next_page_url" 
                        :href="transactions.next_page_url" 
                        class="px-3 py-1 rounded bg-gray-100 hover:bg-gray-200"
                    >
                        Next
                    </Link>
                    <span v-else class="px-3 py-1 rounded bg-gray-200 text-gray-400 cursor-not-allowed">Next</span>
                </div>
            </div>
        </div>
    </div>
</template>
