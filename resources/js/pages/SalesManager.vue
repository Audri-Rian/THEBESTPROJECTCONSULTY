<script setup lang="ts">
import { ref, computed } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import Title from '@/components/ui/title/Title.vue';
import Input from '@/components/ui/input/Input.vue';
import Button from '@/components/ui/button/Button.vue';
import Table from '@/components/ui/table/Table.vue';
import axios from 'axios';


const breadcrumbs = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Sales Registration', href: '/sales' },
];



const products = ref([{ product_id: '', quantity: 1 }]);
const searchQueries = ref<string[]>(['']);
const suggestions = ref<Record<number, { id: number; name: string; quantity: number; price: number }[]>>({});
const selectedProducts = ref<Record<number, { id: number; name: string; quantity: number; price: number }>>({});


const isLoading = ref(false);
const showConfirmModal = ref(false);
const showErrorModal = ref(false);
const showSuccessModal = ref(false);
const errorMessage = ref('');
const currentStockInfo = ref<{ name: string, available: number, requested: number } | null>(null);


const saleTotal = computed(() => {
  let total = 0;
  for (let i = 0; i < products.value.length; i++) {
    const selectedProduct = selectedProducts.value[i];
    if (selectedProduct) {
      // Garantir que o preço seja tratado como número
      const price = typeof selectedProduct.price === 'number' ? selectedProduct.price :
        typeof selectedProduct.price === 'string' ? parseFloat(selectedProduct.price) : 0;

      total += price * products.value[i].quantity;
    }
  }
  return total.toFixed(2);
});



// Buscar produtos com base na pesquisa
// Buscar produtos com base na pesquisa
const fetchProducts = async (index: number) => {
  const query = searchQueries.value[index];
  if (query.length >= 1) {
    try {
      isLoading.value = true;
      const response = await axios.get('/sales/search-products', {
        params: { search: query },
      });

      // Garantir que cada item da resposta tenha um preço válido
      const processedData = response.data.map(item => {
        return {
          ...item,
          // Converter para número e garantir que não seja nulo/undefined
          price: typeof item.price === 'number' ? item.price :
            typeof item.price === 'string' ? parseFloat(item.price) : 0
        };
      });

      suggestions.value[index] = processedData;
    } catch (error) {
      console.error('Error fetching products:', error);
    } finally {
      isLoading.value = false;
    }
  } else {
    suggestions.value[index] = [];
  }
};

// Selecionar um produto da lista de sugestões
const selectProduct = (product: { id: number; name: string; quantity: number; price: number }, index: number) => {
  console.log('Produto recebido da seleção:', product);
  console.log('Preço original:', product.price, 'Tipo:', typeof product.price);

  // Garantir que o preço seja um número válido
  let validPrice = 0;

  if (typeof product.price === 'number') {
    validPrice = product.price;
  } else if (typeof product.price === 'string') {
    validPrice = parseFloat(product.price);
  }

  // Criar um novo objeto com o preço validado
  const validatedProduct = {
    ...product,
    price: validPrice
  };

  console.log('Preço validado:', validatedProduct.price, 'Tipo:', typeof validatedProduct.price);

  // Atualizar o ID do produto
  products.value[index].product_id = validatedProduct.id.toString();

  // Atualizar a consulta de pesquisa com o nome do produto
  searchQueries.value[index] = validatedProduct.name;

  // Armazenar o produto selecionado com o preço validado
  selectedProducts.value[index] = validatedProduct;

  // Verificar se o preço foi armazenado corretamente
  console.log('Produto armazenado:', selectedProducts.value[index]);
  console.log('Preço armazenado:', selectedProducts.value[index].price);

  // Esconder dropdown mas manter informações do produto
  suggestions.value[index] = [];
};

// Adicionar mais uma linha de produto
const addProduct = () => {
  products.value.push({ product_id: '', quantity: 1 });
  searchQueries.value.push('');
};

// Remover uma linha de produto
const removeProduct = (index: number) => {
  products.value.splice(index, 1);
  searchQueries.value.splice(index, 1);
  delete suggestions.value[index];
  delete selectedProducts.value[index]; // Also remove from selectedProducts

  // Se não houver mais produtos, adicione pelo menos um
  if (products.value.length === 0) {
    addProduct();
  }
};

