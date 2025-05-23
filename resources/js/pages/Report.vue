<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
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
const isGenerating = ref(false);

const availableReports = [
  { 
    value: 'ProductsHistory', 
    label: 'Histórico de Produtos',
    description: 'Visualize o histórico completo de produtos e suas movimentações',
    icon: '📦',
    color: 'from-blue-500 to-cyan-600'
  },
  { 
    value: 'LancamentoFinanceiro', 
    label: 'Lançamento Financeiro',
    description: 'Analise os lançamentos e movimentações financeiras',
    icon: '💰',
    color: 'from-green-500 to-emerald-600'
  },
  { 
    value: 'Suppliers', 
    label: 'Fornecedores',
    description: 'Relatório detalhado de fornecedores e parcerias',
    icon: '🏢',
    color: 'from-purple-500 to-violet-600'
  },
  { 
    value: 'Sales', 
    label: 'Vendas',
    description: 'Acompanhe o desempenho de vendas e métricas',
    icon: '📈',
    color: 'from-orange-500 to-red-600'
  },
];

const exportFormats = [
  { name: 'Excel', value: 'xlsx', icon: '📊', color: 'border-green-500/30 hover:border-green-400 hover:bg-green-500/10 dark:hover:bg-green-500/20' },
  { name: 'CSV', value: 'csv', icon: '📋', color: 'border-blue-500/30 hover:border-blue-400 hover:bg-blue-500/10 dark:hover:bg-blue-500/20' },
  { name: 'PDF', value: 'pdf', icon: '📄', color: 'border-red-500/30 hover:border-red-400 hover:bg-red-500/10 dark:hover:bg-red-500/20' },
  { name: 'JSON', value: 'json', icon: '🔩', color: 'border-purple-500/30 hover:border-purple-400 hover:bg-purple-500/10 dark:hover:bg-purple-500/20' }
];

// Helper para determinar o responseType
const getResponseType = (format: string) => {
  switch(format) {
    case 'xlsx': return 'arraybuffer';
    case 'pdf': return 'arraybuffer';
    case 'csv': return 'blob';
    case 'json': return 'blob';
    default: return 'blob';
  }
};

// Dados dos filtros
const filterData = ref<Record<string, any>>({});

// Computed para verificar se pode gerar relatório
const canGenerate = computed(() => {
  return selectedReport.value && selectedFormats.value.length > 0 && !isGenerating.value;
});

// Lógica para gerar relatório
const generateReport = async () => {
  if (!canGenerate.value) return;

  isGenerating.value = true;
  
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
  } finally {
    isGenerating.value = false;
  }
};
</script>

