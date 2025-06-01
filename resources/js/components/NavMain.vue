<script setup lang="ts">
import { SidebarGroup, SidebarGroupLabel, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type SharedData } from '@/types';
import { Link, usePage } from '@inertiajs/vue3';

defineProps<{
    items: NavItem[];
}>();

const page = usePage<SharedData>();
</script>

<template>
    <SidebarGroup class="px-2 py-0">
        <SidebarMenu>
            <SidebarMenuItem v-for="item in items" :key="item.title">
                <SidebarMenuButton 
                    as-child 
                    :is-active="item.href === page.url"
                    class="relative overflow-hidden rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700/50"
                >
                    <Link 
                        :href="item.href" 
                        class="flex items-center gap-3 p-3 text-gray-700 dark:text-gray-200"
                        :class="{ 'bg-gradient-to-r from-blue-500/10 to-purple-500/10 text-blue-700 dark:text-blue-400': item.href === page.url }"
                    >
                        <component 
                            :is="item.icon" 
                            class="h-5 w-5 transition-transform duration-300 group-hover:scale-110"
                            :class="{ 'text-blue-600 dark:text-blue-400': item.href === page.url }"
                        />
                        <span class="font-medium">{{ item.title }}</span>
                    </Link>
                </SidebarMenuButton>
            </SidebarMenuItem>
        </SidebarMenu>
    </SidebarGroup>
</template>
