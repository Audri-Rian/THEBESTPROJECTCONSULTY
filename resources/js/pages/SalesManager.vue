<script setup lang="ts">
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Title from '@/components/ui/title/Title.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import type { BreadcrumbItem } from '@/types';

const products = ref([
    { product_id: '', quantity: '' }
]);

const form = useForm({
    products: products.value,
});

const addProduct = () => {
    products.value.push({ product_id: '', quantity: '' });
};

const submitSale = () => {
    form.products = products.value;

    form.post(route('sales.store'), {
        onSuccess: () => {
            products.value = [{ product_id: '', quantity: '' }];
            form.reset();
        },
    });
};
</script>

<template>
    <Head title="Create Sale" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] p-6 flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b pb-4">
                        <Title title="New Sale" :level="1" />
                        <Button :variant="'default'" @click="addProduct">+ Add Product</Button>
                    </div>

                    <form @submit.prevent="submitSale" class="space-y-6">
                        <div v-for="(product, index) in products" :key="index" class="space-y-2 border p-4 rounded">
                            <div>
                                <Label :for="`product-id-${index}`">Product ID</Label>
                                <Input :id="`product-id-${index}`" v-model="product.product_id" type="number" placeholder="1" />
                            </div>
                            <div>
                                <Label :for="`product-qty-${index}`">Quantity</Label>
                                <Input :id="`product-qty-${index}`" v-model="product.quantity" type="number" placeholder="2" />
                            </div>
                        </div>

                        <div class="pt-4 border-t flex justify-end">
                            <Button type="submit" :variant="'default'" :disabled="form.processing">
                                {{ form.processing ? 'Processing...' : 'Submit Sale' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
