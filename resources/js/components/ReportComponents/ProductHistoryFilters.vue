<script setup lang="ts">
import { ref } from 'vue';
import { debounce } from 'lodash-es';
import axios from 'axios';

interface Product {
  id: number;
  name: string;
  price: number;
  supplier?: {
    name: string;
  };
}

const props = defineProps<{
  modelValue: object
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: typeof filters.value): void
}>();

const filters = ref({
  productName: ''
});

const productSuggestions = ref<Product[]>([]);
const selectedProduct = ref<Product | null>(null);
const searchError = ref<string | null>(null);
const isLoading = ref(false);
const isExporting = ref(false);

const exportFormats = [
  { name: 'Excel', value: 'xlsx', icon: '📊' },
  { name: 'CSV', value: 'csv', icon: '📋' },
  { name: 'PDF', value: 'pdf', icon: '📄' },
  { name: 'JSON', value: 'json', icon: '🔩' }
];

// Função para buscar sugestões de produtos
const fetchProducts = debounce(async (value: string) => {
  if (value.length >= 1) {
    try {
      isLoading.value = true;
      const response = await axios.get('/sales/search-products', {
        params: { search: value },
      });
      
      productSuggestions.value = response.data;
    } catch (error) {
      console.error('Erro ao buscar produtos:', error);
      searchError.value = 'Erro ao buscar produtos. Tente novamente.';
    } finally {
      isLoading.value = false;
    }
  } else {
    productSuggestions.value = [];
  }
}, 300);

const selectProduct = (product: Product) => {
  selectedProduct.value = product;
  filters.value.productName = product.name;
  emit('update:modelValue', filters.value);
  productSuggestions.value = [];
};

const clearSuggestions = () => {
  setTimeout(() => {
    productSuggestions.value = [];
  }, 200);
};

const exportReport = async (format: string, exportAll: boolean = false) => {
  try {
    isExporting.value = true;
    
    const response = await axios.post('/reports/export-products', {
      format,
      productId: exportAll ? null : selectedProduct.value?.id
    }, {
      responseType: format === 'json' ? 'json' : 'blob'
    });

    // Para JSON, apenas baixa o arquivo
    if (format === 'json') {
      const blob = new Blob([JSON.stringify(response.data, null, 2)], { type: 'application/json' });
      const url = window.URL.createObjectURL(blob);
      const link = document.createElement('a');
      link.href = url;
      link.download = `historico-produtos${exportAll ? '' : '-' + selectedProduct.value?.name}.json`;
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
    link.download = `historico-produtos${exportAll ? '' : '-' + selectedProduct.value?.name}.${format}`;
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
    <!-- Seção de Busca de Produto -->
    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-1">
      <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl p-8 border border-blue-100 dark:border-blue-900">
        <div class="flex items-center mb-6">
          <div class="flex-1">
            <h3 class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">
              Exportar Produto Específico
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Busque e exporte dados de um produto individual</p>
          </div>
          <div class="text-4xl">🔍</div>
        </div>

        <div class="w-full">
          <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Nome do Produto</label>
          <div class="relative w-full">
            <input 
              type="text" 
              v-model="filters.productName" 
              @input="(e) => fetchProducts((e.target as HTMLInputElement).value)"
              @blur="clearSuggestions"
              placeholder="Digite o nome do produto"
              class="w-full p-4 border-2 rounded-xl bg-white dark:bg-gray-700 border-blue-100 dark:border-blue-900 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400 focus:border-transparent" 
            />

            <!-- Loading indicator -->
            <div v-if="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-4">
              <svg class="animate-spin h-5 w-5 text-blue-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
            </div>

            <!-- Dropdown de sugestões -->
            <div v-show="productSuggestions.length > 0" class="absolute left-0 right-0 mt-2 bg-white dark:bg-gray-700 rounded-xl shadow-lg z-50 max-h-60 overflow-y-auto border-2 border-blue-100 dark:border-blue-900">
              <div 
                v-for="product in productSuggestions" 
                :key="product.id"
                @mousedown="selectProduct(product)"
                class="p-4 hover:bg-blue-50 dark:hover:bg-blue-900/30 cursor-pointer border-b last:border-b-0 border-blue-100 dark:border-blue-900 transition-colors"
              >
                <div class="flex justify-between items-center">
                  <div>
                    <div class="font-medium text-gray-900 dark:text-gray-100">{{ product.name }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ product.id }}</div>
                  </div>
                  <div class="text-right">
                    <div class="text-blue-600 dark:text-blue-400 font-medium">R$ {{ product.price.toFixed(2) }}</div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Produto selecionado -->
          <div v-if="selectedProduct" class="mt-6">
            <div class="p-6 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 dark:from-blue-500/20 dark:to-indigo-500/20 rounded-xl border-2 border-blue-200 dark:border-blue-800">
              <div class="flex justify-between items-center">
                <div>
                  <div class="text-xl font-semibold text-gray-900 dark:text-gray-100">{{ selectedProduct.name }}</div>
                  <div class="text-sm text-gray-600 dark:text-gray-400 mt-1">ID: {{ selectedProduct.id }}</div>
                </div>
                <div class="text-right">
                  <div class="text-lg font-bold text-blue-600 dark:text-blue-400">
                    R$ {{ selectedProduct.price.toFixed(2) }}
                  </div>
                </div>
              </div>

              <!-- Botões de exportação para produto específico -->
              <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3">
                <button
                  v-for="format in exportFormats"
                  :key="format.value"
                  @click="exportReport(format.value, false)"
                  :disabled="isExporting"
                  class="relative group overflow-hidden rounded-xl p-4 border-2 border-blue-200 dark:border-blue-800 hover:border-blue-400 dark:hover:border-blue-600 bg-white dark:bg-gray-800 transition-all duration-300"
                >
                  <div class="absolute inset-0 bg-gradient-to-r from-blue-500/10 to-indigo-500/10 dark:from-blue-500/20 dark:to-indigo-500/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
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
    <div class="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-gray-800 dark:to-gray-700 rounded-2xl p-1">
      <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-sm rounded-xl p-8 border border-purple-100 dark:border-purple-900">
        <div class="flex items-center justify-between mb-8">
          <div>
            <h3 class="text-2xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-400 dark:to-pink-400 bg-clip-text text-transparent">
              Exportar Todo o Histórico
            </h3>
            <p class="text-gray-600 dark:text-gray-400 mt-1">
              Exporte o histórico completo de todos os produtos
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
            class="relative group overflow-hidden rounded-xl p-6 border-2 border-purple-200 dark:border-purple-800 hover:border-purple-400 dark:hover:border-purple-600 bg-white dark:bg-gray-800 transition-all duration-300"
          >
            <div class="absolute inset-0 bg-gradient-to-r from-purple-500/10 to-pink-500/10 dark:from-purple-500/20 dark:to-pink-500/20 opacity-0 group-hover:opacity-100 transition-opacity"></div>
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