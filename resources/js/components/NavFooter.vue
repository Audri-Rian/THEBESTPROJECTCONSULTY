<script setup lang="ts">
import { SidebarGroup, SidebarGroupContent, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem } from '@/types';

interface Props {
    items: NavItem[];
    class?: string;
}

defineProps<Props>();
</script>

<template>
    <SidebarGroup :class="`group-data-[collapsible=icon]:p-0 ${$props.class || ''}`">
        <SidebarGroupContent>
            <SidebarMenu>
                <SidebarMenuItem v-for="item in items" :key="item.title">
                    <SidebarMenuButton 
                        class="relative overflow-hidden rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700/50" 
                        as-child
                    >
                        <a 
                            :href="item.href" 
                            target="_blank" 
                            rel="noopener noreferrer"
                            class="flex items-center gap-3 p-3 text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-gray-100"
                        >
                            <component 
                                :is="item.icon" 
                                class="h-5 w-5 transition-transform duration-300 group-hover:scale-110"
                            />
                            <span class="font-medium">{{ item.title }}</span>
                        </a>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarGroupContent>
    </SidebarGroup>
</template>
