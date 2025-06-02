<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref } from 'vue';
// Componentes de filtro
import ProductHistoryFilters from '@/components/ReportComponents/ProductHistoryFilters.vue';
import FinancialEntryFilters from '@/components/ReportComponents/FinancialEntryFilters.vue';

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
        <!-- Header com Gradiente -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-blue-600 to-purple-600 dark:from-blue-800 dark:to-purple-800 p-12 mb-12 shadow-xl">
          <div class="relative z-10">
            <h1 class="text-4xl font-bold text-white mb-4">
              Centro de Relatórios
            </h1>
            <p class="text-gray-100 text-lg max-w-2xl">
              Gere relatórios detalhados e personalizados para análise de dados
            </p>
          </div>
          <div class="absolute right-0 top-0 h-full w-1/3 bg-gradient-to-l from-white/10 to-transparent"></div>
        </div>

        <!-- Main Container -->
        <div class="space-y-8">
          <!-- Report Type Selection -->
          <div class=" rounded-3xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="p-8 border-b border-gray-200 dark:border-gray-700">
              <h2 class="text-2xl font-semibold mb-6 text-gray-900 dark:text-white flex items-center">
                <span class="w-2 h-8 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full mr-3"></span>
                Selecione o Tipo de Relatório
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div 
                  v-for="report in availableReports" 
                  :key="report.value"
                  @click="selectedReport = report.value"
                  class="group relative cursor-pointer transform transition-all duration-300 hover:scale-102"
                >
                  <div 
                    class="relative overflow-hidden rounded-2xl border-2 transition-all duration-300 shadow-lg hover:shadow-xl"
                    :class="selectedReport === report.value 
                      ? 'border-blue-500 dark:border-blue-400 ring-2 ring-blue-500/50' 
                      : 'border-gray-200 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-600'"
                  >
                    <div class="bg-gradient-to-br h-full w-full absolute opacity-10" :class="report.color"></div>
                    <div class=" group-hover:bg-gray-50 dark:group-hover:bg-gray-700/50 transition-colors duration-300">
                      <div class="relative p-6">
                        <div class="text-center">
                          <div class="text-5xl mb-4 transform group-hover:scale-110 transition-transform">{{ report.icon }}</div>
                          <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-3">{{ report.label }}</h3>
                          <p class="text-gray-600 dark:text-gray-300 leading-relaxed">{{ report.description }}</p>
                        </div>
                      </div>
                    </div>

                    <!-- Selection Indicator -->
                    <div 
                      v-if="selectedReport === report.value"
                      class="absolute top-4 right-4 w-8 h-8 bg-blue-500 dark:bg-blue-400 rounded-full flex items-center justify-center transform scale-110"
                    >
                      <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                      </svg>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Filters Section -->
            <div v-if="selectedReport" class="p-8 ">
              <h2 class="text-2xl font-semibold mb-6 text-gray-900 dark:text-white flex items-center">
                <span class="w-2 h-8 bg-gradient-to-b from-blue-500 to-purple-600 rounded-full mr-3"></span>
                Configurar Filtros
              </h2>
              
              <ProductHistoryFilters v-if="selectedReport === 'ProductsHistory'" v-model="filterData" />
              <FinancialEntryFilters v-if="selectedReport === 'LancamentoFinanceiro'" v-model="filterData" />
            </div>

            <!-- Empty State -->
            <div v-if="!selectedReport" class="p-16 text-center bg-gray-50 dark:bg-gray-800/50">
              <div class="text-7xl mb-6 animate-bounce">📊</div>
              <h3 class="text-2xl font-semibold text-gray-900 dark:text-gray-100 mb-3">
                Comece Selecionando um Relatório
              </h3>
              <p class="text-gray-600 dark:text-gray-400 text-lg">
                Escolha o tipo de relatório que deseja gerar acima
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>