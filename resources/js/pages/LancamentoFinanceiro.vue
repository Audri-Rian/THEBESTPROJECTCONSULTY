<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head } from '@inertiajs/vue3';
import Button from '@/components/ui/button/Button.vue'
import { ref, onMounted } from 'vue';
import Modal from '@/components/ui/modal/Modal.vue';
import { useForm } from '@inertiajs/vue3';
import axios from 'axios';

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

interface ModalInstance {
    openModal: () => void;
    closeModal: () => void;
}

// Variáveis reativas
const categories = ref<Category[]>([]);
const selectedCategoryId = ref<number | null>(null);

// Forms
const revenueForm = useForm({
    name: '',
    description: '',
    amount: 0,
    date: '',
    categories_id: null as number | null
});

const expenseForm = useForm({
    name: '',
    description: '',
    amount: 0,
    date: '',
    categories_id: null as number | null,
    type: ''
});

const categoryForm = useForm({
    name: '',
    description: ''
});

// Refs para as modais
const revenueModalRef = ref<ModalInstance | null>(null);
const expenseModalRef = ref<ModalInstance | null>(null);
const categoryModalRef = ref<ModalInstance | null>(null);

// Funções
const fetchCategories = async () => {
    try {
        const response = await axios.get<Category[]>(route('categories.index'));
        categories.value = response.data;
    } catch (error) {
        console.error('Erro ao carregar categorias:', error);
    }
};

const toggleCategorySelection = (categoryId: number) => {
    selectedCategoryId.value = selectedCategoryId.value === categoryId ? null : categoryId;
    revenueForm.categories_id = selectedCategoryId.value;
};

const submitRevenue = () => {
    if (!selectedCategoryId.value) {
        alert('Selecione uma categoria');
        return;
    }

    revenueForm.post(route('incomes.store'), {
        preserveScroll: true,
        onSuccess: () => {
            revenueModalRef.value?.closeModal();
            revenueForm.reset();
            selectedCategoryId.value = null;
        },
    });
};

const submitExpense = () => {
    expenseForm.post(route('expenses.store'), {
        preserveScroll: true,
        onSuccess: () => {
            expenseModalRef.value?.closeModal();
            expenseForm.reset();
        },
    });
};

const openRevenueModal = async () => {
    await fetchCategories();
    revenueModalRef.value?.openModal();
};

const openExpenseModal = async () => {
    await fetchCategories();
    expenseModalRef.value?.openModal();
};

const openCategoryModal = async () => {
    await fetchCategories();
    categoryModalRef.value?.openModal();
};

