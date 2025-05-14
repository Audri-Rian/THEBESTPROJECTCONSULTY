<script setup lang="ts">
import { ref, defineProps, defineExpose, onMounted } from 'vue';

const props = defineProps<{
  message?: string;
  type?: 'success' | 'error' | 'warning' | 'info';
  duration?: number;
  autoClose?: boolean;
}>();

const visible = ref(false);
const notificationMessage = ref(props.message || '');
const notificationType = ref(props.type || 'success');
let timer: number | null = null;

const showNotification = (message: string, type: 'success' | 'error' | 'warning' | 'info' = 'success', duration: number = 3000) => {
  notificationMessage.value = message;
  notificationType.value = type;
  visible.value = true;
  
  if (props.autoClose !== false) {
    if (timer) clearTimeout(timer);
    timer = window.setTimeout(() => {
      visible.value = false;
    }, duration);
  }
};

const closeNotification = () => {
  visible.value = false;
  if (timer) clearTimeout(timer);
};

onMounted(() => {
  if (props.message) {
    showNotification(props.message, props.type || 'success', props.duration || 3000);
  }
});

defineExpose({
  showNotification,
  closeNotification
});
</script>

<template>
  <Teleport to="body">
    <div v-if="visible" class="fixed top-4 right-4 z-50 p-4 rounded-md shadow-md transition-all duration-300 flex items-center"
      :class="{
        'bg-green-100 border-l-4 border-green-500 text-green-700': notificationType === 'success',
        'bg-red-100 border-l-4 border-red-500 text-red-700': notificationType === 'error',
        'bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700': notificationType === 'warning',
        'bg-blue-100 border-l-4 border-blue-500 text-blue-700': notificationType === 'info'
      }">
      <!-- Icon based on notification type -->
      <div class="flex-shrink-0 mr-3">
        <svg v-if="notificationType === 'success'" class="h-5 w-5 text-green-500" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
        </svg>
        <svg v-else-if="notificationType === 'error'" class="h-5, w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
        </svg>
        <svg v-else-if="notificationType === 'warning'" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
        <svg v-else-if="notificationType === 'info'" class="h-5 w-5 text-blue-500" viewBox="0 0 20 20" fill="currentColor">
          <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z" clip-rule="evenodd" />
        </svg>
      </div>
      
      <div class="mr-4">{{ notificationMessage }}</div>
      
      <button @click="closeNotification" class="ml-auto text-gray-500 hover:text-gray-700">
        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
      </button>
    </div>
  </Teleport>
</template>