<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Modal from '@/components/ui/modal/Modal.vue';
import Title from '@/components/ui/title/Title.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import axios from 'axios';
import { ref, reactive, onMounted } from 'vue';
import { Head } from '@inertiajs/vue3';

import Notification from '@/components/ui/notification/Notification.vue';
import ConfirmationModal from '@/components/ui/confirmation-modal/ConfirmationModal.vue';
import Card from '@/components/ui/card/Card.vue';
import CardHeader from '@/components/ui/card/CardHeader.vue';
import CardTitle from '@/components/ui/card/CardTitle.vue';
import CardDescription from '@/components/ui/card/CardDescription.vue';
import CardContent from '@/components/ui/card/CardContent.vue';
import CardFooter from '@/components/ui/card/CardFooter.vue';
import { DollarSign, ShoppingBag, Package, Truck } from 'lucide-vue-next';

// Add URL reference
const URL = window.URL;

interface Product {
    id: string;
    name: string;
    description: string;
    price: string;
    price_for_sale: string;
    quantity: string;
    supplier_id: string;
    image_url?: string;
    status_id: number;
    supplier?: {
        id: string;
        name: string;
    };
}

const products = ref<Product[]>([]);
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
    image: null as File | null
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
    image: null as File | null,
    image_url: ''
});
const productToDelete = ref<{ id: string, name: string } | null>(null);
const customQuantityToAdd = ref('');
const breadcrumbs: BreadcrumbItem[] = [
    { title: 'Painel', href: '/dashboard' },
    { title: 'Produtos', href: '/products' },
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
        image: product.image ? new File([product.image], product.name) : null,
        image_url: product.image_url
    });
    customQuantityToAdd.value = '';
    editModalRef.value?.openModal();
};

const openDeleteConfirmModal = (product: any) => {
    productToDelete.value = { id: product.id, name: product.name };
    confirmDeleteModal.value?.openModal({
        title: 'Confirmar Exclusão',
        message: `Tem certeza que deseja excluir este produto? Todos os dados associados a "${product.name}" serão permanentemente removidos.`,
        type: 'error',
        confirmText: 'Excluir',
        cancelText: 'Cancelar'
    });
};

const openCreateConfirmModal = () => {
    confirmCreateModal.value?.openModal({
        title: 'Confirmar Criação do Produto',
        message: 'Tem certeza que deseja criar este produto?',
        type: 'info',
        confirmText: 'Criar',
        cancelText: 'Cancelar'
    });
};

const openUpdateConfirmModal = () => {
    if (Number(selectedProduct.quantity) < Number(selectedProduct.originalQuantity)) {
        showNotification('Não é possível reduzir a quantidade em estoque. Apenas adições são permitidas.', 'error');
        selectedProduct.quantity = selectedProduct.originalQuantity;
        return;
    }
    confirmUpdateModal.value?.openModal({
        title: 'Confirmar Atualização',
        message: `Tem certeza que deseja atualizar o produto "${selectedProduct.name}"?`,
        type: 'warning',
        confirmText: 'Atualizar',
        cancelText: 'Cancelar'
    });
};

const fetchProducts = async () => {
    try {
        const response = await axios.get('/products/getall');
        products.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar produtos:', error);
        showNotification('Erro ao buscar produtos. Por favor, tente novamente.', 'error');
    }
};

const fetchSuppliers = async () => {
    try {
        const response = await axios.get('/suppliers/getall');
        suppliers.value = response.data;
    } catch (error) {
        console.error('Erro ao buscar fornecedores:', error);
        showNotification('Erro ao buscar fornecedores. Por favor, tente novamente.', 'error');
    }
};

const submitSearch = async () => {
    try {
        const response = await axios.post('/products/search', {
            query: filter.search
        });
        products.value = response.data.products;
    } catch (error) {
        console.error('Erro ao pesquisar produtos:', error);
        showNotification('Erro ao pesquisar produtos. Por favor, tente novamente.', 'error');
        products.value = [];
    }
};