const submitCategory = () => {
    categoryForm.post(route('categories.store'), {
        preserveScroll: true,
        onSuccess: () => {
            categoryModalRef.value?.closeModal();
            categoryForm.reset();
            fetchCategories();
        }
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

onMounted(() => {
    fetchCategories();
});
</script>

<template>


    <Head title="Lancamento Financeiro" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="border rounded-xl p-5">
                <!--Rescentes-->
                <div class="mb-8 shadow text-white">

                    <h2 class="text-xl font-semibold mb-1">Lançamentos Recentes</h2>
                    <p class="text-sm text-gray-300 mb-3">Você pode adicionar novas financias e categorias!</p>
                    <table class="min-w-full divide-y text-sm border">
                        <thead class="bg-[#1c1c1c]">
                            <tr>
                                <th class="px-4 py-2 text-left font-semibold">Atributo</th>
                                <th class="px-4 py-2 text-left font-semibold">Tipo</th>
                                <th class="px-4 py-2 text-left font-semibold">Descrição</th>
                                <th class="px-4 py-2 text-left font-semibold">Valor</th>
                                <th class="px-4 py-2 text-left font-semibold">Data</th>
                                <th class="px-4 py-2 text-left font-semibold">Categoria</th>
                                <th class="px-4 py-2 text-left font-semibold">Hora Adicionada</th> <!--TimeStamp-->
                            </tr>
                        </thead>
                        <!-- Aqui virá o loop de lançamentos - LógicaListaLancamentos -->
                        <tbody class="divide-y divide-gray-700">
                            <tr>
                                <td class="px-4 py-2">Receita</td>
                                <td class="px-4 py-2">----</td>
                                <td class="px-4 py-2">Produto</td>
                                <td class="px-4 py-2">R$ 1000,00</td>
                                <td class="px-4 py-2">2025-03-21</td>
                                <td class="px-4 py-2">Produto</td>
                                <td class="px-4 py-2">12:15 - 25/02/2025</td>
                            </tr>
                        </tbody>
                        <tbody class="divide-y divide-gray-700">
                            <tr>
                                <td class="px-4 py-2">Despesa</td>
                                <td class="px-4 py-2">Fixa</td>
                                <td class="px-4 py-2">Aluguel</td>
                                <td class="px-4 py-2">R$ 500,00</td>
                                <td class="px-4 py-2">2025-04-21</td>
                                <td class="px-4 py-2">Imóvel</td>
                                <td class="px-4 py-2">13:50 - 24/02/2025</td>
                            </tr>
                        </tbody>
                    </table>

                </div>

                <!--Barra de Pesquisa-->
                <div class="mb-8 p-6 border rounded-lg shadow text-white">
                    <h2 class="text-xl font-semibold mb-4">Filtros e Pesquisa</h2>
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <input type="text" placeholder="Buscar por Nome..."
                            class="rounded-md border-gray-600 bg-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2" />
                        <select
                            class="rounded-md border-gray-600 bg-gray-700 text-white focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2">
                            <option value="">Todos os Atributos</option>
                            <option value="Receita">Receita</option>
                            <option value="Despesa">Despesa</option>
                        </select>
                        <select
                            class="rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm p-2">
                            <option value="">Todas as Categorias</option>
                            <option value="Alimentação">Alimentação</option>
                            <option value="Transporte">Transporte</option>
                            <option value="Salário">Salário</option>
                            <option value="Lazer">Lazer</option>
                        </select>
                        <input type="date"
                            class="p-2 rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />

                        <Button size="lg"
                            class="w-48 bg-white text-black rounded-md hover:bg-gray-600 hover:text-white">Pesquisar</Button>
                    </div>
                </div>



                <div class="flex justify-end space-x-5">
                    <!--Nova Receita-->
                    <Button @click="openRevenueModal" size="lg"
                        class="w-48 bg-green-700 text-white rounded-md hover:bg-green-600">Novo
                        Lançamento</Button>
                    <!--Nova Despesa-->
                    <Button @click="openExpenseModal" size="lg"
                        class="w-48 bg-red-700 text-white rounded-md hover:bg-red-600">Nova Despesa</Button>
                    <!--Nova Categoria-->
                    <Button @click="openCategoryModal" size="lg"
                        class="w-48 bg-purple-700 text-white rounded-md hover:bg-red-600">
                        Categorias</Button>
                </div>
            </div>
        </div>

        <!--Modal Nova Receita-->
        <Modal ref="revenueModalRef">
            <template #title>
                <h3 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Nova Receita</h3>
            </template>
            <template #body>
                <div class="mb-8 p-6 rounded-lg shadow text-white">
                    <form @submit.prevent="submitRevenue" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300">Nome:</label>
                            <input type="text" id="name" v-model="revenueForm.name"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Nome da receita" required />
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300">Descrição:</label>
                            <input type="text" id="description" v-model="revenueForm.description"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Detalhes da receita" required />
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-300">Valor:</label>
                            <input type="number" id="amount" v-model="revenueForm.amount" step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required />
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-300">Data:</label>
                            <input type="date" id="date" v-model="revenueForm.date"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required />
                        </div>

                        <!-- Seletor de Categorias -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Categoria:</label>
                            <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 bg-gray-700 rounded-md">
                                <div v-for="category in categories" :key="category.id"
                                    @click="toggleCategorySelection(category.id)"
                                    class="p-2 rounded cursor-pointer transition-colors" :class="{
                                        'bg-indigo-600': selectedCategoryId === category.id,
                                        'bg-gray-600 hover:bg-gray-500': selectedCategoryId !== category.id
                                    }">
                                    <div class="flex items-center">
                                        <span class="font-medium">{{ category.name }}</span>
                                        <span class="ml-2 text-xs text-gray-300 truncate">{{ category.description
                                            }}</span>
                                    </div>
                                </div>
                            </div>
                            <p v-if="categories.length === 0" class="text-sm text-gray-400 mt-2">
                                Nenhuma categoria disponível. Crie uma categoria primeiro.
                            </p>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                class="px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-600"
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
                <div>
                    <form class="space-y-4">

                        <form @submit.prevent="" class="space-y-4">
                            <div>
                                <label for="name" class="block text-sm font-medium text-gray-300">Nome:</label>
                                <input type="text" id="name" v-model="expenseForm.name"
                                    class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Nome da receita" required />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Descrição:</label>
                                <input type="text"
                                    class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    placeholder="Ex: Aluguel, Cliente X..." />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Valor:</label>
                                <input type="number"
                                    class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Data:</label>
                                <input type="date"
                                    class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Categoria:</label>
                                <select
                                    class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">

                                </select>
                            </div>
                            <!--Tipo-->
                            <div>
                                <label class="block text-sm font-medium text-gray-300">Origem:</label>
                                <select name="" id=""
                                    class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                    <option value="">Fixa</option>
                                    <option value="">Variavel</option>
                                </select>
                            </div>

                            <!-- Botões -->
                            <div class="flex gap-3 pt-4">
                                <button type="submit"
                                    class="px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-600">Salvar</button>
                            </div>
                        </form>
                    </form>
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
                <form @submit.prevent="submitCategory" class="space-y-4 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-300">Nome:</label>
                        <input type="text" id="name" v-model="categoryForm.name"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Nome da categoria" required />
                    </div>
                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-300">Descrição:</label>
                        <input type="text" id="description" v-model="categoryForm.description"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Ex: Aluguel, Cliente X..." required />
                    </div>

                    <div class="flex gap-3 pt-4">
                        <button type="submit"
                            class="px-4 py-2 bg-green-700 text-white rounded-md hover:bg-green-600">Salvar</button>
                    </div>
                </form>

                <!-- Lista de categorias existentes -->
                <div class="mt-6">
                    <h4 class="text-lg font-medium text-gray-300 mb-3">Categorias Existentes</h4>
                    <ul class="divide-y divide-gray-600">
                        <li v-for="category in categories" :key="category.id"
                            class="py-3 flex justify-between items-center group">
                            <div>
                                <span class="font-medium text-white">{{ category.name }}</span>
                                <p class="text-sm text-gray-400">{{ category.description }}</p>
                            </div>
                            <button @click="deleteCategory(category.id)"
                                class="opacity-0 group-hover:opacity-100 transition-opacity text-red-500 hover:text-red-400">
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