<script setup lang="ts">
import { ref, watch, onMounted } from 'vue';
import { debounce } from 'lodash-es';

interface Product {
  id: number;
  name: string;
  price: number;
  type: string;
}

const props = defineProps<{
  modelValue: object
}>();

const emit = defineEmits<{
  (e: 'update:modelValue', value: typeof filters.value): void
  (e: 'error', error: string): void
}>();

const filters = ref({
  productName: ''
});

const productSuggestions = ref<Product[]>([]);
const selectedProduct = ref<Product | null>(null);
const searchError = ref<string | null>(null);
const isLoading = ref(false);

const selectProduct = (product: Product) => {
  selectedProduct.value = product;
  filters.value.productName = product.name;
  productSuggestions.value = [];
};

watch(() => filters.value, (newValue) => {
  emit('update:modelValue', newValue);
}, { deep: true });

const clearSuggestions = () => {
  setTimeout(() => {
    productSuggestions.value = [];
  }, 200);
};
</script>

<template>
  <div class="space-y-4">
    <h3 class="text-lg font-medium mb-4 dark:text-gray-300">Filtros do Histórico de Produtos</h3>
    <div class="grid grid-cols-1 gap-4">
      <!-- Filtro de Nome -->
      <div>
        <label class="block text-sm font-medium mb-2 dark:text-gray-400">Nome do Produto</label>
        <div class="relative">
          <input 
            type="text" 
            v-model="filters.productName" 
            @input="fetchSuggestions(filters.productName)"
            @blur="clearSuggestions"
            placeholder="Digite o nome"
            class="w-full p-2 border rounded-md dark:bg-gray-800 dark:border-gray-700 dark:text-gray-300" 
          />
          <div v-if="isLoading" class="absolute inset-y-0 right-0 flex items-center pr-3">
            <svg class="animate-spin h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none"
              viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor"
                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
              </path>
            </svg>
          </div>
        </div>

        <div v-if="productSuggestions.length > 0" 
             class="mt-1 absolute z-10 w-full md:w-1/3 bg-white dark:bg-gray-800 border rounded-md shadow-lg max-h-60 overflow-y-auto">
          <div v-for="product in productSuggestions" 
               :key="product.id" 
               @mousedown="selectProduct(product)"
               class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer">
            {{ product.name }} (R$ {{ product.price.toFixed(2) }})
          </div>
        </div>

        <div v-if="selectedProduct" class="mt-2 p-2 bg-gray-100 dark:bg-gray-700 rounded-md">
          <p class="text-sm">Preço atual: R$ {{ selectedProduct.price.toFixed(2) }}</p>
        </div>

        <div v-if="searchError" class="mt-2 text-sm text-red-600 dark:text-red-400">
          {{ searchError }}
        </div>
      </div>
    </div>
  </div>
</template>