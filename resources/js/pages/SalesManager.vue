<script setup lang="ts">
import { ref } from 'vue';
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
const suggestions = ref<Record<number, { id: number; name: string; quantity: number }[]>>({});

const fetchProducts = async (index: number) => {
  const query = searchQueries.value[index];
  if (query.length >= 1) {
    const response = await axios.get('/sales/search-products', {
      params: { search: query },
    });
    suggestions.value[index] = response.data;
  } else {
    suggestions.value[index] = [];
  }
};

const selectProduct = (product: { id: number; name: string; quantity: number }, index: number) => {
  products.value[index].product_id = product.id.toString();
  searchQueries.value[index] = product.name;
  suggestions.value[index] = [product]; 
  suggestions.value[index] = [];
};


const addProduct = () => {
  products.value.push({ product_id: '', quantity: 1 });
  searchQueries.value.push('');
};

const removeProduct = (index: number) => {
  products.value.splice(index, 1);
  searchQueries.value.splice(index, 1);
  delete suggestions.value[index];
};

const submitSale = async () => {
  try {
    for (let i = 0; i < products.value.length; i++) {
      const selectedProduct = suggestions.value[i]?.[0];

      if (selectedProduct && products.value[i].quantity > selectedProduct.quantity) {
        alert(`Not enough stock for ${selectedProduct.name}. Available: ${selectedProduct.quantity}`);
        return;
      }
    }

    const payload = {
      products: products.value,
    };

    const response = await axios.post('/sales', payload);

    alert('Sale submitted successfully.');
    
    products.value = [{ product_id: '', quantity: 1 }];
    searchQueries.value = [''];
    suggestions.value = {};
  } catch (error) {
    console.error(error);
    alert('Error submitting sale.');
  }
};
</script>

<template>
  <AppLayout>

    <Head title="Sales Manager" />

    <div class="p-4">

      <h1 class="text-2xl font-bold mb-4">Sales Registration</h1>
      <p class="mb-6">Here you will make all sales records</p>

      <div v-for="(product, index) in products" :key="index" class="flex space-x-2 items-center mb-4">
        <div class="relative w-1/2">
          <input type="text" v-model="searchQueries[index]" @input="fetchProducts(index)"
            placeholder="Search product..." class="rounded-md border-gray-600 bg-gray-700 text-white p-2 w-full" />

          <div v-if="suggestions[index]?.length" class="absolute bg-white border rounded w-full mt-1 z-10">
            <div v-for="suggestion in suggestions[index]" :key="suggestion.id"
              class="p-2 hover:bg-blue-100 cursor-pointer text-black rounded-md shadow-md mb-1 bg-white"
              @click="selectProduct(suggestion, index)">
              {{ suggestion.name }}
            </div>
          </div>
        </div>

        <input type="number" v-model.number="products[index].quantity" class="rounded-md border-gray-600 bg-gray-700 text-white p-2 w-20"
          min="1">

        <button @click="removeProduct(index)" class="bg-red-700 text-white p-2 rounded-md">
          Remove
        </button>
      </div>

      <div class="flex space-x-4">
        <button @click="addProduct" class="bg-blue-700 text-white p-2 rounded-md">
          Add Another Product
        </button>
        <button @click="submitSale" class="bg-green-700 text-white p-2 rounded-md">
          Submit Sale
        </button>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
.z-10 {
  z-index: 10;
}
</style>
