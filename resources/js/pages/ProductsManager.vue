<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Modal from '@/components/ui/modal/Modal.vue';
import Table from '@/components/ui/table/Table.vue';
import Title from '@/components/ui/title/Title.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';
import { ref, reactive, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3'
const products = ref<any[]>([]);
const suppliers = ref<any[]>([]);
const filter = reactive({
    search: '',
});
const modalRef = ref<InstanceType<typeof Modal> | null>(null);
const editModalRef = ref<InstanceType<typeof Modal> | null>(null);
const deleteConfirmModalRef = ref<InstanceType<typeof Modal> | null>(null);
const createConfirmModalRef = ref<InstanceType<typeof Modal> | null>(null);
const updateConfirmModalRef = ref<InstanceType<typeof Modal> | null>(null);
const formData = reactive({
    name: '',
    description: '',
    price: '',
    price_for_sale: '',
    quantity: '',
    supplier_id: '',
});
const selectedProduct = reactive({
    id: '',
    name: '',
    description: '',
    price: '',
    price_for_sale: '',
    quantity: '',
    supplier_id: '',
    originalQuantity: '',
});
const productToDelete = ref<{ id: string } | null>(null);
const customQuantityToAdd = ref('');
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Products', href: '/products' },
];
const notification = reactive({
    show: false,
    message: '',
    type: 'success',
});
const showNotification = (message: string, type: string = 'success') => {
    notification.message = message;
    notification.type = type;
    notification.show = true;
    setTimeout(() => {
        notification.show = false;
    }, 3000);
};
const addToQuantity = (amount: number) => {
    const currentQuantity = Number(selectedProduct.quantity) || 0;
    selectedProduct.quantity = String(currentQuantity + amount);
};
const addCustomQuantity = () => {
    if (customQuantityToAdd.value && !isNaN(Number(customQuantityToAdd.value))) {
        addToQuantity(Number(customQuantityToAdd.value));
        customQuantityToAdd.value = '';
    }
};
const openAddProductModal = () => modalRef.value?.openModal();
const openEditProductModal = (product: any) => {
    Object.assign(selectedProduct, {
        id: product.id,
        name: product.name,
        description: product.description || '',
        price: product.price,
        price_for_sale: product.price_for_sale || '',
        quantity: product.quantity,
        supplier_id: product.supplier?.id || '',
        originalQuantity: product.quantity,
    });
    customQuantityToAdd.value = '';
    editModalRef.value?.openModal();
};
const openDeleteConfirmModal = (product: any) => {
    productToDelete.value = product;
    deleteConfirmModalRef.value?.openModal();
};
const openCreateConfirmModal = () => {
    createConfirmModalRef.value?.openModal();
};
const openUpdateConfirmModal = () => {
    if (Number(selectedProduct.quantity) < Number(selectedProduct.originalQuantity)) {
        showNotification('Cannot reduce stock quantity. Only additions are allowed.', 'error');
        selectedProduct.quantity = selectedProduct.originalQuantity;
        return;
    }
    updateConfirmModalRef.value?.openModal();
};
const fetchProducts = async () => {
    try {
        const response = await axios.get('/products/getall');
        products.value = response.data;
    } catch (error) {
        console.error('Error fetching products:', error);
    }
};
const fetchSuppliers = async () => {
    try {
        const response = await axios.get('/suppliers/getall');
        suppliers.value = response.data;
    } catch (error) {
        console.error('Error fetching suppliers:', error);
    }
};
const submitSearch = async () => {
    try {
        const response = await axios.get('/products/search', {
            params: { query: filter.search }
        });
        products.value = response.data.products;
    } catch (error) {
        console.error('Error searching products:', error);
        products.value = [];
    }
};
const submitFormCreate = async () => {
    try {
        const response = await axios.post('/products/store', formData);
        if (response.data.product) {
            products.value.push(response.data.product);
            showNotification('Product created successfully!');
        }
        modalRef.value?.closeModal();
        createConfirmModalRef.value?.closeModal();
        Object.assign(formData, {
            name: '',
            description: '',
            price: '',
            price_for_sale: '',
            quantity: '',
            supplier_id: '',
        });
    } catch (error) {
        console.error('Error creating product:', error);
        showNotification('Error creating product. Please try again.', 'error');
    }
};
const submitFormUpdate = async () => {
    try {
        const payload = {
            name: selectedProduct.name ?? null,
            description: selectedProduct.description ?? null,
            price: selectedProduct.price ?? null,
            price_for_sale: selectedProduct.price_for_sale ?? null,
            quantity: selectedProduct.quantity ?? null,
            supplier_id: selectedProduct.supplier_id ?? null,
        };
        const response = await axios.put(`/products/${selectedProduct.id}`, payload);
        if (response.data.product) {
            const index = products.value.findIndex(p => p.id === selectedProduct.id);
            if (index !== -1) {
                products.value[index] = response.data.product;
            }
            showNotification('Product updated successfully!');
        } else {
            await fetchProducts();
            showNotification('Product updated successfully!');
        }
        editModalRef.value?.closeModal();
        updateConfirmModalRef.value?.closeModal();
    } catch (error) {
        console.error('Error updating product:', error);
        showNotification('Error updating product. Please try again.', 'error');
    }
};
const deleteProduct = async () => {
    if (!productToDelete.value) return;
    try {
        const response = await axios.delete(`/products/${productToDelete.value.id}`);
        if (response.status === 200) {
            products.value = products.value.filter(product => product.id !== productToDelete.value?.id);
            showNotification(response.data.message || 'Product deleted successfully!');
        }
        deleteConfirmModalRef.value?.closeModal();
        productToDelete.value = null;
    } catch (error) {
        console.error('Error deleting product:', error);
        showNotification('Error deleting product. Please try again.', 'error');
    }
};
onMounted(async () => {
    try {
        await Promise.all([
            fetchProducts(),
            fetchSuppliers()
        ]);
    } catch (error) {
        console.error('Error loading initial data:', error);
    }
});
</script>
<template>

    <Head title="Products Manager" />
    <div v-if="notification.show" class="fixed top-4 right-4 z-50 p-4 rounded-md shadow-md transition-all duration-300"
        :class="{
            'bg-green-100 border-l-4 border-green-500 text-green-700': notification.type === 'success',
            'bg-red-100 border-l-4 border-red-500 text-red-700': notification.type === 'error',
            'bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700': notification.type === 'warning',
            'bg-blue-100 border-l-4 border-blue-500 text-blue-700': notification.type === 'info'
        }">
        {{ notification.message }}
    </div>
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div
                class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border md:min-h-min">
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
                    <form @submit.prevent="submitSearch" class="mb-4">
                        <input v-model="filter.search" type="text" placeholder="Search for products"
                            class="text- w-full rounded-md border border-gray-300 p-2 text-gray-900 focus:outline-none focus:ring-2 focus:ring-blue-400 md:w-1/3" />
                        <button type="submit"
                            class="text-blue rounded-xl ml-5 bg-blue-600 px-4 py-2 text-sm font-medium shadow transition-colors hover:bg-blue-700">
                            Search
                        </button>
                    </form>
                    <Table
                        :headers="['ID', 'Name', 'Description', 'Price', 'Price For Sale', 'Quantity', 'Supplier', 'Action']"
                        :data="products
                            .filter(product => product.status_id === 1)
                            .map((product) => ({
                                ID: product.id,
                                Name: product.name,
                                Description: product.description ? product.description : 'N/A',
                                Price: product.price ? `R$ ${product.price}` : 'N/A',
                                'Price For Sale': product.price_for_sale ? `R$ ${product.price_for_sale}` : 'N/A',
                                Quantity: product.quantity ? product.quantity : 'N/A',
                                Supplier: product.supplier ? product.supplier.name : 'N/A',
                                product: product,
                            }))">
                        <template #action="{ row }">
                            <div class="flex gap-2">
                                <Button @click="openEditProductModal(row.product)" icon="Pencil">Edit</Button>
                                <Button @click="openDeleteConfirmModal(row.product)" icon="Trash"
                                    variant="destructive">Delete</Button>
                            </div>
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
                    <form @submit.prevent="openCreateConfirmModal" class="space-y-4">
                        <div>
                            <Label for="name">Name</Label>
                            <Input id="name" v-model="formData.name" type="text" required autofocus :tabindex="1"
                                placeholder="Ring of Power" />
                        </div>
                        <div>
                            <Label for="description">Description</Label>
                            <Input id="description" v-model="formData.description" type="text" autofocus :tabindex="1"
                                placeholder="It is one item to give more power" />
                        </div>
                        <div>
                            <Label for="price">Price</Label>
                            <Input id="price" v-model="formData.price" type="number" autofocus :tabindex="1"
                                placeholder="R$ 15" />
                        </div>
                        <div>
                            <Label for="price_for_sale">Price For Sale</Label>
                            <Input id="price_for_sale" v-model="formData.price_for_sale" type="number" autofocus
                                :tabindex="1" placeholder="R$ 20" />
                        </div>
                        <div>
                            <Label for="quantity">Quantity in Stock</Label>
                            <Input id="quantity" v-model="formData.quantity" type="number" autofocus :tabindex="1"
                                placeholder="120" />
                        </div>
                        <div>
                            <Label for="supplier">Supplier</Label>
                            <select id="supplier" v-model="formData.supplier_id"
                                class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background">
                                <option value="">Select a supplier</option>
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                    {{ supplier.name }}
                                </option>
                            </select>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                            <Button type="button" @click="openCreateConfirmModal" variant="default">Create
                                Product</Button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>
        <Modal ref="createConfirmModalRef">
            <template #title>
                <Title title="Confirm Product Creation" :level="1" />
            </template>
            <template #body>
                <div class="space-y-4">
                    <div class="rounded-md bg-blue-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                <p class="text-sm text-blue-700">
                                    Are you sure you want to create this product?
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                        <Button type="button" @click="createConfirmModalRef?.closeModal()"
                            variant="outline">Cancel</Button>
                        <Button type="button" @click="submitFormCreate" variant="default">Confirm Creation</Button>
                    </div>
                </div>
            </template>
        </Modal>
        <Modal ref="editModalRef">
            <template #title>
                <Title title="Edit Product" :level="1" />
            </template>
            <template #body>
                <form @submit.prevent="openUpdateConfirmModal" class="space-y-4">
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
                        <Label for="edit-price_for_sale">Price For Sale</Label>
                        <Input v-model="selectedProduct.price_for_sale" id="edit-price_for_sale" type="number" />
                    </div>
                    <div>
                        <Label for="edit-quantity">Quantity in Stock</Label>
                        <div class="flex items-center">
                            <Input v-model="selectedProduct.quantity" id="edit-quantity" type="number" readonly />
                            <div class="ml-2 text-sm text-gray-500">
                                (Original: {{ selectedProduct.originalQuantity }})
                            </div>
                        </div>
                        <div class="mt-2 flex flex-wrap gap-2">
                            <Button type="button" @click="addToQuantity(1)" variant="outline" size="lg">+1</Button>
                            <Button type="button" @click="addToQuantity(5)" variant="outline" size="lg">+5</Button>
                            <Button type="button" @click="addToQuantity(10)" variant="outline" size="lg">+10</Button>
                            <Button type="button" @click="addToQuantity(100)" variant="outline" size="lg">+100</Button>
                            <div class="flex items-center gap-2">
                                <Input v-model="customQuantityToAdd" type="number" placeholder="Custom amount"
                                    class="p-2" min="1" />
                                <Button type="button" @click="addCustomQuantity" variant="outline" size="lg">
                                    Add Custom
                                </Button>
                            </div>
                        </div>
                        <div class="mt-2 rounded-md bg-blue-50 p-2 text-sm text-blue-800">
                            Note: You can only increase stock quantity, not decrease it.
                        </div>
                    </div>
                    <div>
                        <Label for="edit-supplier">Supplier</Label>
                        <select v-model="selectedProduct.supplier_id" id="edit-supplier"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background">
                            <option value="">No supplier</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                {{ supplier.name }}
                            </option>
                        </select>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                        <Button type="button" @click="openUpdateConfirmModal" :variant="'default'">Update
                            Product</Button>
                    </div>
                </form>
            </template>
        </Modal>
        <Modal ref="updateConfirmModalRef">
            <template #title>
                <Title title="Confirm Product Update" :level="1" />
            </template>
            <template #body>
                <div class="space-y-4">
                    <div class="rounded-md bg-yellow-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3 flex-1 md:flex md:justify-between">
                                <p class="text-sm text-yellow-700">
                                    Are you sure you want to update this product?
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                        <Button type="button" @click="updateConfirmModalRef?.closeModal()"
                            variant="outline">Cancel</Button>
                        <Button type="button" @click="submitFormUpdate" variant="default">Confirm Update</Button>
                    </div>
                </div>
            </template>
        </Modal>
        <Modal ref="deleteConfirmModalRef">
            <template #title>
                <Title title="Confirm Delete" :level="1" />
            </template>
            <template #body>
                <div class="space-y-4">
                    <div class="rounded-md bg-red-50 p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800">
                                    Warning! This action cannot be undone.
                                </h3>
                                <div class="mt-2 text-sm text-red-700">
                                    <p>
                                        Are you sure you want to delete this product? All data associated with it will
                                        be permanently removed.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                        <Button type="button" @click="deleteConfirmModalRef?.closeModal()"
                            variant="outline">Cancel</Button>
                        <Button type="button" @click="deleteProduct" variant="destructive">Confirm Delete</Button>
                    </div>
                </div>
            </template>
        </Modal>
    </AppLayout>
</template>