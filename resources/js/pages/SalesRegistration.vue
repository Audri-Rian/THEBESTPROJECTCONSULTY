<script setup lang="ts">

import Title from '@/components/ui/title/Title.vue';
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';


const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Sales Registration',
        href: '/SalesRegistration',
    },
];

</script>

<template>

    <Head title="Sales Registration" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div
                class="relative min-h-[100vh] p-6 flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b">
                        <div class="flex-1">
                            <Title title="Sales Registration" :level="1" />
                            <Title title="Here you will make all sales records" :level="2" />
                        </div>
                    </div>
                </div>
            </div>
        </div>


    </AppLayout>
</template>