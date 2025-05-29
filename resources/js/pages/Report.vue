<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
// Componentes de filtro
import ProductHistoryFilters from '@/components/ReportComponents/ProductHistoryFilters.vue';

const breadcrumbs = ref([
  { text: 'Home', href: '/', title: 'Página Inicial' },
  { text: 'Reports', href: '/reports', title: 'Relatórios' },
]);

// Estado do relatório
const selectedReport = ref('');
const filterData = ref<Record<string, any>>({});

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
];
</script>

<template>
  <Head title="Reports" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="min-h-screen p-6">
      <div class="max-w-7xl mx-auto mb-8">
        <div class="text-center mb-12">
          <h1 class="text-4xl font-bold text-black dark:text-white mb-4">
            Centro de Relatórios
          </h1>
          <p class="text-gray-700 dark:text-gray-300 text-lg max-w-2xl mx-auto">
            Gere relatórios detalhados e personalizados para análise de dados
          </p>
        </div>

        <!-- Main Container -->
        <div class="rounded-3xl shadow-lg border border-gray-300 dark:border-gray-600 overflow-hidden">
          
          <!-- Report Type Selection -->
          <div class="p-8 border-b border-gray-200 dark:border-gray-600">
            <h2 class="text-2xl font-semibold mb-6 text-black dark:text-white flex items-center">
              <span class="w-2 h-8 bg-black dark:bg-white rounded-full mr-3"></span>
              Selecione o Tipo de Relatório
            </h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <div 
                v-for="report in availableReports" 
                :key="report.value"
                @click="selectedReport = report.value"
                class="group relative cursor-pointer"
              >
                <div 
                  class="relative overflow-hidden mb-3 rounded-2xl border-2 transition-all duration-300"
                  :class="selectedReport === report.value 
                    ? 'border-black dark:border-white shadow-md' 
                    : 'border-gray-300 dark:border-gray-600 hover:border-gray-500 dark:hover:border-gray-400'"
                >
                  <div class="bg-gray-100 dark:bg-gray-700 group-hover:bg-gray-200 dark:group-hover:bg-gray-600 transition-colors duration-300"></div>
                  
                  <div class="relative p-6">
                    <div class="text-center">
                      <div class="text-4xl mb-3">{{ report.icon }}</div>
                      <h3 class="font-semibold text-black dark:text-white mb-2">{{ report.label }}</h3>
                      <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">{{ report.description }}</p>
                    </div>
                  </div>

                  <!-- Selection Indicator -->
                  <div 
                    v-if="selectedReport === report.value"
                    class="absolute top-3 right-3 w-6 h-6 bg-black dark:bg-white rounded-full flex items-center justify-center"
                  >
                    <svg class="w-4 h-4 text-white dark:text-black" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                    </svg>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Filters Section -->
          <div v-if="selectedReport" class="p-8">
            <h2 class="text-2xl font-semibold mb-6 text-black dark:text-white flex items-center">
              <span class="w-2 h-8 bg-black dark:bg-white rounded-full mr-3"></span>
              Configurar Filtros
            </h2>
            
            <div class="rounded-2xl p-6 border border-gray-300 dark:border-gray-600">
              <ProductHistoryFilters v-if="selectedReport === 'ProductsHistory'" v-model="filterData" />
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="!selectedReport" class="p-16 text-center">
            <div class="text-6xl mb-4">📊</div>
            <h3 class="text-xl font-semibold text-gray-600 dark:text-gray-300 mb-2">
              Comece Selecionando um Relatório
            </h3>
            <p class="text-gray-500 dark:text-gray-400">
              Escolha o tipo de relatório que deseja gerar acima
            </p>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>