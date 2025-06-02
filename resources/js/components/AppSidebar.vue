<script setup lang="ts">
import NavFooter from '@/components/NavFooter.vue';
import NavMain from '@/components/NavMain.vue';
import NavUser from '@/components/NavUser.vue';
import { Sidebar, SidebarContent, SidebarFooter, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar';
import { type NavItem, type NavSection } from '@/types';
import { Link } from '@inertiajs/vue3';
import { BookOpen, Folder, LayoutGrid, Box, Truck, BadgeDollarSign, ChartNoAxesCombined, Save } from 'lucide-vue-next';
import AppLogo from './AppLogo.vue';

const mainNavSections: NavSection[] = [
    {
        title: 'Geral', //Titulos de Identificação
        items: [
            {
                title: 'Dashboard',
                href: '/dashboard',
                icon: LayoutGrid,
            },
            
        ],
        
    },
    {
        title: 'Controle de Estoque', //Titulos de Identificação
        items: [
            {
                title: 'Produtos',
                href: '/products',
                icon: Box,
            },
            {
                title: 'Histórico de produtos',
                href: '/products/history',
                icon: BookOpen,
            },
            {
                title: 'Fornecedores',
                href: '/suppliers',
                icon: Truck,
            },
        ]
    },
    {
        title: 'Financeiro', //Titulos de Identificação
        items: [
            {
                title: 'Lançamento Financeiro',
                href: '/LancamentoFinanceiro',
                icon: ChartNoAxesCombined,
            },
            {
                title: 'Vendas',
                href: '/sales',
                icon: BadgeDollarSign
            },
        ]
    },
    {
        title: 'Relatórios', //Titulos de Identificação
        items: [
            {
                title: 'Relatórios Gerais',
                href: '/report',
                icon: Save
            },
        ]
    }
];

const footerNavItems: NavItem[] = [
    {
        title: 'Github Repo',
        href: 'https://github.com/Audri-Rian/THEBESTPROJECTCONSULTY',
        icon: Folder,
    },
    {
        title: 'Documentation',
        href: 'https://github.com/Audri-Rian/THEBESTPROJECTCONSULTY',
        icon: BookOpen,
    },
];
</script>

<template>
    <Sidebar collapsible="icon" variant="inset" class="border-r">
        <SidebarHeader>
            <SidebarMenu>
                <SidebarMenuItem>
                    <SidebarMenuButton size="lg" as-child>
                        <Link :href="route('dashboard')">
                            <AppLogo />
                        </Link>
                    </SidebarMenuButton>
                </SidebarMenuItem>
            </SidebarMenu>
        </SidebarHeader>

        <SidebarContent>
            <div class="space-y-4">
                <div v-for="section in mainNavSections" :key="section.title" class="px-3">
                    <h2 class="mb-2 px-4 text-lg font-semibold tracking-tight text-gray-500">
                        {{ section.title }}
                    </h2>
                    <NavMain :items="section.items" />
                </div>
            </div>
        </SidebarContent>

        <SidebarFooter>
            <NavFooter :items="footerNavItems" />
            <NavUser />
        </SidebarFooter>
    </Sidebar>
    <slot />
</template>