// Verificar se há estoque suficiente antes de confirmar a venda
const checkStock = () => {
  for (let i = 0; i < products.value.length; i++) {
    const selectedProduct = selectedProducts.value[i];

    if (!selectedProduct || !products.value[i].product_id) {
      errorMessage.value = 'Por favor, selecione todos os produtos antes de prosseguir.';
      showErrorModal.value = true;
      return false;
    }

    if (selectedProduct && products.value[i].quantity > selectedProduct.quantity) {
      currentStockInfo.value = {
        name: selectedProduct.name,
        available: selectedProduct.quantity,
        requested: products.value[i].quantity
      };
      errorMessage.value = `Estoque insuficiente para ${selectedProduct.name}. Disponível: ${selectedProduct.quantity}`;
      showErrorModal.value = true;
      return false;
    }
  }

  // Tudo ok, mostrar modal de confirmação
  showConfirmModal.value = true;
  return true;
};

// Iniciar processo de venda
const startSale = () => {
  if (products.value[0].product_id === '') {
    errorMessage.value = 'Por favor, selecione pelo menos um produto.';
    showErrorModal.value = true;
    return;
  }

  checkStock();
};

// Enviar venda para o servidor
const submitSale = async () => {
  try {
    isLoading.value = true;
    showConfirmModal.value = false;

    const payload = {
      products: products.value,
    };

    const response = await axios.post('/sales', payload);

    showSuccessModal.value = true;

    // Limpar o formulário após sucesso
    products.value = [{ product_id: '', quantity: 1 }];
    searchQueries.value = [''];
    suggestions.value = {};
    selectedProducts.value = {}; // Clear selected products too

  } catch (error) {
    console.error(error);
    errorMessage.value = 'Erro ao registrar a venda. Por favor, tente novamente.';
    showErrorModal.value = true;
  } finally {
    isLoading.value = false;
  }
};

// Verificar estoque em tempo real ao alterar quantidade
const checkQuantity = (index: number) => {
  const selectedProduct = selectedProducts.value[index];
  if (selectedProduct && products.value[index].quantity > selectedProduct.quantity) {
    return { valid: false, available: selectedProduct.quantity };
  }
  return { valid: true };
};

// Verificar se um produto foi selecionado
const isProductSelected = (index: number) => {
  return products.value[index].product_id !== '';
};
</script>

