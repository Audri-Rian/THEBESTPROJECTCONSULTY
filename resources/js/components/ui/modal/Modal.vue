<script setup lang="ts">
import { ref } from 'vue';
import Button from '@/components/ui/button/Button.vue';
import { Title } from '@/components/ui/title';

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

defineExpose({
    openModal,
    closeModal
});
</script>

<template>
    <div v-if="isVisible" class="fixed inset-0 bg-black bg-opacity-80 flex justify-center items-center z-50">
        <div class="bg-[#0a0a0a] border rounded-xl w-96 p-6">
            <div>
                <div class="border-b pb-2 mb-2">
                    <slot name="title"></slot>
                </div>

                <slot name="body"></slot>
            </div>
            <div class="mt-4 flex justify-end space-x-2 border-t pt-2">
                <Button variant="default" @click="closeModal">Close</Button>
                <Button variant="default" @click="confirmAction">Confirm</Button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>
