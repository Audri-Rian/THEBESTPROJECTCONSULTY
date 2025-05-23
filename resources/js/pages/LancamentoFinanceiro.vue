<script setup lang="ts">
import Button from '@/components/ui/button/Button.vue';
import Modal from '@/components/ui/modal/Modal.vue';
import SearchBarFinancial from '@/components/FinancialComponents/SearchBarFinancial.vue';
import ListFinancial from '@/components/FinancialComponents/ListFinancial.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { formatDate } from '@vueuse/core';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Lancamento Financeiro',
        href: '/LancamentoFinanceiro',
    },
];

// Interfaces
interface Category {
    id: number;
    name: string;
    description: string;
}

interface ExpenseType {
    id: number;
    name: string;
    description: string;
}

interface ModalInstance {
    openModal: () => void;
    closeModal: () => void;
}
interface FinancialItem {
    id: number;
    tipo: 'Receita' | 'Despesa';
    nome: string;
    descricao: string;
    valor: number;
    data: string;
    categoria?: string;
    tipoDespesa?: string;
    categoria_id?: number;
    tipoDespesa_id?: number;
}

onMounted(() => {
    fetchCategories();
    fetchExpenseTypes();
});

// -> Código de Lançamento Financeiro
// Forms
const revenueForm = useForm({
    name: '',
    description: '',
    amount: 0,
    date: '',
    categories_id: null as number | null,
});
const revenueModalRef = ref<ModalInstance | null>(null);

const submitRevenue = () => {
    if (!selectedCategoryId.value) {
        alert('Selecione uma categoria');
        return;
    }
    
    const url = isEditing.value
        ? route('incomes.update', currentEditId.value as number) // Type assertion
        : route('incomes.store');

    const method = isEditing.value ? 'put' : 'post';

    revenueForm[method](url as string, { // Type assertion para string
        preserveScroll: true,
        onSuccess: () => {
            revenueModalRef.value?.closeModal();
            revenueForm.reset();
            selectedCategoryId.value = null;
            listFinancialRef.value?.carregarLancamentos();
            isEditing.value = false;
            currentEditId.value = null;
        },
    });
};
const openRevenueModal = async () => {
    await fetchCategories();
    revenueModalRef.value?.openModal();
};

// Modal
const openExpenseModal = async () => {
    await Promise.all([fetchCategories(), fetchExpenseTypes()]);
    expenseModalRef.value?.openModal();
};

// -> Código de Despesas
// Variavel Reativa
const expenseModalRef = ref<ModalInstance | null>(null);
// Refs para as modais
const categoryModalRef = ref<ModalInstance | null>(null);

// Form
const expenseForm = useForm({
    name: '',
    description: '',
    amount: 0,
    date: '',
    expense_types_id: null as number | null,
});

//Funções
const submitExpense = () => {
    const url = isEditing.value
        ? route('expenses.update', currentEditId.value as number)
        : route('expenses.store');

    const method = isEditing.value ? 'put' : 'post';

    expenseForm[method](url as string, {
        preserveScroll: true,
        onSuccess: () => {
            expenseModalRef.value?.closeModal();
            expenseForm.reset();
            listFinancialRef.value?.carregarLancamentos();
            isEditing.value = false;
            currentEditId.value = null;
        },
    });
};

// -> Código de Categorias
// Variavel Reativa
const categories = ref<Category[]>([]);

// Variavel ao qual eu não sei a funcionalidade
const selectedCategoryId = ref<number | null>(null);
const toggleCategorySelection = (categoryId: number) => {
    selectedCategoryId.value = selectedCategoryId.value === categoryId ? null : categoryId;
    revenueForm.categories_id = selectedCategoryId.value;
};

// Modal
const openCategoryModal = async () => {
    await fetchCategories();
    categoryModalRef.value?.openModal();
};

// Forms
const categoryForm = useForm({
    name: '',
    description: '',
});
// Funções
const fetchCategories = async () => {
    try {
        const response = await axios.get<Category[]>(route('categories.index'));
        categories.value = response.data;
    } catch (error) {
        console.error('Erro ao carregar categorias:', error);
    }
};
const submitCategory = () => {
    categoryForm.post(route('categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            categoryModalRef.value?.closeModal();
            categoryForm.reset();
            fetchCategories();
        },
    });
};

const deleteCategory = async (id: number) => {
    if (!confirm('Tem certeza que deseja excluir esta categoria?')) {
        return;
    }

    try {
        await axios.delete(route('categories.destroy', id));
        fetchCategories();
    } catch (error) {
        console.error('Erro ao excluir categoria:', error);
    }
};

// -> Código de Tipos de Despesas
// Variavel Reativa
const expenseTypes = ref<ExpenseType[]>([]);
const selectedExpenseTypeId = ref<number | null>(null);

// Formularios
const expenseTypeForm = useForm({
    name: '',
    description: '',
});
// Váriavel Reativa de Modal
const expenseTypeModalRef = ref<ModalInstance | null>(null);

