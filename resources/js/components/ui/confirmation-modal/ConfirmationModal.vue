<script setup lang="ts">
import { ref, defineProps, defineEmits, defineExpose } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import Title from '@/components/ui/title/Title.vue';

const props = defineProps<{
  title?: string;
  message?: string;
  type?: 'default' | 'warning' | 'info' | 'error' | 'success';
  confirmText?: string;
  cancelText?: string;
  showCancel?: boolean;
}>();

const emits = defineEmits<{
  (e: 'confirm'): void;
  (e: 'cancel'): void;
}>();

const visible = ref(false);
const modalTitle = ref(props.title || 'Confirmation');
const modalMessage = ref(props.message || 'Are you sure you want to proceed?');
const modalType = ref(props.type || 'default');
const confirmButtonText = ref(props.confirmText || 'Confirm');
const cancelButtonText = ref(props.cancelText || 'Cancel');

const openModal = (config?: {
  title?: string;
  message?: string;
  type?: 'default' | 'warning' | 'info' | 'error' | 'success';
  confirmText?: string;
  cancelText?: string;
}) => {
  if (config) {
    if (config.title) modalTitle.value = config.title;
    if (config.message) modalMessage.value = config.message;
    if (config.type) modalType.value = config.type;
    if (config.confirmText) confirmButtonText.value = config.confirmText;
    if (config.cancelText) cancelButtonText.value = config.cancelText;
  }
  visible.value = true;
};

const closeModal = () => {
  visible.value = false;
};

const handleConfirm = () => {
  emits('confirm');
  closeModal();
};

const handleCancel = () => {
  emits('cancel');
  closeModal();
};

defineExpose({
  openModal,
  closeModal
});

const getIconByType = () => {
  switch (modalType.value) {
    case 'warning':
      return {
        bgColor: 'bg-yellow-50',
        textColor: 'text-yellow-700',
        iconColor: 'text-yellow-400',
        path: 'M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z'
      };
    case 'error':
      return {
        bgColor: 'bg-red-50',
        textColor: 'text-red-700',
        iconColor: 'text-red-400',
        path: 'M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z'
      };
    case 'success':
      return {
        bgColor: 'bg-green-50',
        textColor: 'text-green-700',
        iconColor: 'text-green-400',
        path: 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z'
      };
    case 'info':
    default:
      return {
        bgColor: 'bg-blue-50',
        textColor: 'text-blue-700',
        iconColor: 'text-blue-400',
        path: 'M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9a1 1 0 00-1-1z'
      };
  }
};
</script>

<template>
  <Teleport to="body">
    <div v-if="visible" class="fixed inset-0 flex items-center justify-center z-50">
      <!-- Backdrop -->
      <div class="fixed inset-0 bg-black/70" @click="handleCancel"></div>
      
      <!-- Modal content -->
      <div class="relative bg-background dark:bg-background border rounded-lg shadow-xl max-w-md w-full mx-4 overflow-hidden">
        <div class="px-6 py-4">
          <div class="flex justify-between items-center mb-4">
            <Title :title="modalTitle" :level="1" class="text-xl font-semibold" />
            <button 
              @click="handleCancel" 
              class="text-gray-400 hover:text-gray-500 focus:outline-none"
            >
              <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
              </svg>
            </button>
          </div>
          
          <div class="space-y-4">
            <div 
              class="rounded-md p-4" 
              :class="getIconByType().bgColor"
            >
              <div class="flex">
                <div class="flex-shrink-0">
                  <svg class="h-5 w-5" :class="getIconByType().iconColor" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd" :d="getIconByType().path" clip-rule="evenodd" />
                  </svg>
                </div>
                <div class="ml-3 flex-1 md:flex md:justify-between">
                  <p class="text-sm" :class="getIconByType().textColor">
                    {{ modalMessage }}
                  </p>
                </div>
              </div>
            </div>
            
            <!-- Slot for additional content -->
            <slot name="body"></slot>
            
            <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
              <Button 
                v-if="props.showCancel !== false" 
                type="button" 
                @click="handleCancel" 
                variant="outline"
              >
                {{ cancelButtonText }}
              </Button>
              <Button 
                type="button" 
                @click="handleConfirm" 
                :variant="modalType === 'error' ? 'destructive' : 'default'"
              >
                {{ confirmButtonText }}
              </Button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>