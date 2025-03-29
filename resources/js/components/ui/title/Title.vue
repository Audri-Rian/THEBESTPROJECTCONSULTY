<script setup lang="ts">
const props = defineProps<{
    title: string;
    level?: 1 | 2 | 3 | 4;
}>();
</script>

<template>
    <div>
        <component
            :is="'h' + (level || 1)"
            class="m-2"
            :class="{
                'font-semibold text-xl text-[#3e3e45] dark:text-[#f5f5f5]': level === 1,
                'font-medium text-lg text-[#3e3e45]/70 dark:text-[#f5f5f5]/70': level === 2,
                'font-normal text-base text-[#3e3e45]/50 dark:text-[#f5f5f5]/50': level === 3,
                'font-light text-sm text-[#3e3e45]/40 dark:text-[#f5f5f5]/40': level === 4,
            }"
        >
            {{ title }}
        </component>
    </div>
</template>

<style scoped></style>
