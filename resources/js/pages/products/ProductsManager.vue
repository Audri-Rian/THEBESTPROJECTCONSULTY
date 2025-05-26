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
import { Head } from '@inertiajs/vue3';

import Notification from '@/components/ui/notification/Notification.vue';
import ConfirmationModal from '@/components/ui/confirmation-modal/ConfirmationModal.vue';

const products = ref<any[]>([]);
const suppliers = ref<any[]>([]);
const filter = reactive({
    search: '',
});
const modalRef = ref<InstanceType<typeof Modal> | null>(null);
const editModalRef = ref<InstanceType<typeof Modal> | null>(null);
const notification = ref<InstanceType<typeof Notification> | null>(null);
const confirmDeleteModal = ref<InstanceType<typeof ConfirmationModal> | null>(null);
const confirmCreateModal = ref<InstanceType<typeof ConfirmationModal> | null>(null);
const confirmUpdateModal = ref<InstanceType<typeof ConfirmationModal> | null>(null);

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
const productToDelete = ref<{ id: string, name: string } | null>(null);
const customQuantityToAdd = ref('');
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Dashboard', href: '/dashboard' },
    { title: 'Products', href: '/products' },
];

const showNotification = (message: string, type: 'success' | 'error' | 'warning' | 'info' = 'success', duration: number = 3000) => {
    notification.value?.showNotification(message, type, duration);
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
    productToDelete.value = { id: product.id, name: product.name };
    confirmDeleteModal.value?.openModal({
        title: 'Confirm Delete',
        message: `Are you sure you want to delete this product? All data associated with "${product.name}" will be permanently removed.`,
        type: 'error',
        confirmText: 'Delete',
        cancelText: 'Cancel'
    });
};

const openCreateConfirmModal = () => {
    confirmCreateModal.value?.openModal({
        title: 'Confirm Product Creation',
        message: 'Are you sure you want to create this product?',
        type: 'info',
        confirmText: 'Create',
        cancelText: 'Cancel'
    });
};

const openUpdateConfirmModal = () => {
    if (Number(selectedProduct.quantity) < Number(selectedProduct.originalQuantity)) {
        showNotification('Cannot reduce stock quantity. Only additions are allowed.', 'error');
        selectedProduct.quantity = selectedProduct.originalQuantity;
        return;
    }
    confirmUpdateModal.value?.openModal({
        title: 'Confirm Product Update',
        message: `Are you sure you want to update the product "${selectedProduct.name}"?`,
        type: 'warning',
        confirmText: 'Update',
        cancelText: 'Cancel'
    });
};

const fetchProducts = async () => {
    try {
        const response = await axios.get('/products/getall');
        products.value = response.data;
    } catch (error) {
        console.error('Error fetching products:', error);
        showNotification('Error fetching products. Please try again.', 'error');
    }
};

const fetchSuppliers = async () => {
    try {
        const response = await axios.get('/suppliers/getall');
        suppliers.value = response.data;
    } catch (error) {
        console.error('Error fetching suppliers:', error);
        showNotification('Error fetching suppliers. Please try again.', 'error');
    }
};

const submitSearch = async () => {
    try {
        const response = await axios.post('/products/search', {
            query: filter.search
        });
        products.value = response.data.products;
    } catch (error) {
        console.error('Error searching products:', error);
        showNotification('Error searching products. Please try again.', 'error');
        products.value = [];
    }
};


const submitFormCreate = async () => {
    try {
        const response = await axios.post('/products/store', formData);
        if (response.data.product) {
            products.value.push(response.data.product);
            showNotification(response.data.message || 'Product created successfully!');
        } else if (response.data.status == false) {
            showNotification(response.data.message, 'error');
        }
        modalRef.value?.closeModal();
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
        productToDelete.value = null;
    } catch (error) {
        console.error('Error deleting product:', error);
        showNotification('Error deleting product. Please try again.', 'error');
    }
};

// Handlers para os modais de confirmação
const handleConfirmDelete = () => {
    deleteProduct();
};

const handleConfirmCreate = () => {
    submitFormCreate();
};

const handleConfirmUpdate = () => {
    submitFormUpdate();
};

onMounted(async () => {
    try {
        await Promise.all([
            fetchProducts(),
            fetchSuppliers()
        ]);
    } catch (error) {
        console.error('Error loading initial data:', error);
        showNotification('Error loading initial data. Please refresh the page.', 'error');
    }
});
</script>
<template>

    <Head title="Products Manager" />

    <!-- Componente de Notificação -->
    <Notification ref="notification" />

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
                    <form @submit.prevent="submitSearch" class="flex flex-col sm:flex-row items-center gap-3">
                        <Input v-model="filter.search" type="text" placeholder="Search for products"
                            class="w-full sm:flex-grow rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                        <Button type="submit" variant="default" class="w-full sm:w-auto">
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

        <!-- Modais de confirmação usando o novo componente -->
        <ConfirmationModal ref="confirmCreateModal" @confirm="handleConfirmCreate" @cancel="modalRef?.closeModal()" />

        <ConfirmationModal ref="confirmUpdateModal" @confirm="handleConfirmUpdate" @cancel="() => { }" />

        <ConfirmationModal ref="confirmDeleteModal" @confirm="handleConfirmDelete"
            @cancel="() => { productToDelete = null }" />
    </AppLayout>
</template>