<script setup lang="ts">
defineProps<{
    headers: string[];
    data: Record<string, any>[];
}>();
</script>

<template>
    <div class="overflow-x-auto border rounded-xl">
        <table class="w-full rounded-lg shadow-md">
            <thead class="bg-gradient-to-r from-blue-600/90 to-purple-600/90 text-white">
                <tr>
                    <th v-for="(header, index) in headers" :key="index" class="py-3 px-4 text-left">
                        {{ header }}
                    </th>
                </tr>
            </thead>

            <tbody class="bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200">
                <tr v-for="(row, rowIndex) in data" :key="rowIndex" class="border-t border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
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
