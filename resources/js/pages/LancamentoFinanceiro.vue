<script setup lang="ts">
import ListFinancial from '@/components/FinancialComponents/ListFinancial.vue';
import Button from '@/components/ui/button/Button.vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import Modal from '@/components/ui/modal/Modal.vue';
import Title from '@/components/ui/title/Title.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import { onMounted, ref } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Lançamento Financeiro',
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

    const url = isEditing.value ? route('incomes.update', currentEditId.value as number) : route('incomes.store');

    const method = isEditing.value ? 'put' : 'post';

    revenueForm[method](url as string, {
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
    const url = isEditing.value ? route('expenses.update', currentEditId.value as number) : route('expenses.store');

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

const handleEdit = (item: FinancialItem) => {
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

// barra de pesquisa
const searchTerm = ref<string>('');

const handleSearch = () => {
    listFinancialRef.value?.carregarLancamentos(searchTerm.value);
};
</script>

<template>
    <Head title="Lançamento Financeiro" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] flex-1 rounded-xl border border-sidebar-border/70 p-6 dark:border-sidebar-border md:min-h-min">
                <div class="space-y-4">
                    <!-- Header Section -->
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b pb-4">
                        <div class="flex-1">
                            <Title title="Lançamentos Financeiros" :level="1" />
                            <Title title="Gerencie suas receitas, despesas, categorias e tipos de despesa" :level="2" />
                        </div>
                        <div class="flex flex-wrap gap-2">
                            <Button :icon="'Plus'" :variant="'default'" @click="openRevenueModal"> Nova Receita </Button>
                            <Button :icon="'Minus'" :variant="'destructive'" @click="openExpenseModal"> Nova Despesa </Button>
                            <Button :icon="'Tag'" :variant="'outline'" @click="openCategoryModal"> Categorias </Button>
                            <Button :icon="'FileText'" :variant="'outline'" @click="openExpenseTypeModal"> Tipos de Despesa </Button>
                        </div>
                    </div>

                    <!-- Search Section -->
                    <form @submit.prevent="handleSearch" class="flex flex-col items-center gap-3 sm:flex-row">
                        <Input
                            v-model="searchTerm"
                            @input="handleSearch"
                            type="text"
                            placeholder="Pesquise os Lançamentos"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 sm:flex-grow"
                        />
                        <Button type="submit" variant="default" class="w-full sm:w-auto"> Pesquisar </Button>
                    </form>

                    <!-- Financial List Section -->
                    <div>
                        <ListFinancial ref="listFinancialRef" @edit="handleEdit" />
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Nova Receita -->
        <Modal ref="revenueModalRef">
            <template #title>
                <Title title="Nova Receita" :level="1" />
            </template>
            <template #body>
                <form @submit.prevent="submitRevenue" class="space-y-4">
                    <div>
                        <Label for="revenue-name">Nome</Label>
                        <Input id="revenue-name" v-model="revenueForm.name" type="text" placeholder="Nome da receita" required />
                    </div>

                    <div>
                        <Label for="revenue-description">Descrição</Label>
                        <Input id="revenue-description" v-model="revenueForm.description" type="text" placeholder="Detalhes da receita" required />
                    </div>

                    <div>
                        <Label for="revenue-amount">Valor</Label>
                        <Input id="revenue-amount" v-model="revenueForm.amount" type="number" step="0.01" placeholder="0.00" required />
                    </div>

                    <div>
                        <Label for="revenue-date">Data</Label>
                        <Input id="revenue-date" v-model="revenueForm.date" type="date" required />
                    </div>

                    <!-- Seletor de Categorias -->
                    <div>
                        <Label>Categoria</Label>
                        <div class="mt-2 grid max-h-40 grid-cols-2 gap-2 overflow-y-auto rounded-md border p-2">
                            <div
                                v-for="category in categories"
                                :key="category.id"
                                @click="toggleCategorySelection(category.id)"
                                class="cursor-pointer rounded border p-2 transition-colors"
                                :class="{
                                    'border-primary bg-primary text-primary-foreground': selectedCategoryId === category.id,
                                    'hover:bg-muted': selectedCategoryId !== category.id,
                                }"
                            >
                                <div class="flex flex-col">
                                    <span class="text-sm font-medium">{{ category.name }}</span>
                                    <span class="truncate text-xs opacity-70">{{ category.description }}</span>
                                </div>
                            </div>
                        </div>
                        <p v-if="categories.length === 0" class="mt-2 text-sm text-destructive">
                            Nenhuma categoria disponível. Crie uma categoria primeiro.
                        </p>
                    </div>

                    <div class="flex justify-end space-x-2 border-t pt-4">
                        <Button type="submit" :disabled="revenueForm.processing || !selectedCategoryId" variant="default">
                            {{ isEditing ? 'Atualizar' : 'Salvar' }} Receita
                        </Button>
                    </div>
                </form>
            </template>
        </Modal>

        <!-- Modal Nova Despesa -->
        <Modal ref="expenseModalRef">
            <template #title>
                <Title title="Nova Despesa" :level="1" />
            </template>
            <template #body>
                <form @submit.prevent="submitExpense" class="space-y-4">
                    <div>
                        <Label for="expense-name">Nome</Label>
                        <Input id="expense-name" v-model="expenseForm.name" type="text" placeholder="Nome da despesa" required />
                    </div>

                    <div>
                        <Label for="expense-description">Descrição</Label>
                        <Input id="expense-description" v-model="expenseForm.description" type="text" placeholder="Detalhes da despesa" required />
                    </div>

                    <div>
                        <Label for="expense-amount">Valor</Label>
                        <Input id="expense-amount" v-model="expenseForm.amount" type="number" step="0.01" placeholder="0.00" required />
                    </div>

                    <div>
                        <Label for="expense-date">Data</Label>
                        <Input id="expense-date" v-model="expenseForm.date" type="date" required />
                    </div>

                    <div>
                        <Label for="expense-type">Tipo de Despesa</Label>
                        <select
                            id="expense-type"
                            v-model="expenseForm.expense_types_id"
                            class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-base ring-offset-background"
                            required
                        >
                            <option value="" disabled>Selecione um tipo</option>
                            <option v-for="type in expenseTypes" :key="type.id" :value="type.id">
                                {{ type.name }}
                            </option>
                        </select>
                        <p v-if="expenseTypes.length === 0" class="mt-2 text-sm text-destructive">
                            Você precisa criar pelo menos um tipo de despesa antes de continuar.
                        </p>
                    </div>

                    <div class="flex justify-end space-x-2 border-t pt-4">
                        <Button type="submit" variant="destructive" :disabled="expenseForm.processing || expenseTypes.length === 0">
                            {{ isEditing ? 'Atualizar' : 'Salvar' }} Despesa
                        </Button>
                    </div>
                </form>
            </template>
        </Modal>

        <!-- Modal Categorias -->
        <Modal ref="categoryModalRef">
            <template #title>
                <Title title="Gerenciar Categorias" :level="1" />
            </template>
            <template #body>
                <div class="space-y-6">
                    <!-- Formulário para nova categoria -->
                    <div class="border-b pb-4">
                        <form @submit.prevent="submitCategory" class="space-y-4">
                            <div>
                                <Label for="category-name">Nome</Label>
                                <Input id="category-name" v-model="categoryForm.name" type="text" placeholder="Nome da categoria" required />
                            </div>
                            <div>
                                <Label for="category-description">Descrição</Label>
                                <Input
                                    id="category-description"
                                    v-model="categoryForm.description"
                                    type="text"
                                    placeholder="Ex: Aluguel, Cliente X..."
                                    required
                                />
                            </div>
                            <div class="flex justify-end">
                                <Button type="submit" variant="default"> Adicionar Categoria </Button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de categorias existentes -->
                    <div>
                        <Title title="Categorias Existentes" :level="3" />
                        <div class="mt-3 max-h-60 space-y-2 overflow-y-auto">
                            <div
                                v-for="category in categories"
                                :key="category.id"
                                class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50"
                            >
                                <div class="flex-1">
                                    <div class="font-medium">{{ category.name }}</div>
                                    <div class="text-sm text-muted-foreground">{{ category.description }}</div>
                                </div>
                                <Button @click="deleteCategory(category.id)" variant="destructive" size="sm" icon="Trash"> Excluir </Button>
                            </div>
                            <div v-if="categories.length === 0" class="py-4 text-center text-muted-foreground">Nenhuma categoria cadastrada</div>
                        </div>
                    </div>
                </div>
            </template>
        </Modal>

        <!-- Modal Tipos de Despesa -->
        <Modal ref="expenseTypeModalRef">
            <template #title>
                <Title title="Gerenciar Tipos de Despesa" :level="1" />
            </template>
            <template #body>
                <div class="space-y-6">
                    <!-- Formulário para novo tipo -->
                    <div class="border-b pb-4">
                        <form @submit.prevent="submitExpenseType" class="space-y-4">
                            <div>
                                <Label for="expense-type-name">Nome</Label>
                                <Input id="expense-type-name" v-model="expenseTypeForm.name" type="text" placeholder="Nome do tipo" required />
                            </div>
                            <div>
                                <Label for="expense-type-description">Descrição</Label>
                                <Input
                                    id="expense-type-description"
                                    v-model="expenseTypeForm.description"
                                    type="text"
                                    placeholder="Descrição do tipo"
                                    required
                                />
                            </div>
                            <div class="flex justify-end">
                                <Button type="submit" variant="default"> Adicionar Tipo </Button>
                            </div>
                        </form>
                    </div>

                    <!-- Lista de tipos existentes -->
                    <div>
                        <Title title="Tipos Existentes" :level="3" />
                        <div class="mt-3 max-h-60 space-y-2 overflow-y-auto">
                            <div
                                v-for="type in expenseTypes"
                                :key="type.id"
                                class="flex items-center justify-between rounded-md border p-3 hover:bg-muted/50"
                            >
                                <div class="flex-1">
                                    <div class="font-medium">{{ type.name }}</div>
                                    <div class="text-sm text-muted-foreground">{{ type.description }}</div>
                                </div>
                                <Button @click="deleteExpenseType(type.id)" variant="destructive" size="sm" icon="Trash"> Excluir </Button>
                            </div>
                            <div v-if="expenseTypes.length === 0" class="py-4 text-center text-muted-foreground">
                                Nenhum tipo de despesa cadastrado
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </Modal>
    </AppLayout>
</template>
