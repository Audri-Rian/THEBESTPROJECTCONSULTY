<script setup lang="ts">
defineProps<{
    headers: string[];
    data: Record<string, any>[];
}>();
</script>

<template>
    <div class="overflow-x-auto border rounded-xl">
        <table class="w-full rounded-lg shadow-md">
            <thead class="bg-[#fafafa] text-[#1a1a1a] dark:bg-[#121212] dark:text-[#f5f5f5]">
            <tr>
                <th v-for="(header, index) in headers" :key="index" class="py-3 px-4 text-left">
                    {{ header }}
                </th>
            </tr>
            </thead>

            <tbody class="dark:text-white">
            <tr v-for="(row, rowIndex) in data" :key="rowIndex" class="border-t">
                <td v-for="(header, colIndex) in headers" :key="colIndex" class="py-2 px-4">
                    <template v-if="header === 'Ação'">
                        <slot name="action" :row="row"></slot>
                    </template>
                    <template v-else>
                        {{ row[header] ?? "-" }}
                    </template>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>
table {
    border-radius: 8px;
    overflow: hidden;
}
</style>
