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
    <div>
        <div v-if="loading" class="p-4 text-center text-gray-400">Carregando...</div>

        <div v-else-if="error" class="p-4 text-center text-red-500">
            {{ error }}
        </div>

        <template v-else>
            <div v-if="lancamentos.length === 0" class="p-4 text-center text-yellow-500">Nenhum lançamento encontrado</div>

            <table v-else class="min-w-full divide-y border text-sm">
                <thead class="bg-[#1c1c1c] text-white">
                    <tr>
                        <th class="px-4 py-2 text-left font-semibold">Tipo</th>
                        <th class="px-4 py-2 text-left font-semibold">Nome</th>
                        <th class="px-4 py-2 text-left font-semibold">Descrição</th>
                        <th class="px-4 py-2 text-left font-semibold">Valor</th>
                        <th class="px-4 py-2 text-left font-semibold">Data</th>
                        <th class="px-4 py-2 text-left font-semibold">Categoria / Tipo</th>
                        <th class="px-4 py-2 text-left font-semibold">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="item in lancamentos" :key="item.id" class="border-t transition-colors hover:bg-gray-800">
                        <td class="px-4 py-2">
                            <span
                                class="inline-block rounded px-2 py-1"
                                :class="{
                                    'bg-green-700 text-green-100': item.tipo === 'Receita',
                                    'bg-red-700 text-red-100': item.tipo === 'Despesa',
                                }"
                            >
                                {{ item.tipo }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ item.nome }}</td>
                        <td class="px-4 py-2">{{ item.descricao }}</td>
                        <td
                            class="px-4 py-2 font-mono"
                            :class="{
                                'text-green-400': item.tipo === 'Receita',
                                'text-red-400': item.tipo === 'Despesa',
                            }"
                        >
                            R$ {{ formatarValor(item.valor) }}
                        </td>
                        <td class="px-4 py-2">{{ formatarData(item.data) }}</td>
                        <td class="px-4 py-2">
                            {{ item.tipo === 'Receita' ? item.categoria : item.tipoDespesa }}
                        </td>
                        <td class="flex gap-2 px-4 py-2">
                            <button @click="editarLancamento(item)" class="p-1 text-blue-400 transition-colors hover:text-blue-300" title="Editar">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"
                                    />
                                </svg>
                            </button>

                            <button @click="excluirLancamento(item)" class="p-1 text-red-400 transition-colors hover:text-red-300" title="Excluir">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"
                                    />
                                </svg>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </template>
    </div>
</template>
