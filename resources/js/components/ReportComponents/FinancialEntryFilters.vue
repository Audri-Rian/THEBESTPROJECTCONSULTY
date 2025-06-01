<script setup lang="ts">
import { ref } from 'vue';
import { debounce } from 'lodash-es';
import axios from 'axios';

interface FinancialEntry {
  id: number;
  nome: string;
  descricao: string;
  valor: number;
  data: string;
  tipo: 'Receita' | 'Despesa';
  categoria?: string;
  tipoDespesa?: string;
}

const props = defineProps<{
  modelValue: object
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: typeof filters.value): void
}>();

const filters = ref({
  searchTerm: ''
});

const entrySuggestions = ref<FinancialEntry[]>([]);
const selectedEntry = ref<FinancialEntry | null>(null);
const searchError = ref<string | null>(null);
const isLoading = ref(false);
const isExporting = ref(false);

const exportFormats = [
  { name: 'Excel', value: 'xlsx', icon: '📊' },
  { name: 'CSV', value: 'csv', icon: '📋' },
  { name: 'PDF', value: 'pdf', icon: '📄' },
  { name: 'JSON', value: 'json', icon: '🔩' }
];

// Função para buscar lançamentos financeiros
const fetchEntries = debounce(async (value: string) => {
  if (value.length >= 1) {
    try {
      isLoading.value = true;
      const response = await axios.post('/reports/search-entries', {
        search: value
      });
      
      entrySuggestions.value = response.data;
    } catch (error) {
      console.error('Erro ao buscar lançamentos:', error);
      searchError.value = 'Erro ao buscar lançamentos. Tente novamente.';
    } finally {
      isLoading.value = false;
    }
  } else {
    entrySuggestions.value = [];
  }
}, 300);

const selectEntry = (entry: FinancialEntry) => {
  selectedEntry.value = entry;
  filters.value.searchTerm = entry.nome;
  emit('update:modelValue', filters.value);
  entrySuggestions.value = [];
};

const clearSuggestions = () => {
  setTimeout(() => {
    entrySuggestions.value = [];
  }, 200);
};

const exportReport = async (format: string, exportAll: boolean = false) => {
  try {
    isExporting.value = true;
    
    const response = await axios.post('/reports/export-financial', {
      format,
      entryId: exportAll ? null : selectedEntry.value?.id
    }, {
      responseType: format === 'json' ? 'json' : 'blob'
    });

    // Para JSON, apenas baixa o arquivo
    if (format === 'json') {
      const blob = new Blob([JSON.stringify(response.data, null, 2)], { type: 'application/json' });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `lancamentos-financeiros${exportAll ? '' : '-' + selectedEntry.value?.nome}.json`;
      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);
      window.URL.revokeObjectURL(url);
      return;
    }

    // Para outros formatos
    const blob = new Blob([response.data], {
      type: format === 'xlsx' 
        ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        : format === 'csv'
          ? 'text/csv'
          : 'application/pdf'
    });

    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = `lancamentos-financeiros${exportAll ? '' : '-' + selectedEntry.value?.nome}.${format}`;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);

  } catch (error) {
    console.error('Erro ao exportar relatório:', error);
    searchError.value = 'Erro ao exportar relatório. Tente novamente.';
  } finally {
    isExporting.value = false;
  }
};
</script>

