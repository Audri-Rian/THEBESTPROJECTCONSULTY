<script setup lang="ts">
import UserInfo from '@/components/UserInfo.vue';
import { DropdownMenu, DropdownMenuContent, DropdownMenuTrigger } from '@/components/ui/dropdown-menu';
import { SidebarMenu, SidebarMenuButton, SidebarMenuItem, useSidebar } from '@/components/ui/sidebar';
import { type SharedData, type User } from '@/types';
import { usePage } from '@inertiajs/vue3';
import { ChevronsUpDown } from 'lucide-vue-next';
import UserMenuContent from './UserMenuContent.vue';

const page = usePage<SharedData>();
const user = page.props.auth.user as User;
const { isMobile, state } = useSidebar();
</script>

<template>
    <SidebarMenu>
        <SidebarMenuItem>
            <DropdownMenu>
                <DropdownMenuTrigger as-child>
                    <SidebarMenuButton 
                        size="lg" 
                        class="relative overflow-hidden rounded-lg transition-all duration-300 hover:bg-gray-100 dark:hover:bg-gray-700/50 data-[state=open]:bg-gray-100 dark:data-[state=open]:bg-gray-700/50"
                    >
                        <UserInfo :user="user" class="flex-1" />
                        <ChevronsUpDown class="ml-auto h-4 w-4 text-gray-500 transition-transform duration-300 group-data-[state=open]:rotate-180" />
                    </SidebarMenuButton>
                </DropdownMenuTrigger>
                <DropdownMenuContent 
                    class="w-[--radix-dropdown-menu-trigger-width] min-w-56 rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 shadow-lg" 
                    :side="isMobile ? 'bottom' : state === 'collapsed' ? 'left' : 'bottom'"
                    align="end" 
                    :side-offset="4"
                >
                    <UserMenuContent :user="user" />
                </DropdownMenuContent>
            </DropdownMenu>
        </SidebarMenuItem>
    </SidebarMenu>
</template>