<template>
  <Head title="Reports" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen bg-gradient-to-br from-gray-900 via-gray-800 to-slate-900 dark:from-gray-900 dark:via-gray-800 dark:to-blue-900/20 p-6">
      
      <!-- Header Section -->
      <div class="max-w-7xl mx-auto mb-8">
        <div class="text-center mb-12">
          <h1 class="text-4xl font-bold bg-gradient-to-r from-blue-400 to-purple-400 bg-clip-text text-transparent mb-4">
            Centro de Relatórios
          </h1>
          <p class="text-gray-300 dark:text-gray-300 text-lg max-w-2xl mx-auto">
            Gere relatórios detalhados e personalizados para análise de dados em diversos formatos
          </p>
        </div>

        <!-- Main Container -->
        <div class="bg-gray-800/90 backdrop-blur-sm dark:bg-gray-900/90 rounded-3xl shadow-2xl border border-gray-700/50 dark:border-gray-700/50 overflow-hidden">
          
          <!-- Report Type Selection -->
          <div class="p-8 border-b border-gray-700 dark:border-gray-700">
            <h2 class="text-2xl font-semibold mb-6 text-gray-100 dark:text-white flex items-center">
              <span class="w-2 h-8 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full mr-3"></span>
              Selecione o Tipo de Relatório
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              <div 
                v-for="report in availableReports" 
                :key="report.value"
                @click="selectedReport = report.value"
                class="group relative cursor-pointer"
              >
                <div 
                  class="relative overflow-hidden rounded-2xl border-2 transition-all duration-300 transform hover:scale-105"
                  :class="selectedReport === report.value 
                    ? 'border-blue-500 shadow-lg shadow-blue-500/25' 
                    : 'border-gray-600 dark:border-gray-600 hover:border-blue-400'"
                >
                  <div :class="`absolute inset-0 bg-gradient-to-br ${report.color} opacity-0 group-hover:opacity-20 transition-opacity duration-300`"></div>
                  
                  <div class="relative p-6 bg-gray-800 dark:bg-gray-800">
                    <div class="text-center">
                      <div class="text-4xl mb-3">{{ report.icon }}</div>
                      <h3 class="font-semibold text-gray-100 dark:text-white mb-2">{{ report.label }}</h3>
                      <p class="text-sm text-gray-300 dark:text-gray-300 leading-relaxed">{{ report.description }}</p>
                    </div>
                  </div>

                  <!-- Selection Indicator -->
                  <div 
                    v-if="selectedReport === report.value"
                    class="absolute top-3 right-3 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Filters Section -->
          <div v-if="selectedReport" class="p-8 border-b border-gray-700 dark:border-gray-700 bg-gray-700/30 dark:bg-gray-800/50">
            <h2 class="text-2xl font-semibold mb-6 text-gray-100 dark:text-white flex items-center">
              <span class="w-2 h-8 bg-gradient-to-b from-green-500 to-emerald-600 rounded-full mr-3"></span>
              Configurar Filtros
            </h2>
            
            <div class="bg-gray-800 dark:bg-gray-900 rounded-2xl p-6 shadow-lg border border-gray-600 dark:border-gray-700">
              <ProductHistoryFilters v-if="selectedReport === 'ProductsHistory'" v-model="filterData" />
              <SuppliersFilters v-if="selectedReport === 'Suppliers'" v-model="filterData" />
              <FinancialEntryFilters v-if="selectedReport === 'LancamentoFinanceiro'" v-model="filterData" />
              <SalesFilters v-if="selectedReport === 'Sales'" v-model="filterData" />
            </div>
          </div>

          <!-- Export Formats Section -->
          <div v-if="selectedReport" class="p-8 border-b border-gray-700 dark:border-gray-700">
            <h2 class="text-2xl font-semibold mb-6 text-gray-100 dark:text-white flex items-center">
              <span class="w-2 h-8 bg-gradient-to-b from-purple-500 to-pink-600 rounded-full mr-3"></span>
              Formatos de Exportação
            </h2>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <label 
                v-for="format in exportFormats" 
                :key="format.value"
                class="group relative cursor-pointer"
              >
                <input type="checkbox" v-model="selectedFormats" :value="format.value" class="sr-only" />
                <div 
                  class="relative overflow-hidden rounded-xl border-2 p-6 transition-all duration-300 transform hover:scale-105 bg-gray-700 dark:bg-gray-800"
                  :class="[
                    format.color,
                    selectedFormats.includes(format.value) 
                      ? 'border-blue-500 shadow-lg shadow-blue-500/25 bg-blue-500/20 dark:bg-blue-500/20' 
                      : 'border-gray-600 dark:border-gray-600'
                  ]"
                >
                  <div class="text-center">
                    <div class="text-3xl mb-2">{{ format.icon }}</div>
                    <span class="font-medium text-gray-100 dark:text-white">{{ format.name }}</span>
                  </div>

                  <!-- Selection Indicator -->
                  <div 
                    v-if="selectedFormats.includes(format.value)"
                    class="absolute top-2 right-2 w-5 h-5 bg-blue-500 rounded-full flex items-center justify-center"
                  >
                    <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                </div>
              </label>
            </div>

            <!-- Selected Formats Preview -->
            <div v-if="selectedFormats.length > 0" class="mt-6 p-4 bg-blue-500/20 dark:bg-blue-500/20 rounded-xl border border-blue-500/30 dark:border-blue-500/30">
              <p class="text-sm text-blue-200 dark:text-blue-200 mb-2">
                <strong>Formatos selecionados:</strong>
              </p>
              <div class="flex flex-wrap gap-2">
                <span 
                  v-for="format in selectedFormats" 
                  :key="format"
                  class="px-3 py-1 bg-blue-600/30 dark:bg-blue-600/30 text-blue-200 dark:text-blue-200 rounded-full text-sm font-medium"
                >
                  {{ exportFormats.find(f => f.value === format)?.name }}
                </span>
              </div>
            </div>
          </div>

          <!-- Generate Button Section -->
          <div v-if="selectedReport" class="p-8">
            <button 
              @click="generateReport"
              :disabled="!canGenerate"
              class="w-full relative overflow-hidden rounded-2xl py-4 px-8 text-lg font-semibold transition-all duration-300 transform group"
              :class="canGenerate 
                ? 'bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700 text-white shadow-lg hover:shadow-xl hover:scale-105' 
                : 'bg-gray-600 dark:bg-gray-700 text-gray-400 dark:text-gray-400 cursor-not-allowed'"
            >
              <div v-if="isGenerating" class="flex items-center justify-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Gerando Relatório...
              </div>
              <div v-else class="flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Gerar Relatório
              </div>
              
              <!-- Shine Effect -->
              <div class="absolute inset-0 -top-1/2 -bottom-1/2 bg-gradient-to-r from-transparent via-white/20 to-transparent skew-x-12 transform -translate-x-full group-hover:translate-x-full transition-transform duration-1000"></div>
            </button>

            <!-- Status Messages -->
            <div v-if="!selectedReport || selectedFormats.length === 0" class="mt-4 text-center">
              <p class="text-sm text-gray-400 dark:text-gray-400">
                {{ !selectedReport ? 'Selecione um tipo de relatório para continuar' : 'Escolha pelo menos um formato de exportação' }}
              </p>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="!selectedReport" class="p-16 text-center">
            <div class="text-6xl mb-4">📊</div>
            <h3 class="text-xl font-semibold text-gray-300 dark:text-gray-300 mb-2">
              Comece Selecionando um Relatório
            </h3>
            <p class="text-gray-400 dark:text-gray-400">
              Escolha o tipo de relatório que deseja gerar acima
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>