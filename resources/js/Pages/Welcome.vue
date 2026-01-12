<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Welcome" />
    <div class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex p-6 lg:p-8 items-center lg:justify-center min-h-screen flex-col">
        <header class="w-full lg:max-w-4xl max-w-[335px] text-sm mb-6 not-has-[nav]:hidden">
            <nav v-if="canLogin" class="flex items-center justify-end gap-4">
                <Link
                    v-if="$page.props.auth.user"
                    href="/dashboard"
                    class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                >
                    Dashboard
                </Link>

                <template v-else>
                    <Link
                        :href="route('login')"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] text-[#1b1b18] border border-transparent hover:border-[#19140035] dark:hover:border-[#3E3E3A] rounded-sm text-sm leading-normal"
                    >
                        Log in
                    </Link>

                    <Link
                        v-if="canRegister"
                        :href="route('register')"
                        class="inline-block px-5 py-1.5 dark:text-[#EDEDEC] border-[#19140035] hover:border-[#1915014a] border text-[#1b1b18] dark:border-[#3E3E3A] dark:hover:border-[#62605b] rounded-sm text-sm leading-normal"
                    >
                        Register
                    </Link>
                </template>
            </nav>
        </header>
        
        <main class="flex items-center justify-center w-full transition-opacity opacity-100 duration-750 lg:grow">
             <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">Welcome to Aer Test App</h1>
                <p class="mb-4">Converted to Inertia + Vue 3</p>
                <div class="flex gap-4 justify-center">
                    <Link href="/checkout" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Go to Checkout</Link>
                    <Link href="/payments/view" class="bg-gray-600 text-white px-4 py-2 rounded hover:bg-gray-700">View Payments</Link>
                </div>
             </div>
        </main>
    </div>
</template>
