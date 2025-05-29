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
    <div class="bg-white dark:bg-gray-800 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
      <h3 class="text-lg font-medium mb-4 text-gray-900 dark:text-gray-100">Buscar Produto Específico</h3>
      <div class="w-full">
        <label class="block text-sm font-medium mb-2 text-gray-700 dark:text-gray-300">Nome do Produto</label>
        <div class="relative w-full">
          <input 
            type="text" 
            v-model="filters.productName" 
            @input="(e) => fetchProducts((e.target as HTMLInputElement).value)"
            @blur="clearSuggestions"
            placeholder="Digite o nome do produto"
            class="w-full p-3 border rounded-lg dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 focus:ring-2 focus:ring-blue-500 dark:focus:ring-blue-400" 
          />

          <!-- Loading indicator -->
          <div v-if="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-3">
            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
          </div>

          <!-- Dropdown de sugestões -->
          <div v-show="productSuggestions.length > 0" class="absolute left-0 right-0 mt-1 bg-white dark:bg-gray-700 rounded-lg shadow-lg z-50 max-h-60 overflow-y-auto border border-gray-200 dark:border-gray-600">
            <div 
              v-for="product in productSuggestions" 
              :key="product.id"
              @mousedown="selectProduct(product)"
              class="p-3 hover:bg-gray-50 dark:hover:bg-gray-600 cursor-pointer border-b last:border-b-0 border-gray-200 dark:border-gray-600"
            >
              <div class="flex justify-between items-center">
                <div>
                  <div class="font-medium text-gray-900 dark:text-gray-100">{{ product.name }}</div>
                  <div class="text-sm text-gray-500 dark:text-gray-400">ID: {{ product.id }}</div>
                </div>
                <div class="text-right">
                  <div class="text-blue-600 dark:text-blue-400">R$ {{ product.price.toFixed(2) }}</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Produto selecionado -->
        <div v-if="selectedProduct" class="mt-4">
          <div class="p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg border border-blue-200 dark:border-blue-800">
            <div class="flex justify-between items-center">
              <div>
                <div class="font-medium text-gray-900 dark:text-gray-100">{{ selectedProduct.name }}</div>
                <div class="text-sm text-gray-600 dark:text-gray-400">ID: {{ selectedProduct.id }}</div>
              </div>
              <div class="text-blue-600 dark:text-blue-400">R$ {{ selectedProduct.price.toFixed(2) }}</div>
            </div>
          </div>

          <!-- Botões de exportação para produto específico -->
          <div class="mt-4">
            <h4 class="text-sm font-medium mb-3 text-gray-700 dark:text-gray-300">Exportar Histórico deste Produto</h4>
            <div class="flex flex-wrap gap-2">
              <button
                v-for="format in exportFormats"
                :key="format.value"
                @click="exportReport(format.value, false)"
                :disabled="isExporting"
                class="inline-flex items-center px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 dark:focus:ring-blue-400 transition-colors"
              >
                <span class="mr-2">{{ format.icon }}</span>
                <span>{{ format.name }}</span>
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Seção de Exportação Completa -->
    <div class="bg-gradient-to-br from-gray-50 to-white dark:from-gray-800 dark:to-gray-700 rounded-lg p-6 border border-gray-200 dark:border-gray-700">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Exportar Todo o Histórico</h3>
          <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
            Exporte o histórico completo de todos os produtos
          </p>
        </div>
        <div class="text-4xl">📊</div>
      </div>

      <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
        <button
          v-for="format in exportFormats"
          :key="format.value"
          @click="exportReport(format.value, true)"
          :disabled="isExporting"
          class="flex flex-col items-center justify-center p-4 bg-white dark:bg-gray-700 border-2 border-gray-200 dark:border-gray-600 rounded-xl hover:border-blue-500 dark:hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition-all group"
        >
          <span class="text-3xl mb-2 group-hover:scale-110 transition-transform">{{ format.icon }}</span>
          <span class="font-medium text-gray-900 dark:text-gray-100">{{ format.name }}</span>
        </button>
      </div>
    </div>

    <!-- Mensagem de erro -->
    <div v-if="searchError" class="mt-2 p-4 bg-red-50 dark:bg-red-900/20 rounded-lg border border-red-200 dark:border-red-800">
      <p class="text-sm text-red-600 dark:text-red-400">
        {{ searchError }}
      </p>
    </div>
  </div>
</template>