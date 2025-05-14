<script lang="ts" setup>
import { ref, onMounted } from 'vue'; // Import ref for reactive state
import AppLayout from '@/layouts/AppLayout.vue';
import Table from '@/components/ui/table/Table.vue';
import { Head } from '@inertiajs/vue3';

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
                <div>
                    <Table
                        :headers="['Nome', 'Quantidade', 'Preço', 'Saida']"
                        :data="productsHistory
                        .map((product) => ({
                            Nome: product.product,
                            Quantidade: product.quantity,
                            Preço: `R$ ${product.total}`,
                            Saida: product.date,
                        }))"/>
                </div>
            </div>
        </div>
    </AppLayout>
</template>