const handleImageUpload = async (event: Event, isEdit: boolean = false) => {
    const target = event.target as HTMLInputElement;
    if (target.files && target.files[0]) {
        const file = target.files[0];
        console.log('File selected:', file);
        
        if (isEdit) {
            try {
                const formData = new FormData();
                formData.append('image', file, file.name);
                
                console.log('Uploading image for product:', selectedProduct.id);
                console.log('FormData contents:', {
                    image: file.name,
                    size: file.size,
                    type: file.type
                });

                const response = await axios.post(`/products/${selectedProduct.id}/upload-image`, formData, {
                    headers: {
                        'Content-Type': 'multipart/form-data',
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                console.log('Image upload response:', response.data);
                
                if (response.data.status) {
                    selectedProduct.image_url = response.data.image_url;
                    showNotification('Imagem atualizada com sucesso!');
                } else {
                    showNotification(response.data.message || 'Erro ao atualizar imagem', 'error');
                }
            } catch (error: any) {
                console.error('Erro ao fazer upload da imagem:', error);
                console.error('Error details:', error.response?.data);
                showNotification(
                    error.response?.data?.message || 'Erro ao fazer upload da imagem. Por favor, tente novamente.',
                    'error'
                );
            }
        } else {
            formData.image = file;
            console.log('Create mode - Image set to:', formData.image);
        }
    }
};

const submitFormCreate = async () => {
    try {
        const formDataToSend = new FormData();
        
        // Add all form fields
        Object.keys(formData).forEach(key => {
            const typedKey = key as keyof typeof formData;
            if (formData[typedKey] !== null) {
                if (key === 'image' && formData[typedKey] instanceof File) {
                    formDataToSend.append(key, formData[typedKey] as File);
                } else {
                    formDataToSend.append(key, String(formData[typedKey]));
                }
            }
        });

        console.log('Sending form data:', Object.fromEntries(formDataToSend));

        const response = await axios.post('/products/store', formDataToSend, {
            headers: {
                'Content-Type': 'multipart/form-data'
            }
        });
        
        if (response.data.product) {
            products.value.push(response.data.product);
            showNotification(response.data.message || 'Produto criado com sucesso!');
        } else if (response.data.status === false) {
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
            image: null
        });
    } catch (error: any) {
        console.error('Erro ao criar produto:', error);
        showNotification(
            error.response?.data?.message || 'Erro ao criar produto. Por favor, tente novamente.',
            'error'
        );
    }
};

const submitFormUpdate = async () => {
    try {
        const formDataToSend = new FormData();
        
        // Add all form fields
        Object.keys(selectedProduct).forEach(key => {
            const typedKey = key as keyof typeof selectedProduct;
            if (selectedProduct[typedKey] !== null && 
                key !== 'originalQuantity' && 
                key !== 'image_url' && 
                key !== 'image') {
                formDataToSend.append(key, String(selectedProduct[typedKey]));
            }
        });

        // Handle image separately
        if (selectedProduct.image instanceof File) {
            console.log('Adding image to FormData:', selectedProduct.image);
            formDataToSend.append('image', selectedProduct.image, selectedProduct.image.name);
        }

        // Debug log to see what's being sent
        console.log('FormData contents:');
        for (let pair of formDataToSend.entries()) {
            console.log(pair[0] + ': ' + (pair[1] instanceof File ? `File: ${pair[1].name}` : pair[1]));
        }

        const response = await axios.put(`/products/${selectedProduct.id}`, formDataToSend, {
            headers: {
                'Content-Type': 'multipart/form-data',
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        console.log('Server response:', response.data);
        
        if (response.data.product) {
            const index = products.value.findIndex(p => p.id === selectedProduct.id);
            if (index !== -1) {
                products.value[index] = response.data.product;
            }
            showNotification('Produto atualizado com sucesso!');
        } else {
            await fetchProducts();
            showNotification('Produto atualizado com sucesso!');
        }
        editModalRef.value?.closeModal();
    } catch (error: any) {
        console.error('Erro ao atualizar produto:', error);
        console.error('Error details:', error.response?.data);
        showNotification(
            error.response?.data?.message || 'Erro ao atualizar produto. Por favor, tente novamente.',
            'error'
        );
    }
};

const deleteProduct = async () => {
    if (!productToDelete.value) return;
    try {
        const response = await axios.delete(`/products/${productToDelete.value.id}`);
        if (response.status === 200) {
            products.value = products.value.filter(product => product.id !== productToDelete.value?.id);
            showNotification(response.data.message || 'Produto excluído com sucesso!');
        }
        productToDelete.value = null;
    } catch (error) {
        console.error('Erro ao excluir produto:', error);
        showNotification('Erro ao excluir produto. Por favor, tente novamente.', 'error');
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
    <Head title="Gerenciador de Produtos" />

    <Notification ref="notification" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border md:min-h-min">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b">
                        <div class="flex-1">
                            <Title title="Produtos no Sistema" :level="1" />
                            <Title title="Aqui você pode visualizar os produtos cadastrados na base de dados" :level="2" />
                        </div>
                        <div class="flex-shrink-0">
                            <Button :icon="'Pin'" :variant="'default'" @click="openAddProductModal">Adicionar Produto</Button>
                        </div>
                    </div>

                    <form @submit.prevent="submitSearch" class="flex flex-col sm:flex-row items-center gap-3">
                        <Input v-model="filter.search" type="text" placeholder="Pesquise por produtos"
                            class="w-full sm:flex-grow rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" />
                        <Button type="submit" variant="default" class="w-full sm:w-auto">
                            Pesquisar
                        </Button>
                    </form>

                    <!-- Card Grid replacing Table -->
                    <div class="grid grid-cols-1 gap-6">
                        <Card v-for="product in products.filter(product => product.status_id === 1)" :key="product.id" 
                            class="flex flex-col justify-between hover:shadow-lg transition-shadow duration-200 bg-white dark:bg-gray-800/50 border border-gray-200 dark:border-gray-700">
                            <CardHeader>
                                <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                                    <div class="relative w-24 h-24 flex-shrink-0">
                                        <img 
                                            :src="product.image_url || '/images/placeholder.png'" 
                                            :alt="product.name" 
                                            class="w-full h-full object-cover rounded-lg shadow-md"
                                        />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2">
                                            <CardTitle class="truncate text-xl text-gray-900 dark:text-gray-100">{{ product.name }}</CardTitle>
                                            <span class="px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                                Ativo
                                            </span>
                                        </div>
                                        <CardDescription class="mt-1 line-clamp-2 text-gray-500 dark:text-gray-400">{{ product.description || 'Sem descrição' }}</CardDescription>
                                    </div>
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mt-4">
                                    <div class="flex items-center gap-2 p-3 bg-gray-50/50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <DollarSign class="h-5 w-5 text-gray-600 dark:text-gray-300" />
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Preço de Custo</p>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ product.price ? `R$ ${product.price}` : 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 p-3 bg-gray-50/50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <ShoppingBag class="h-5 w-5 text-gray-600 dark:text-gray-300" />
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Preço de Venda</p>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ product.price_for_sale ? `R$ ${product.price_for_sale}` : 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 p-3 bg-gray-50/50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <Package class="h-5 w-5 text-gray-600 dark:text-gray-300" />
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Estoque</p>
                                            <p class="font-semibold text-gray-900 dark:text-gray-100">{{ product.quantity ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-2 p-3 bg-gray-50/50 dark:bg-gray-700/50 rounded-lg border border-gray-200 dark:border-gray-600">
                                        <Truck class="h-5 w-5 text-gray-600 dark:text-gray-300" />
                                        <div>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Fornecedor</p>
                                            <p class="font-semibold truncate text-gray-900 dark:text-gray-100">{{ product.supplier ? product.supplier.name : 'N/A' }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex justify-end gap-2 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <Button @click="openEditProductModal(product)" icon="Pencil" variant="outline" class="border-gray-200 dark:border-gray-600 text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">Editar</Button>
                                    <Button @click="openDeleteConfirmModal(product)" icon="Trash" variant="destructive" class="bg-rose-100 text-rose-800 dark:bg-rose-500/20 dark:text-rose-400 hover:bg-rose-200 dark:hover:bg-rose-500/30">Excluir</Button>
                                </div>
                            </CardContent>
                        </Card>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Adicionar Produto -->
        <Modal ref="modalRef">
            <template #title>
                <Title title="Adicionar Produto" :level="1" />
            </template>
            <template #body>
                <div>
                    <form @submit.prevent="openCreateConfirmModal" class="space-y-4">
                        <div>
                            <Label for="name">Nome</Label>
                            <Input id="name" v-model="formData.name" type="text" required autofocus :tabindex="1" placeholder="Anel do Poder" />
                        </div>
                        <div>
                            <Label for="description">Descrição</Label>
                            <Input id="description" v-model="formData.description" type="text" autofocus :tabindex="1" placeholder="É um item para dar mais poder" />
                        </div>
                        <div>
                            <Label for="price">Preço</Label>
                            <Input id="price" v-model="formData.price" type="number" autofocus :tabindex="1" placeholder="R$ 15" />
                        </div>
                        <div>
                            <Label for="price_for_sale">Preço de Venda</Label>
                            <Input id="price_for_sale" v-model="formData.price_for_sale" type="number" autofocus :tabindex="1" placeholder="R$ 20" />
                        </div>
                        <div>
                            <Label for="quantity">Quantidade em Estoque</Label>
                            <Input id="quantity" v-model="formData.quantity" type="number" autofocus :tabindex="1" placeholder="120" />
                        </div>
                        <div>
                            <Label for="supplier">Fornecedor</Label>
                            <select id="supplier" v-model="formData.supplier_id" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background">
                                <option value="">Selecione um fornecedor</option>
                                <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                    {{ supplier.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <Label for="image">Imagem do Produto</Label>
                            <div class="mt-2 flex items-center gap-4">
                                <div class="relative w-32 h-32 flex-shrink-0">
                                    <img 
                                        :src="formData.image ? URL.createObjectURL(formData.image) : '/images/placeholder.png'" 
                                        alt="Preview" 
                                        class="w-full h-full object-cover rounded-lg shadow-md"
                                    />
                                </div>
                                <Input 
                                    id="image" 
                                    type="file" 
                                    accept="image/*"
                                    @change="(e: Event) => handleImageUpload(e, false)"
                                    class="w-full"
                                />
                            </div>
                        </div>
                        <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                            <Button type="button" @click="openCreateConfirmModal" variant="default">Criar Produto</Button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>

        <!-- Modal de Edição -->
        <Modal ref="editModalRef">
            <template #title>
                <Title title="Editar Produto" :level="1" />
            </template>
            <template #body>
                <form @submit.prevent="openUpdateConfirmModal" class="space-y-4">
                    <div>
                        <Label for="edit-name">Nome</Label>
                        <Input v-model="selectedProduct.name" id="edit-name" type="text" required />
                    </div>
                    <div>
                        <Label for="edit-description">Descrição</Label>
                        <Input v-model="selectedProduct.description" id="edit-description" type="text" />
                    </div>
                    <div>
                        <Label for="edit-price">Preço</Label>
                        <Input v-model="selectedProduct.price" id="edit-price" type="number" />
                    </div>
                    <div>
                        <Label for="edit-price_for_sale">Preço de Venda</Label>
                        <Input v-model="selectedProduct.price_for_sale" id="edit-price_for_sale" type="number" />
                    </div>
                    <div>
                        <Label for="edit-quantity">Quantidade em Estoque</Label>
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
                                <Input v-model="customQuantityToAdd" type="number" placeholder="Quantidade personalizada" class="p-2" min="1" />
                                <Button type="button" @click="addCustomQuantity" variant="outline" size="lg">
                                    Adicionar Personalizado
                                </Button>
                            </div>
                        </div>
                        <div class="mt-2 rounded-md bg-blue-50 p-2 text-sm text-blue-800">
                            Nota: Você pode apenas aumentar a quantidade em estoque, não reduzi-la.
                        </div>
                    </div>
                    <div>
                        <Label for="edit-supplier">Fornecedor</Label>
                        <select v-model="selectedProduct.supplier_id" id="edit-supplier" class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background">
                            <option value="">Sem fornecedor</option>
                            <option v-for="supplier in suppliers" :key="supplier.id" :value="supplier.id">
                                {{ supplier.name }}
                            </option>
                        </select>
                    </div>
                    <div>
                        <Label for="edit-image">Imagem do Produto</Label>
                        <div class="mt-2 flex items-center gap-4">
                            <div class="relative w-32 h-32 flex-shrink-0">
                                <img 
                                    :src="selectedProduct.image ? URL.createObjectURL(selectedProduct.image) : (selectedProduct.image_url || '/images/placeholder.png')" 
                                    :alt="selectedProduct.name" 
                                    class="w-full h-full object-cover rounded-lg shadow-md"
                                />
                            </div>
                            <Input 
                                id="edit-image" 
                                type="file" 
                                accept="image/*"
                                @change="(e: Event) => handleImageUpload(e, true)"
                                class="w-full"
                            />
                        </div>
                    </div>
                    <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                        <Button type="button" @click="openUpdateConfirmModal" :variant="'default'">Atualizar Produto</Button>
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