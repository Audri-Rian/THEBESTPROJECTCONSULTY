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

/**
 * todo: Para manter uma boa pratica, separar as interfaces no js/types.
 * todo: Ela mantem uma tipagem forte, a fim de garantir que os dados recebidos/salvos tenham a estrutura correta, ela tambem previne erros ao tentar acessar dados que não existem
 * 
 * ? Vue 3: Variaveis Reativas. usamos ref e reactive para criar varias que, quando alteradas, atualizam automaticamente a interface do usuario
 * ? Porque usar? quando o vlaor muda, o Vue atualiza o DOM automaticamente, uso em templates facilita a vinculação de dados como(V-MODEL, {{variavel}} )
 * ? const categories = ref<Category[]>([]); // Armazena um array vazio e são preenchidos por via chamada de API
 * ? Instanciar chamadas de funções repetidas a fim de evitar um reprocessamento desnecessarios
 * 
 * * useForm é um helper do interia que facilita o gerenciamento de formularios, submissão de dados, tratamento de erros, reset de campos
 * * o useForm do vue ja vem com os metodos como reset, post e erros, tem uma propriedade chamda PROCESSING que indica se o formulario está sendo enviado
 * * é chamado no template com   v-model="revenueForm.name" 
 * 
 * ! Usamos o axios para buscar dados da API laravel, que permite adicionar logica global e util se o usuario sair da pagina antes da resposta
 */

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

// -> onMounted
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
    categories_id: null as number | null
});
// Variaveis Reativas
const revenueModalRef = ref<ModalInstance | null>(null);
// Funções de Funcionalidade
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
    categories_id: null as number | null,
    expense_types_id: null as number | null
});

//Funções
const submitExpense = () => {
    if (!expenseForm.expense_types_id) {
        alert('Selecione um tipo de despesa');
        return;
    }

    expenseForm.post(route('expenses.store'), {
        preserveScroll: true,
        onSuccess: () => {
            expenseModalRef.value?.closeModal();
            expenseForm.reset();
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
    description: ''
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

// -> Código de Tipos de Despesas
// Variavel Reativa
const expenseTypes = ref<ExpenseType[]>([]);
const selectedExpenseTypeId = ref<number | null>(null);

// Formularios
const expenseTypeForm = useForm({
    name: '',
    description: ''
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
        }
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

</script>

<template>


    <Head title="Lancamento Financeiro" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="p-4">
            <div class="border rounded-xl p-5">
                <!--Rescentes-->
                <div class="mb-8 shadow text-white">

                    <h2 class="text-xl font-semibold mb-1">Lançamentos Recentes</h2>
                    <p class="text-sm text-gray-300 mb-3">Essa página funciona, mas vou fazer uma refatoração de código
                        completo nela.</p>
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
                        class="w-48 bg-green-700 text-white rounded-md hover:bg-green-600">Nova Receita</Button>
                    <!--Nova Despesa-->
                    <Button @click="openExpenseModal" size="lg"
                        class="w-48 bg-red-700 text-white rounded-md hover:bg-red-600">Nova Despesa</Button>
                    <!--Nova Categoria-->
                    <Button @click="openCategoryModal" size="lg"
                        class="w-48 bg-purple-700 text-white rounded-md hover:bg-red-600">
                        Categorias</Button>
                    <!--Nova Despesa-->
                    <Button @click="openExpenseTypeModal" size="lg"
                        class="w-48 bg-blue-700 text-white rounded-md hover:bg-blue-600">
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
                <div class="mb-8 p-6 rounded-lg shadow text-white">
                    <form @submit.prevent="submitExpense" class="space-y-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-300">Nome:</label>
                            <input type="text" id="name" v-model="expenseForm.name"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Nome da despesa" required />
                        </div>

                        <div>
                            <label for="description" class="block text-sm font-medium text-gray-300">Descrição:</label>
                            <input type="text" id="description" v-model="expenseForm.description"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                placeholder="Detalhes da despesa" required />
                        </div>

                        <div>
                            <label for="amount" class="block text-sm font-medium text-gray-300">Valor:</label>
                            <input type="number" id="amount" v-model="expenseForm.amount" step="0.01"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required />
                        </div>

                        <div>
                            <label for="date" class="block text-sm font-medium text-gray-300">Data:</label>
                            <input type="date" id="date" v-model="expenseForm.date"
                                class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                required />
                        </div>

                        <!-- Seletor de Categorias -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300 mb-2">Categoria:</label>
                            <div class="grid grid-cols-2 gap-2 max-h-40 overflow-y-auto p-2 bg-gray-700 rounded-md">
                                <div v-for="category in categories" :key="category.id"
                                    @click="expenseForm.categories_id = category.id"
                                    class="p-2 rounded cursor-pointer transition-colors" :class="{
                                        'bg-indigo-600': expenseForm.categories_id === category.id,
                                        'bg-gray-600 hover:bg-gray-500': expenseForm.categories_id !== category.id
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

                        <!-- Seletor de Tipo de Despesa (agora obrigatório) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-300">Tipo de Despesa:</label>
                            <select v-model="expenseForm.expense_types_id" class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm
                                focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                                <option value="" disabled>Selecione um tipo</option>
                                <option v-for="type in expenseTypes" :key="type.id" :value="type.id">
                                    {{ type.name }}
                                </option>
                            </select>
                            <p v-if="expenseTypes.length === 0" class="text-sm text-red-400 mt-2">
                                Você precisa criar pelo menos um tipo de despesa antes de continuar.
                            </p>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit" class="px-4 py-2 bg-red-700 text-white rounded-md hover:bg-red-600"
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
                <form @submit.prevent="submitExpenseType" class="space-y-4 mb-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Nome:</label>
                        <input type="text" v-model="expenseTypeForm.name"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Nome do tipo" required />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-300">Descrição:</label>
                        <input type="text" v-model="expenseTypeForm.description"
                            class="mt-1 block w-full rounded-md border-gray-600 bg-gray-700 text-white shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                            placeholder="Descrição do tipo" required />
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="submit" class="px-4 py-2 bg-blue-700 text-white rounded-md hover:bg-blue-600">
                            Salvar
                        </button>
                    </div>
                </form>

                <div class="mt-6">
                    <h4 class="text-lg font-medium text-gray-300 mb-3">Tipos Existentes</h4>
                    <ul class="divide-y divide-gray-600">
                        <li v-for="type in expenseTypes" :key="type.id"
                            class="py-3 flex justify-between items-center group">
                            <div>
                                <span class="font-medium text-white">{{ type.name }}</span>
                                <p class="text-sm text-gray-400">{{ type.description }}</p>
                            </div>
                            <button @click="deleteExpenseType(type.id)"
                                class="opacity-0 group-hover:opacity-100 transition-opacity text-red-500 hover:text-red-400">
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