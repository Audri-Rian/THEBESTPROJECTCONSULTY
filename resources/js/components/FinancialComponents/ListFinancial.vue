<script>
import axios from 'axios';

export default {
    data() {
        return {
            lancamentos: [],
            loading: true,
            error: null,
            searchQuery: '',
        };
    },
    mounted() {
        this.carregarLancamentos();
    },
    methods: {
        async carregarLancamentos(filter = '') {
            this.loading = true;
            try {
                const response = await axios.get('/search-entries', {
                    params: {
                        search: filter,
                    },
                });
                this.lancamentos = response.data;
            } catch (error) {
                this.error = `Erro: ${error.message}`;
            } finally {
                this.loading = false;
            }
        },
        formatarData(data) {
            try {
                return new Date(data).toLocaleDateString('pt-BR');
            } catch {
                return 'Data inválida';
            }
        },
        async excluirLancamento(item) {
            if (!confirm(`Tem certeza que deseja excluir "${item.nome}"?`)) return;

            try {
                const url = item.tipo === 'Receita' ? `/incomes/${item.id}` : `/expenses/${item.id}`;

                await axios.delete(url);
                await this.carregarLancamentos();
            } catch (error) {
                alert('Erro ao excluir: ' + (error.response?.data?.message || error.message));
            }
        },
        editarLancamento(item) {
            this.$emit('edit', item);
        },

        formatarValor(valor) {
            const num = Number(valor) || 0;
            return num.toFixed(2).replace('.', ',');
        },
    },
    expose: ['carregarLancamentos'],
};
</script>

<template>
    <div class="space-y-4">
        <!-- Loading State -->
        <div v-if="loading" class="flex items-center justify-center p-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600"></div>
            <span class="ml-3 text-gray-600 dark:text-gray-300">Carregando...</span>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="p-6 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
            <div class="flex items-center">
                <svg class="h-5 w-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="ml-2 text-red-600 dark:text-red-400">{{ error }}</span>
            </div>
        </div>

        <template v-else>
            <!-- Empty State -->
            <div v-if="lancamentos.length === 0" class="flex flex-col items-center justify-center p-8 text-center">
                <svg class="h-12 w-12 text-yellow-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
                <span class="text-yellow-600 dark:text-yellow-400 text-lg">Nenhum lançamento encontrado</span>
            </div>

            <!-- Table -->
            <div v-else class="bg-white dark:bg-gray-800 rounded-xl shadow-lg overflow-hidden border border-gray-200 dark:border-gray-700">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gradient-to-r from-blue-600/90 to-purple-600/90">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Nome</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Descrição</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Valor</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Data</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Categoria / Tipo</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-white uppercase tracking-wider">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <tr v-for="item in lancamentos" :key="item.id" 
                            class="transition-colors hover:bg-gray-50 dark:hover:bg-gray-700/50">
                            <td class="px-4 py-3 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                                    :class="{
                                        'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': item.tipo === 'Receita',
                                        'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200': item.tipo === 'Despesa',
                                    }">
                                    {{ item.tipo }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-900 dark:text-gray-100">{{ item.nome }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ item.descricao }}</td>
                            <td class="px-4 py-3 text-sm font-mono whitespace-nowrap"
                                :class="{
                                    'text-green-600 dark:text-green-400': item.tipo === 'Receita',
                                    'text-red-600 dark:text-red-400': item.tipo === 'Despesa',
                                }">
                                R$ {{ formatarValor(item.valor) }}
                            </td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">{{ formatarData(item.data) }}</td>
                            <td class="px-4 py-3 text-sm text-gray-500 dark:text-gray-400">
                                {{ item.tipo === 'Receita' ? item.categoria : item.tipoDespesa }}
                            </td>
                            <td class="px-4 py-3 text-sm">
                                <div class="flex items-center space-x-3">
                                    <button @click="editarLancamento(item)" 
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <button @click="excluirLancamento(item)" 
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </template>
    </div>
</template>

<style scoped>
.animate-spin {
    animation: spin 1s linear infinite;
}

@keyframes spin {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}
</style>
