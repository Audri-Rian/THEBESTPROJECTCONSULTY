<script setup lang="ts">
import { ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';

const isVisible = ref(false);

const openModal = () => {
    isVisible.value = true;
};

const closeModal = () => {
    isVisible.value = false;
};

const confirmAction = () => {
    closeModal();
};

const handleOutsideClick = (event: MouseEvent) => {
    const modalContent = event.target as HTMLElement;
    if (modalContent && !modalContent.closest('.modal-content')) {
        closeModal();
    }
};

defineExpose({
    openModal,
    closeModal
});
</script>

<template>
    <div v-if="isVisible"
         class="fixed inset-0 bg-black bg-opacity-80 flex justify-center items-center z-50"
         @click="handleOutsideClick">
        <div class="bg-white dark:bg-[#0a0a0a] border rounded-xl w-2/4 p-6 modal-content" @click.stop>
            <div>
                <div class="border-b pb-2 mb-2">
                    <slot name="title"></slot>
                </div>

                <slot name="body"></slot>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal-content {
    cursor: pointer;
}
</style>