// Modal
const openExpenseTypeModal = async () => {
    await fetchExpenseTypes();
    expenseTypeModalRef.value?.openModal();
};

// Funções
const fetchExpenseTypes = async () => {
    try {
        const response = await axios.get<ExpenseType[]>(route('expense-types.index'));
        expenseTypes.value = response.data;
    } catch (error) {
        console.error('Erro ao carregar tipos de despesa:', error);
    }
};

const submitExpenseType = () => {
    expenseTypeForm.post(route('expense-types.store'), {
        preserveScroll: true,
        onSuccess: () => {
            expenseTypeModalRef.value?.closeModal();
            expenseTypeForm.reset();
            fetchExpenseTypes();
        },
    });
};

const deleteExpenseType = async (id: number) => {
    if (!confirm('Tem certeza que deseja excluir este tipo de despesa?')) {
        return;
    }

    try {
        await axios.delete(route('expense-types.destroy', id));
        fetchExpenseTypes();
    } catch (error) {
        console.error('Erro ao excluir tipo de despesa:', error);
    }
};

//Listagem de Dados
const listFinancialRef = ref();
const isEditing = ref(false);
const currentEditId = ref<number | null>(null);

const handleEdit = (item: FinancialItem) => { // Tipo explícito
    isEditing.value = true;
    currentEditId.value = item.id;

    if (item.tipo === 'Receita') {
        revenueForm.name = item.nome;
        revenueForm.description = item.descricao;
        revenueForm.amount = Math.abs(item.valor);
        revenueForm.date = item.data;
        revenueForm.categories_id = item.categoria_id;
        selectedCategoryId.value = item.categoria_id;
        revenueModalRef.value?.openModal();
    } else {
        expenseForm.name = item.nome;
        expenseForm.description = item.descricao;
        expenseForm.amount = Math.abs(item.valor);
        expenseForm.date = item.data;
        expenseForm.expense_types_id = item.tipoDespesa_id;
        expenseModalRef.value?.openModal();
    }
};

</script>

