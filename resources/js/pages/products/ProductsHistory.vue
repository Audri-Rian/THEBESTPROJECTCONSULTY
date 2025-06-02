<script lang="ts" setup>
import { ref, onMounted } from 'vue'; // Import ref for reactive state
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import { Package, DollarSign, Calendar, Tag, ArrowUpRight, ArrowDownRight } from 'lucide-vue-next';

const breadcrumbs = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Produtos', href: '/products' },
    { title: 'Histórico', href: '/products/history' },
];

// Reactive state for products history
const productsHistory = ref<any[]>([]);

const fetchProductsHistory = async () => {
    try {
        const response = await fetch('/products/history/get');
        if (!response.ok) throw new Error('Network response was not ok');
        return await response.json();
    } catch (error) {
        console.error('Error fetching products history:', error);
        return []; // Return empty array on error
    }
};

onMounted(async () => {
    // Await the fetch and update reactive state
    productsHistory.value = await fetchProductsHistory();
    console.log('Products history:', productsHistory.value);
});

const formatCurrency = (value: string | number) => {
    const numValue = typeof value === 'string' ? parseFloat(value) : value;
    if (isNaN(numValue)) return 'R$ 0,00';
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(numValue);
};

const formatDate = (dateString: string) => {
    try {
        const date = new Date(dateString);
        if (isNaN(date.getTime())) return dateString;
        return date.toLocaleString('pt-BR', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    } catch (error) {
        return dateString;
    }
};

const formatNumber = (value: string | number) => {
    const numValue = typeof value === 'string' ? parseFloat(value) : value;
    if (isNaN(numValue)) return '0';
    return Math.abs(numValue).toString();
};
</script>

<template>
    <Head title="Histórico de Produtos" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border md:min-h-min">
                <div class="space-y-4">
                    <div class="border-b mb-4 p-2">
                        <h1 class="text-2xl font-bold">Histórico de Produtos</h1>
                        <p class="text-gray-500">Aqui você pode visualizar o histórico de produtos.</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 gap-6">
                    <Card v-for="history in productsHistory" :key="history.id" 
                        class="flex flex-col justify-between hover:shadow-lg transition-shadow duration-200 bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                        <CardHeader>
                            <div class="flex items-center justify-between">
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <CardTitle class="truncate text-xl text-gray-900 dark:text-gray-100">{{ history.product }}</CardTitle>
                                        <span :class="[
                                            'px-2 py-1 rounded-full text-xs font-medium',
                                            history.type === 'entrada' 
                                                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400' 
                                                : 'bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400'
                                        ]">
                                            {{ history.type === 'entrada' ? 'Entrada' : 'Saída' }}
                                        </span>
                                    </div>
                                    <CardDescription class="mt-1 text-gray-500 dark:text-gray-400">ID: {{ history.id }}</CardDescription>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span :class="[
                                        'text-lg font-semibold',
                                        history.type === 'entrada' 
                                            ? 'text-emerald-600 dark:text-emerald-400' 
                                            : 'text-rose-600 dark:text-rose-400'
                                    ]">
                                        {{ history.type === 'entrada' ? '+' : '-' }}{{ formatNumber(history.quantity) }}
                                    </span>
                                    <component :is="history.type === 'entrada' ? ArrowUpRight : ArrowDownRight" 
                                        :class="[
                                            'h-5 w-5',
                                            history.type === 'entrada' 
                                                ? 'text-emerald-600 dark:text-emerald-400' 
                                                : 'text-rose-600 dark:text-rose-400'
                                        ]" />
                                </div>
                            </div>
                        </CardHeader>
                        <CardContent>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                <div class="flex items-center gap-2 p-3 bg-gray-50/50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <Package class="h-5 w-5 text-gray-600 dark:text-gray-300" />
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Quantidade</p>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ formatNumber(history.quantity) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 p-3 bg-gray-50/50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <DollarSign class="h-5 w-5 text-gray-600 dark:text-gray-300" />
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Preço</p>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ formatCurrency(history.price) }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 p-3 bg-gray-50/50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <Tag class="h-5 w-5 text-gray-600 dark:text-gray-300" />
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Tipo</p>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100 capitalize">{{ history.type }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 p-3 bg-gray-50/50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                    <Calendar class="h-5 w-5 text-gray-600 dark:text-gray-300" />
                                    <div>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">Data</p>
                                        <p class="font-semibold text-gray-900 dark:text-gray-100">{{ formatDate(history.date) }}</p>
                                    </div>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>
            </div>
        </div>
    </AppLayout>
</template>