<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
import axios from 'axios';
// Componentes de filtro
import ProductHistoryFilters from '@/components/ReportComponents/ProductHistoryFilters.vue';
//import FinancialEntryFilters from '@/components/ReportComponents/FinancialEntryFilters.vue';

const breadcrumbs = ref([
  { text: 'Home', href: '/', title: 'Página Inicial' },
  { text: 'Reports', href: '/reports', title: 'Relatórios' },
]);

// Estado do relatório
const selectedReport = ref('');
const selectedFormats = ref<string[]>([]); // Definir tipo explícito
const availableReports = [
  { value: 'ProductsHistory', label: 'Histórico de Produtos' },
  { value: 'LancamentoFinanceiro', label: 'Lançamento Financeiro' },
];

const exportFormats = [
  { name: 'Excel', value: 'xlsx', icon: '📊' },
  { name: 'CSV', value: 'csv', icon: '📋' },
  { name: 'PDF', value: 'pdf', icon: '📄' },
  { name: 'JSON', value: 'json', icon: '🔩' }
];
// Helper para determinar o responseType
const getResponseType = (format: string) => {
  switch(format) {
    case 'xlsx': return 'arraybuffer';
    case 'pdf': return 'arraybuffer';
    case 'csv': return 'blob';
    case 'json': return 'blob'; // Alterado de 'json' para 'blob'
    default: return 'blob';
  }
};
// Dados dos filtros (serão preenchidos pelos componentes filhos)
const filterData = ref<Record<string, any>>({}); // Tipagem genérica

// Lógica para gerar relatório
const generateReport = async () => {
  if (!selectedReport.value || selectedFormats.value.length === 0) return;

  try {
    for (const format of selectedFormats.value) {
      const response = await axios.post('/reports/product-history', {
        ...filterData.value,
        format: format
      }, {
        responseType: getResponseType(format)
      });

      const timestamp = new Date().toISOString().slice(0, 19).replace(/[-T:]/g, '');
      const filename = `report-${timestamp}.${format}`;

      const url = window.URL.createObjectURL(
        new Blob([response.data], {
          type: response.headers?.['content-type'] || 'application/octet-stream'
        })
      );

      const link = document.createElement('a');
      link.href = url;
      link.setAttribute('download', filename);
      document.body.appendChild(link);
      link.click();

      if (link.parentNode) {
        link.parentNode.removeChild(link);
      }
      window.URL.revokeObjectURL(url);
    }
  } catch (error) {
    console.error('Erro ao gerar relatório:', error);
    alert('Erro ao gerar relatório! Verifique o console para detalhes.');
  }
};
</script>

<template>

  <Head title="Reports" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
      <div class="rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
        <h1 class="text-2xl font-bold mb-6">Gerar Relatório</h1>

        <!-- Seção de seleção de relatório -->
        <div class="mb-8">
          <label class="block text-sm font-medium mb-2 dark:text-gray-300">Tipo de Relatório</label>
          <select v-model="selectedReport"
            class="w-full p-2 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300">
            <option value="" disabled>Selecione um relatório</option>
            <option v-for="report in availableReports" :key="report.value" :value="report.value">
              {{ report.label }}
            </option>
          </select>
        </div>

        <!-- Componentes de filtro dinâmicos -->
        <div class="mb-8">
          <ProductHistoryFilters v-if="selectedReport === 'ProductsHistory'" v-model="filterData" />
          <SuppliersFilters v-if="selectedReport === 'Suppliers'" v-model="filterData" />
          <FinancialEntryFilters v-if="selectedReport === 'LancamentoFinanceiro'" v-model="filterData" />
          <SalesFilters v-if="selectedReport === 'Sales'" v-model="filterData" />
        </div>

        <!-- Seção de formatos de exportação -->
        <div class="mb-8">
          <label class="block text-sm font-medium mb-4 dark:text-gray-300">Formatos de Exportação</label>
          <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <label v-for="format in exportFormats" :key="format.value"
              class="flex items-center p-4 border rounded-md cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800"
              :class="{ 'border-blue-500': selectedFormats.includes(format.value) }">
              <input type="checkbox" v-model="selectedFormats" :value="format.value" class="hidden" />
              <span class="text-2xl mr-2">{{ format.icon }}</span>
              <span class="dark:text-gray-300">{{ format.name }}</span>
            </label>
          </div>
        </div>

        <!-- Botão de geração -->
        <button @click="generateReport"
          class="w-full bg-blue-500 text-white py-3 px-6 rounded-md hover:bg-blue-600 transition-colors"
          :disabled="!selectedReport || selectedFormats.length === 0">
          Gerar Relatório
        </button>
      </div>
    </div>
  </AppLayout>
</template>