<template>

  <Head title="Sales Manager" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="p-4">
      <div class="border rounded-xl p-5">

        <div class="mb-8 shadow text-white">
          <!-- Todo o conteúdo principal agora está dentro da div com as classes mb-8 shadow text-white -->
          <h1 class="m-2 font-semibold text-xl text-[#3e3e45] dark:text-[#f5f5f5]">Registro de Vendas</h1>
          <p class="m-2 font-medium text-lg text-[#3e3e45]/70 dark:text-[#f5f5f5]/70">Registre todas as suas vendas de forma rápida e eficiente</p>

          <!-- Formulário de vendas -->
          <div class="rounded-lg shadow-md p-6 mb-6 border">
            <h2 class="m-2 font-semibold text-xl text-[#3e3e45] dark:text-[#f5f5f5]">Itens da Venda</h2>

            <!-- Cabeçalho da tabela -->
            <div class="grid grid-cols-12 gap-4 mb-2 font-medium text-[#3e3e45] dark:text-[#f5f5f5] px-2">
              <div class="col-span-5">Produto</div>
              <div class="col-span-2">Quantidade</div>
              <div class="col-span-2">Estoque</div>
              <div class="col-span-2">Preço Unit.</div>
              <div class="col-span-1">Ações</div>
            </div>

            <!-- Lista de produtos -->
            <div v-for="(product, index) in products" :key="index"
              class="grid grid-cols-12 gap-4 items-center mb-3 py-3 px-2 rounded-lg">

              <!-- Campo de pesquisa de produto -->
              <div class="relative col-span-5">
                <input type="text" v-model="searchQueries[index]" @input="fetchProducts(index)"
                  placeholder="Pesquisar produto..."
                  class="w-full p-3 border-black bg-transparent rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-[#fafafa] text-[#1a1a1a] dark:bg-[#121212] dark:text-[#f5f5f5]" />

                <!-- Indicador de produto selecionado -->
                <div v-if="isProductSelected(index)" class="absolute right-10 top-3 text-green-500">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                </div>

                <!-- Indicador de carregamento -->
                <div v-if="isLoading" class="absolute right-3 top-3">
                  <div class="animate-spin rounded-full h-5 w-5 border-b-2 border-blue-400"></div>
                </div>

                <!-- Lista de sugestões -->
                <div v-if="suggestions[index]?.length"
                  class="absolute bg-gray-700 border border-gray-700 rounded-lg w-full mt-1 z-20 shadow-lg max-h-60 overflow-y-auto">
                  <div v-for="suggestion in suggestions[index]" :key="suggestion.id"
                    class="p-3 hover:bg-gray-700 cursor-pointer border-b border-gray-700 flex justify-between"
                    @click="selectProduct(suggestion, index)">
                    <div>
                      <div class="font-medium text-white">{{ suggestion.name }}</div>
                      <div class="text-sm text-gray-400">ID: {{ suggestion.id }}</div>
                    </div>
                    <div class="text-right">
                      <div class="font-medium text-blue-400">
                        R$ {{ typeof suggestion.price === 'number' ? suggestion.price.toFixed(2) :
                          typeof suggestion.price === 'string' ? parseFloat(suggestion.price).toFixed(2) : '0.00' }}
                      </div>
                      <div class="text-sm" :class="suggestion.quantity > 0 ? 'text-green-400' : 'text-red-400'">
                        Estoque: {{ suggestion.quantity }}
                      </div>
                    </div>
                  </div>
                </div>
              </div>


              <!-- Campo de quantidade -->
              <div class="col-span-2">
                <input type="number" v-model.number="products[index].quantity" min="1"
                  class="w-full p-3 bg-transparent rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-[#fafafa] text-[#1a1a1a] dark:bg-[#121212] dark:text-[#f5f5f5]" />
              </div>

              <!-- Informação de estoque -->
              <div class="col-span-2 text-center">
                <div v-if="selectedProducts[index]">
                  <span :class="checkQuantity(index).valid ? 'bg-green-900 text-green-400' : 'bg-red-900 text-red-400'"
                    class="px-3 py-1 rounded-full text-sm font-medium">
                    {{ selectedProducts[index].quantity }} unid.
                  </span>
                </div>
                <div v-else class="text-gray-500 text-sm">--</div>
              </div>

              <!-- Preço unitário -->
              <div class="col-span-2 text-center">
                <div v-if="selectedProducts[index]" class="w-full p-3 bg-transparent rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all bg-[#fafafa] text-[#1a1a1a] dark:bg-[#121212] dark:text-[#f5f5f5]">
                  R$ {{ typeof selectedProducts[index].price === 'number' ?
                    selectedProducts[index].price.toFixed(2) :
                    typeof selectedProducts[index].price === 'string' ?
                      parseFloat(selectedProducts[index].price).toFixed(2) : '0.00' }}
                </div>
                <div v-else class="text-gray-500 text-sm">--</div>
              </div>

              <!-- Botão remover -->
              <div class="col-span-1 text-right">
                <button @click="removeProduct(index)"
                  class="bg-red-900 text-red-400 p-2 rounded-lg hover:bg-red-800 transition-colors"
                  title="Remover item">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                  </svg>
                </button>
              </div>
            </div>

            <!-- Botões de ação e total da venda -->
            <div class="flex justify-between items-center mt-6">
              <button @click="addProduct"
                class="bg-transparent text-[#3e3e45] dark:text-[#f5f5f5] px-4 py-2 rounded-lg flex items-center hover:bg-gray-700 transition-colors border">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                  stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Adicionar Produto
              </button>

              <div class="flex items-center space-x-4">
                <!-- Total da Venda -->
                <div class="px-4 py-2 rounded-lg shadow-sm">
                  <span class="text-sm font-medium m-2 text-[#3e3e45] dark:text-[#f5f5f5]">Total da Venda:</span>
                  <span class="text-xl font-bold text-[#3e3e45] dark:text-[#f5f5f5]">R$ {{ saleTotal }}</span>
                </div>

                <button @click="startSale"
                  class="bg-blue-600 text-white px-6 py-2 rounded-lg flex items-center hover:bg-blue-700 transition-colors font-medium"
                  :disabled="isLoading">
                  <svg v-if="!isLoading" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                  </svg>
                  <div v-else class="animate-spin rounded-full h-5 w-5 border-t-2 border-white mr-2"></div>
                  Finalizar Venda
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Modal de confirmação -->
    <div v-if="showConfirmModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
      <div class="bg-gray-900 rounded-lg shadow-xl p-6 w-full max-w-md mx-4 border border-gray-800">
        <h3 class="text-xl font-bold mb-4 text-gray-200">Confirmar Venda</h3>

        <div class="mb-6">
          <p class="text-gray-400 mb-4">Você está prestes a registrar uma venda com os seguintes itens:</p>

          <div class="bg-gray-800 p-4 rounded-lg mb-4">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-gray-700">
                  <th class="text-left py-2 text-gray-300">Produto</th>
                  <th class="text-center py-2 text-gray-300">Qtd</th>
                  <th class="text-right py-2 text-gray-300">Subtotal</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="(product, index) in products" :key="index" class="border-b border-gray-700">
                  <td class="py-2 text-gray-300">{{ searchQueries[index] }}</td>
                  <td class="text-center py-2 text-gray-300">{{ product.quantity }}</td>
                  <td class="text-right py-2 text-blue-400">
                    R$ {{ selectedProducts[index] ?
                      (typeof selectedProducts[index].price === 'number' ?
                        (selectedProducts[index].price * product.quantity).toFixed(2) :
                        typeof selectedProducts[index].price === 'string' ?
                          (parseFloat(selectedProducts[index].price) * product.quantity).toFixed(2) : '0.00') : '0.00' }}
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr class="font-bold">
                  <td class="py-2 text-gray-300" colspan="2">Total</td>
                  <td class="text-right py-2 text-blue-400">R$ {{ saleTotal }}</td>
                </tr>
              </tfoot>
            </table>
          </div>

          <p class="text-gray-400">Deseja confirmar esta venda?</p>
        </div>

        <div class="flex justify-end space-x-3">
          <button @click="showConfirmModal = false"
            class="px-4 py-2 border border-gray-700 rounded-lg text-gray-300 hover:bg-gray-800 transition-colors">
            Cancelar
          </button>
          <button @click="submitSale"
            class="px-4 py-2 bg-green-700 text-white rounded-lg hover:bg-green-600 transition-colors">
            Confirmar Venda
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de erro -->
    <div v-if="showErrorModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
      <div class="bg-gray-900 rounded-lg shadow-xl p-6 w-full max-w-md mx-4 border border-gray-800">
        <div class="flex items-center justify-center bg-red-900 rounded-full w-16 h-16 mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
        </div>

        <h3 class="text-xl font-bold mb-2 text-center text-gray-200">Erro na Venda</h3>
        <p class="text-gray-400 text-center mb-4">{{ errorMessage }}</p>

        <!-- Informações de estoque caso seja necessário -->
        <div v-if="currentStockInfo" class="bg-red-900 bg-opacity-30 p-4 rounded-lg mb-4 border border-red-800">
          <h4 class="font-medium text-red-400 mb-2">Estoque Insuficiente</h4>
          <p class="text-sm text-red-300">Produto: {{ currentStockInfo.name }}</p>
          <div class="flex justify-between text-sm mt-2 text-gray-300">
            <p>Quantidade solicitada: <span class="font-bold text-white">{{ currentStockInfo.requested }}</span></p>
            <p>Disponível em estoque: <span class="font-bold text-white">{{ currentStockInfo.available }}</span></p>
          </div>
        </div>

        <div class="text-center">
          <button @click="showErrorModal = false"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
            Entendi
          </button>
        </div>
      </div>
    </div>

    <!-- Modal de sucesso -->
    <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-50">
      <div class="bg-gray-900 rounded-lg shadow-xl p-6 w-full max-w-md mx-4 border border-gray-800">
        <div class="flex items-center justify-center bg-green-900 rounded-full w-16 h-16 mx-auto mb-4">
          <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-green-400" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
        </div>

        <h3 class="text-xl font-bold mb-2 text-center text-gray-200">Venda Registrada!</h3>
        <p class="text-gray-400 text-center mb-6">Sua venda foi registrada com sucesso no sistema.</p>

        <div class="text-center">
          <button @click="showSuccessModal = false"
            class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
            Continuar
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
input[type="number"]::-webkit-inner-spin-button,
input[type="number"]::-webkit-outer-spin-button {
  opacity: 1;
}
</style>