<template>

    <Head title="Lancamento Financeiro" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="rounded-xl border p-5">
                <!--Rescentes-->
                <div class="mb-8 text-white shadow">
                    <h2 class="mb-1 text-xl font-semibold">Lançamentos Recentes</h2>
                    <p class="mb-3 text-sm text-gray-300">Essa página funciona, mas vou fazer uma refatoração de código
                        completo nela.</p>

                    <!--Barra de Pesquisa-->
                    <SearchBarFinancial />
                    <!--Listagem Financeira-->
                    <ListFinancial ref="listFinancialRef" @edit="handleEdit" />
                </div>

                <div class="flex justify-start space-x-5">
                    <!--Nova Receita-->
                    <Button @click="openRevenueModal" size="lg"
                        class="w-48 rounded-md bg-green-700 text-white hover:bg-green-600">Novo Lançamento</Button>
                    <!--Nova Despesa-->
                    <Button @click="openExpenseModal" size="lg"
                        class="w-48 rounded-md bg-red-700 text-white hover:bg-red-600">Nova Despesa</Button>
                    <!--Nova Categoria-->
                    <Button @click="openCategoryModal" size="lg"
                        class="w-48 rounded-md bg-purple-700 text-white hover:bg-purple-600">
                        Categorias</Button>
                    <!--Nova Despesa-->
                    <Button @click="openExpenseTypeModal" size="lg"
                        class="w-48 rounded-md bg-blue-700 text-white hover:bg-blue-600">
                        Tipos de Despesa
                    </Button>
                </div>
            </div>
        </div>

        <!--Modal Nova Receita-->
        <Modal ref="revenueModalRef">
            <template #title>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Nova Receita</h3>
            </template>
            <template #body>
                <div class="mb-8 rounded-lg p-6 text-white shadow">
                    <form @submit.prevent="submitRevenue" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300">Nome:</label>
                            <input type="text" id="name" v-model="revenueForm.name"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Nome da receita" required />
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300">Descrição:</label>
                            <input type="text" id="description" v-model="revenueForm.description"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Detalhes da receita" required />
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-300">Valor:</label>
                            <input type="number" id="amount" v-model="revenueForm.amount" step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required />
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-300">Data:</label>
                            <input type="date" id="date" v-model="revenueForm.date"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required />
                        </div>

                        <!-- Seletor de Categorias -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-300">Categoria:</label>
                            <div class="grid max-h-40 grid-cols-2 gap-2 overflow-y-auto rounded-md bg-gray-700 p-2">
                                <div v-for="category in categories" :key="category.id"
                                    @click="toggleCategorySelection(category.id)"
                                    class="cursor-pointer rounded p-2 transition-colors" :class="{
                                        'bg-indigo-600': selectedCategoryId === category.id,
                                        'bg-gray-600 hover:bg-gray-500': selectedCategoryId !== category.id,
                                    }">
                                    <div class="flex items-center">
                                        <span class="font-medium">{{ category.name }}</span>
                                        <span class="ml-2 truncate text-xs text-gray-300">{{ category.description
                                        }}</span>
                                    </div>
                                </div>
                            </div>
                            <p v-if="categories.length === 0" class="mt-2 text-sm text-red-400">
                                Nenhuma categoria disponível. Crie uma categoria primeiro.
                            </p>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                class="rounded-md bg-green-700 px-4 py-2 text-white hover:bg-green-600"
                                :disabled="revenueForm.processing || !selectedCategoryId">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>

        <!--Modal Nova Despesa-->
        <Modal ref="expenseModalRef">
            <template #title>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Nova Despesa</h3>
            </template>
            <template #body>
                <div class="mb-8 rounded-lg p-6 text-white shadow">
                    <form @submit.prevent="submitExpense" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300">Nome:</label>
                            <input type="text" id="name" v-model="expenseForm.name"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Nome da despesa" required />
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300">Descrição:</label>
                            <input type="text" id="description" v-model="expenseForm.description"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                placeholder="Detalhes da despesa" required />
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-300">Valor:</label>
                            <input type="number" id="amount" v-model="expenseForm.amount" step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required />
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-300">Data:</label>
                            <input type="date" id="date" v-model="expenseForm.date"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required />
                        </div>

                        <!-- Seletor de Tipo de Despesa (agora obrigatório) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Tipo de Despesa:</label>
                            <select v-model="expenseForm.expense_types_id"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                required>
                                <option value="" disabled>Selecione um tipo</option>
                                <option v-for="type in expenseTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <p v-if="expenseTypes.length === 0" class="mt-2 text-sm text-red-400">
                                Você precisa criar pelo menos um tipo de despesa antes de continuar.
                            </p>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit" class="rounded-md bg-red-700 px-4 py-2 text-white hover:bg-red-600"
                                :disabled="expenseForm.processing || expenseTypes.length === 0">
                                Salvar Despesa
                            </button>
                        </div>
                    </form>
                </div>
            </template>
        </Modal>

        <!-- Modal Tipos de Despesa -->
        <Modal ref="expenseTypeModalRef">
            <template #title>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Gerenciar Tipos de Despesa</h3>
            </template>
            <template #body>
                <form @submit.prevent="submitExpenseType" class="mb-6 space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Nome:</label>
                        <input type="text" v-model="expenseTypeForm.name"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Nome do tipo" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Descrição:</label>
                        <input type="text" v-model="expenseTypeForm.description"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Descrição do tipo" required />
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="rounded-md bg-blue-700 px-4 py-2 text-white hover:bg-blue-600">Salvar</button>
                    </div>
                </form>

                <div class="mt-6">
                    <h4 class="mb-3 text-lg font-medium text-gray-300">Tipos Existentes</h4>
                    <ul class="divide-y divide-gray-600">
                        <li v-for="type in expenseTypes" :key="type.id"
                            class="group flex items-center justify-between py-3">
                            <div>
                                <span class="font-medium text-white">{{ type.name }}</span>
                                <p class="text-sm text-gray-400">{{ type.description }}</p>
                            </div>
                            <button @click="deleteExpenseType(type.id)"
                                class="text-red-500 opacity-0 transition-opacity hover:text-red-400 group-hover:opacity-100">
                                <!-- Ícone de lixeira (igual ao da modal de categorias) -->
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </li>
                    </ul>
                </div>
            </template>
        </Modal>

        <!--Modal Nova Categoria-->
        <Modal ref="categoryModalRef">
            <template #title>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Gerenciar Categorias</h3>
            </template>
            <template #body>
                <!-- Formulário para nova categoria -->
                <form @submit.prevent="submitCategory" class="mb-6 space-y-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300">Nome:</label>
                        <input type="text" id="name" v-model="categoryForm.name"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Nome da categoria" required />
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300">Descrição:</label>
                        <input type="text" id="description" v-model="categoryForm.description"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            placeholder="Ex: Aluguel, Cliente X..." required />
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="rounded-md bg-green-700 px-4 py-2 text-white hover:bg-green-600">Salvar</button>
                    </div>
                </form>

                <!-- Lista de categorias existentes -->
                <div class="mt-6">
                    <h4 class="mb-3 text-lg font-medium text-gray-300">Categorias Existentes</h4>
                    <ul class="divide-y divide-gray-600">
                        <li v-for="category in categories" :key="category.id"
                            class="group flex items-center justify-between py-3">
                            <div>
                                <span class="font-medium text-white">{{ category.name }}</span>
                                <p class="text-sm text-gray-400">{{ category.description }}</p>
                            </div>
                            <button @click="deleteCategory(category.id)"
                                class="text-red-500 opacity-0 transition-opacity hover:text-red-400 group-hover:opacity-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </li>
                    </ul>
                </div>
            </template>
        </Modal>
    </AppLayout>
</template>