<template>
  <div class="space-y-8">
    <!-- Seção de Busca de Lançamento -->
    <div class=" rounded-2xl p-1">
      <div class="backdrop-blur-sm rounded-xl p-8 border border-green-100 dark:border-green-900">
        <div class="flex items-center mb-6">
          <div class="flex-1">
            <h3 class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 dark:from-green-400 dark:to-emerald-400 bg-clip-text text-transparent">
              Exportar Lançamento Específico
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Busque e exporte dados de um lançamento individual</p>
          </div>
          <div class="text-4xl">🔍</div>
        </div>

        <div class="w-full">
          <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Nome do Lançamento</label>
          <div class="relative w-full">
            <input 
              type="text" 
              v-model="filters.searchTerm" 
              @input="(e) => fetchEntries((e.target as HTMLInputElement).value)"
              @blur="clearSuggestions"
              placeholder="Digite o nome do lançamento"
              class="w-full p-4 border-2 rounded-xl  border-green-100 dark:border-green-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-green-500 dark:focus:ring-green-400 focus:border-transparent" 
            />

            <!-- Loading indicator -->
            <div v-if="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-4">
              <svg class="animate-spin h-5 w-5 text-green-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>

            <!-- Dropdown de sugestões -->
            <div v-show="entrySuggestions.length > 0" class="absolute left-0 right-0 mt-2  rounded-xl shadow-lg z-50 max-h-60 overflow-y-auto border-2 border-green-100 dark:border-green-900">
              <div 
                v-for="entry in entrySuggestions" 
                :key="entry.id"
                @mousedown="selectEntry(entry)"
                class="p-4 hover:bg-green-50 dark:hover:bg-green-900/30 cursor-pointer border-b last:border-b-0 border-green-100 dark:border-green-900 transition-colors"
              >
                <div class="flex justify-between items-center">
                  <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ entry.nome }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ entry.descricao }}</div>
                  </div>
                  <div class="text-right">
                    <div :class="entry.tipo === 'Receita' ? 'text-green-600 dark:text-green-400 font-medium' : 'text-red-600 dark:text-red-400 font-medium'">
                      R$ {{ Math.abs(entry.valor).toFixed(2) }}
                    </div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">{{ entry.tipo }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Lançamento selecionado -->
          <div v-if="selectedEntry" class="mt-6">
            <div class="p-6 bg-gradient-to-r from-green-500/10 to-emerald-500/10 dark:from-green-500/20 dark:to-emerald-500/20 rounded-xl border-2 border-green-200 dark:border-green-800">
              <div class="flex justify-between items-center">
                <div>
                  <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ selectedEntry.nome }}</div>
                  <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ selectedEntry.descricao }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                    {{ selectedEntry.tipo === 'Receita' ? selectedEntry.categoria : selectedEntry.tipoDespesa }}
                  </div>
                </div>
                <div class="text-right">
                  <div :class="selectedEntry.tipo === 'Receita' ? 'text-lg font-bold text-green-600 dark:text-green-400' : 'text-lg font-bold text-red-600 dark:text-red-400'">
                    R$ {{ Math.abs(selectedEntry.valor).toFixed(2) }}
                  </div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">{{ selectedEntry.tipo }}</div>
                </div>
              </div>

              <!-- Botões de exportação para lançamento específico -->
              <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <button
                  v-for="format in exportFormats"
                  :key="format.value"
                  @click="exportReport(format.value, false)"
                  :disabled="isExporting"
                  class="relative group overflow-hidden rounded-xl p-4 border-2 border-green-200 dark:border-green-800 hover:border-green-400 dark:hover:border-green-600  transition-all duration-300"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-green-500/10 to-emerald-500/10 dark:from-green-500/20 dark:to-emerald-500/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
                  <div class="relative flex flex-col items-center">
                    <span class="text-2xl mb-2 transform group-hover:scale-110 transition-transform">{{ format.icon }}</span>
                    <span class="font-medium text-sm text-gray-900 dark:text-gray-100">{{ format.name }}</span>
                  </div>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Seção de Exportação Completa -->
    <div class="rounded-2xl p-1">
      <div class=" backdrop-blur-sm rounded-xl p-8 border border-amber-100 dark:border-amber-900">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h3 class="text-2xl font-bold bg-gradient-to-r from-amber-600 to-orange-600 dark:from-amber-400 dark:to-orange-400 bg-clip-text text-transparent">
              Exportar Todos os Lançamentos
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
              Exporte o histórico completo de todos os lançamentos financeiros
            </p>
          </div>
          <div class="text-5xl">📊</div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
          <button
            v-for="format in exportFormats"
            :key="format.value"
            @click="exportReport(format.value, true)"
            :disabled="isExporting"
            class="relative group overflow-hidden rounded-xl p-6 border-2 border-amber-200 dark:border-amber-800 hover:border-amber-400 dark:hover:border-amber-600  transition-all duration-300"
          >
            <div class="absolute inset-0 bg-gradient-to-r from-amber-500/10 to-orange-500/10 dark:from-amber-500/20 dark:to-orange-500/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
            <div class="relative flex flex-col items-center">
              <span class="text-4xl mb-3 transform group-hover:scale-110 transition-transform">{{ format.icon }}</span>
              <span class="font-medium text-gray-900 dark:text-gray-100">{{ format.name }}</span>
            </div>
          </button>
        </div>
      </div>
    </div>

    <!-- Mensagem de erro -->
    <div v-if="searchError" class="rounded-xl p-4 bg-red-50 dark:bg-red-900/20 border-2 border-red-200 dark:border-red-800">
      <p class="text-sm text-red-600 dark:text-red-400">
        {{ searchError }}
      </p>
    </div>
  </div>
</template>
