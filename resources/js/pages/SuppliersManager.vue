<script setup lang="ts">
import type { BreadcrumbItem } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import { Table } from '@/components/ui/table';
import Button from '../components/ui/button/Button.vue';
import Title from '@/components/ui/title';
import { Modal } from '@/components/ui/modal';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: '/dashboard',
    },
    {
        title: 'Products',
        href: '/products',
    },
    {
        title: 'Suppliers',
        href: '/suppliers'
    }
];

const selectedSupplier = ref({
    id: '',
    name: '',
    cnpj: '',
    email: '',
    phone: '',
    street: '',
    city: '',
    state: '',
    postal_code: '',
    district: '',
});

let form = useForm({
    name: '',
    cnpj: '',
    email: '',
    phone: '',
    street: '',
    city: '',
    state: '',
    postal_code: '',
    district: '',
});

const modalRef = ref<InstanceType<typeof Modal> | null>(null);
const editModalRef = ref<InstanceType<typeof Modal> | null>(null);

const openAddSupplierModal = () => {
    form = useForm({
        name: '',
        cnpj: '',
        email: '',
        phone: '',
        street: '',
        city: '',
        state: '',
        postal_code: '',
        district: '',
    });
    modalRef.value?.openModal();
};

const openEditSupplierModal = (supplier: any) => {
    selectedSupplier.value = { ...supplier };
    editModalRef.value?.openModal();
};

const submitFormCreate = () => {
    form.post(route('suppliers.store'), {
        onSuccess: () => {
            form.reset();
            modalRef.value?.closeModal();
        },
    });
};

const submitFormUpdate = () => {
    form.name = selectedSupplier.value.name;
    form.cnpj = selectedSupplier.value.cnpj;
    form.email = selectedSupplier.value.email;
    form.phone = selectedSupplier.value.phone;
    form.street = selectedSupplier.value.street;
    form.city = selectedSupplier.value.city;
    form.state = selectedSupplier.value.state;
    form.postal_code = selectedSupplier.value.postal_code;
    form.district = selectedSupplier.value.district;

    form.put(route('suppliers.update', selectedSupplier.value.id), {
        onSuccess: () => {
            editModalRef.value?.closeModal();
        },
    });
};

defineProps<{
    suppliers: Array<{
        id: number;
        name: string;
        cnpj: string;
        email: string;
        phone: string;
        street: string;
        city: string;
        state: string;
        postal_code: string;
        district: string;
    }>;
}>();
</script>

<template>
    <Head title="Suppliers Manager" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div class="relative min-h-[100vh] p-6 flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b">
                        <div class="flex-1">
                            <Title title="Suppliers in System" :level="1" />
                            <Title title="Here you can view the Suppliers registered in the database" :level="2" />
                        </div>
                        <div class="flex-shrink-0">
                            <Button :icon="'Pin'" :variant="'default'" @click="openAddSupplierModal">Add Supplier</Button>
                        </div>
                    </div>

                    <Table
                        :headers="['ID', 'Name', 'Email', 'Phone', 'Action']"
                        :data="
                        suppliers.map((supplier) => ({
                                ID: supplier.id,
                                Name: supplier.name,
                                Email: supplier.email,
                                Phone: supplier.phone,
                                supplier: supplier,
                            }))
                        "
                    >
                        <template #action="{ row }">
                            <Button @click="openEditSupplierModal(row.supplier)" icon="Pencil">Edit</Button>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>

    <Modal ref="modalRef">
        <template #title>
            <Title title="Add Supplier" :level="1" />
        </template>
        <template #body>
            <div>
                <form @submit.prevent="submitFormCreate" class="space-y-4 grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <Label for="name">Name</Label>
                        <Input id="name" v-model="form.name" type="text" required placeholder="Legit Papai Clothes" />
                    </div>
                    <div>
                        <Label for="cnpj">CNPJ</Label>
                        <Input id="cnpj" v-model="form.cnpj" type="text" placeholder="85.407.042/0001-02" />
                    </div>
                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" placeholder="legitpapai@kryptforge.com" />
                    </div>
                    <div>
                        <Label for="phone">Phone</Label>
                        <Input id="phone" v-model="form.phone" type="number" placeholder="40028922" />
                    </div>
                    <div>
                        <Label for="street">Street</Label>
                        <Input id="street" v-model="form.street" type="text" placeholder="Rua das Negas" />
                    </div>
                    <div>
                        <Label for="district">District</Label>
                        <Input id="district" v-model="form.district" type="text" placeholder="Centro" />
                    </div>
                    <div>
                        <Label for="city">City</Label>
                        <Input id="city" v-model="form.city" type="text" placeholder="São Paulo" />
                    </div>
                    <div>
                        <Label for="state">State</Label>
                        <Input id="state" v-model="form.state" type="text" placeholder="SP" />
                    </div>
                    <div>
                        <Label for="postal_code">Postal Code</Label>
                        <Input id="postal_code" v-model="form.postal_code" type="text" placeholder="01000-000" />
                    </div>
                    <div class="col-span-2 flex justify-end">
                        <Button type="submit" :variant="'default'">Save</Button>
                    </div>
                </form>
            </div>
        </template>
    </Modal>

    <Modal ref="editModalRef">
        <template #title>
            <Title title="Edit Supplier" :level="1" />
        </template>
        <template #body>
            <form @submit.prevent="submitFormUpdate" class="space-y-4 grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <Label for="edit-name">Name</Label>
                    <Input v-model="selectedSupplier.name" id="edit-name" type="text" required />
                </div>
                <div>
                    <Label for="edit-cnpj">CNPJ</Label>
                    <Input v-model="selectedSupplier.cnpj" id="edit-cnpj" type="text" />
                </div>
                <div>
                    <Label for="edit-email">Email</Label>
                    <Input v-model="selectedSupplier.email" id="edit-email" type="email" />
                </div>
                <div>
                    <Label for="edit-phone">Phone</Label>
                    <Input v-model="selectedSupplier.phone" id="edit-phone" type="tel" />
                </div>
                <div>
                    <Label for="edit-street">Street</Label>
                    <Input v-model="selectedSupplier.street" id="edit-street" type="text" />
                </div>
                <div>
                    <Label for="edit-district">District</Label>
                    <Input v-model="selectedSupplier.district" id="edit-district" type="text" />
                </div>
                <div>
                    <Label for="edit-city">City</Label>
                    <Input v-model="selectedSupplier.city" id="edit-city" type="text" />
                </div>
                <div>
                    <Label for="edit-state">State</Label>
                    <Input v-model="selectedSupplier.state" id="edit-state" type="text" />
                </div>
                <div>
                    <Label for="edit-postal_code">Postal Code</Label>
                    <Input v-model="selectedSupplier.postal_code" id="edit-postal_code" type="text" />
                </div>
                <div class="col-span-2 flex justify-end">
                    <Button type="submit" :variant="'default'">Update Supplier</Button>
                </div>
            </form>
        </template>
    </Modal>
</template>

<style scoped>

</style>
