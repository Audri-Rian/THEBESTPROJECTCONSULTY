<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Modal from '@/components/ui/modal/Modal.vue';
import Table from '@/components/ui/table/Table.vue';
import Title from '@/components/ui/title/Title.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    name: '',
    description: '',
    price: '',
    quantity: '',
    supplier_id: '',
});

const selectedProduct = ref({
    id: '',
    name: '',
    description: '',
    price: '',
    quantity: '',
    supplier_id: '',
});

const submitFormCreate = () => {
    form.post(route('products.store'), {
        onSuccess: () => {
            form.reset();
            modalRef.value?.closeModal();
        },
    });
};

const submitFormUpdate = () => {
    form.name = selectedProduct.value.name;
    form.description = selectedProduct.value.description;
    form.price = selectedProduct.value.price;
    form.quantity = selectedProduct.value.quantity;
    form.supplier_id = selectedProduct.value.supplier_id;

    form.put(route('products.update', selectedProduct.value.id), {
        onSuccess: () => {
            editModalRef.value?.closeModal();
        },
    });
};


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Products',
        href: '/products',
    },
];

const modalRef = ref<InstanceType<typeof Modal> | null>(null);
const editModalRef = ref<InstanceType<typeof Modal> | null>(null);

const openAddProductModal = () => {
    if (modalRef.value != null) {
        modalRef.value.openModal();
    }
};

const openEditProductModal = (product: any) => {
    selectedProduct.value = {
        id: product.id,
        name: product.name,
        description: product.description || '',
        price: product.price,
        quantity: product.quantity,
        supplier_id: product.supplier?.id || '',
    };

    editModalRef.value?.openModal();
};

defineProps<{
    products: Array<{
        id: number;
        name: string;
        description: string;
        price: number;
        quantity: number;
        supplier: { name: string };
    }>;
    suppliers?: Array<{ id: number; name: string }>;
}>();
</script>

<template>
    <Head title="Products Manager" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] p-6 flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b">
                        <div class="flex-1">
                            <Title title="Products in System" :level="1" />
                            <Title title="Here you can view the products registered in the database" :level="2" />
                        </div>
                        <div class="flex-shrink-0">
                            <Button :icon="'Pin'" :variant="'default'" @click="openAddProductModal">Add Product</Button>
                        </div>
                    </div>

                    <Table
                        :headers="['ID', 'Name', 'Description', 'Price', 'Quantity', 'Supplier', 'Action']"
                        :data="
                            products.map((product) => ({
                                ID: product.id,
                                Name: product.name,
                                Description: product.description ? product.description : 'N/A',
                                Price: product.price ? `R$ ${product.price}` : 'N/A',
                                Quantity: product.quantity ? product.quantity : 'N/A',
                                Supplier: product.supplier ? product.supplier.name : 'N/A',
                                product: product,
                            }))
                        "
                    >
                        <template #action="{ row }">
                            <Button @click="openEditProductModal(row.product)" icon="Pencil">Edit</Button>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
        <Modal ref="modalRef">
            <template #title>
                <Title title="Add Product" :level="1" />
            </template>
            <template #body>
                <div>
                    <form @submit.prevent="submitFormCreate" class="space-y-4">
                        <div>
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="form.name" type="text" required autofocus :tabindex="1" placeholder="Ring of Power" />
                        </div>

                        <div>
                            <Label for="description">Description</Label>
                            <Input
                                id="description"
                                v-model="form.description"
                                type="text"
                                autofocus
                                :tabindex="1"
                                placeholder="It is one item to give more power"
                            />
                        </div>
                        <div>
                            <Label for="price">Price</Label>
                            <Input id="price" v-model="form.price" type="number" autofocus :tabindex="1" placeholder="R$ 15" />
                        </div>
                        <div>
                            <Label for="quantity">Quantity in Stock</Label>
                            <Input id="quantity" v-model="form.quantity" type="number" autofocus :tabindex="1" placeholder="120" />
                        </div>
                        <div>
                            <Label for="supplier">Supplier</Label>
                            <select
                                id="supplier"
                                v-model="form.supplier_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background"
                            >
                                <option value="">Select a supplier</option>
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                    {{ supplier.name }}
                                </option>
                            </select>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                            <Button type="submit" variant="default" :disabled="form.processing">
                                {{ form.processing ? 'Saving...' : 'Confirm' }}
                            </Button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>
        <Modal ref="editModalRef">
            <template #title>
                <Title title="Edit Product" :level="1" />
            </template>
            <template #body>
                <form class="space-y-4">
                    <div>
                        <Label for="edit-name">Name</Label>
                        <Input v-model="selectedProduct.name" id="edit-name" type="text" required />
                    </div>
                    <div>
                        <Label for="edit-description">Description</Label>
                        <Input v-model="selectedProduct.description" id="edit-description" type="text" />
                    </div>
                    <div>
                        <Label for="edit-price">Price</Label>
                        <Input v-model="selectedProduct.price" id="edit-price" type="number" />
                    </div>
                    <div>
                        <Label for="edit-quantity">Quantity in Stock</Label>
                        <Input v-model="selectedProduct.quantity" id="edit-quantity" type="number" />
                    </div>
                    <div>
                        <Label for="edit-supplier">Supplier</Label>
                        <select v-model="selectedProduct.supplier_id" id="edit-supplier" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background">
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                {{ supplier.name }}
                            </option>
                        </select>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                        <Button @click="submitFormUpdate" type="submit" :variant="'default'">Update Product</Button>
                    </div>
                </form>
            </template>
        </Modal>
    </AppLayout>
</template>
