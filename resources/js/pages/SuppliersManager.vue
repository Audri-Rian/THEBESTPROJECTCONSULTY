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
import axios from 'axios';
const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Painel',
        href: '/dashboard',
    },
    {
        title: 'Produtos',
        href: '/products',
    },
    {
        title: 'Fornecedores',
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
const deleteSupplier = (id: number) => {
    axios
        .delete(route('suppliers.destroy', id))
        .then(() => {
            window.location.reload();
        })
        .catch((error) => {
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
        status_id: number;
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

    <Head title="Gerenciador de Fornecedores" />
    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex h-full flex-1 flex-col gap-4 rounded-xl p-4">
            <div
                class="relative min-h-[100vh] p-6 flex-1 rounded-xl border border-sidebar-border/70 dark:border-sidebar-border md:min-h-min">
                <div class="space-y-4">
                    <div class="flex flex-wrap items-center justify-between gap-4 border-b">
                        <div class="flex-1">
                            <Title title="Fornecedores no Sistema" :level="1" />
                            <Title title="Aqui você pode visualizar os fornecedores cadastrados na base de dados" :level="2" />
                        </div>
                        <div class="flex-shrink-0">
                            <Button :icon="'Pin'" :variant="'default'" @click="openAddSupplierModal">Adicionar
                                Fornecedor</Button>
                        </div>
                    </div>
                    <Table :headers="['ID', 'Nome', 'Email', 'Telefone', 'Ação']" :data="suppliers
                        .filter(supplier => supplier.status_id === 1)
                        .map((supplier) => ({
                            'ID': supplier.id,
                            'Nome': supplier.name,
                            'Email': supplier.email,
                            'Telefone': supplier.phone,
                            'Ação': '',
                            supplier  // Mantendo o objeto supplier completo para o template action
                        }))">
                        <template #action="{ row }">
                            <div class="flex gap-2">
                                <Button @click="openEditSupplierModal(row.supplier)" icon="Pencil">Editar</Button>
                                <Button @click="deleteSupplier(row.supplier.id)" icon="Trash"
                                    variant="destructive">Excluir</Button>
                            </div>
                        </template>
                    </Table>
                </div>
            </div>
        </div>
    </AppLayout>
    <Modal ref="modalRef">
        <template #title>
            <Title title="Adicionar Fornecedor" :level="1" />
        </template>
        <template #body>
            <div>
                <form @submit.prevent="submitFormCreate" class="space-y-4 grid grid-cols-2 gap-4">
                    <div class="col-span-2">
                        <Label for="name">Nome</Label>
                        <Input id="name" v-model="form.name" type="text" required placeholder="Joias da Malenia" />
                    </div>
                    <div>
                        <Label for="cnpj">CNPJ</Label>
                        <Input id="cnpj" v-model="form.cnpj" type="text" placeholder="85.407.042/0001-02" />
                    </div>
                    <div>
                        <Label for="email">Email</Label>
                        <Input id="email" v-model="form.email" type="email" placeholder="joiasdamalenia@exemplo.com" />
                    </div>
                    <div>
                        <Label for="phone">Telefone</Label>
                        <Input id="phone" v-model="form.phone" type="number" placeholder="40028922" />
                    </div>
                    <div>
                        <Label for="street">Rua</Label>
                        <Input id="street" v-model="form.street" type="text" placeholder="Rua das Flores" />
                    </div>
                    <div>
                        <Label for="district">Bairro</Label>
                        <Input id="district" v-model="form.district" type="text" placeholder="Centro" />
                    </div>
                    <div>
                        <Label for="city">Cidade</Label>
                        <Input id="city" v-model="form.city" type="text" placeholder="São Paulo" />
                    </div>
                    <div>
                        <Label for="state">Estado</Label>
                        <Input id="state" v-model="form.state" type="text" placeholder="SP" />
                    </div>
                    <div>
                        <Label for="postal_code">CEP</Label>
                        <Input id="postal_code" v-model="form.postal_code" type="text" placeholder="01000-000" />
                    </div>
                    <div class="col-span-2 flex justify-end">
                        <Button type="submit" :variant="'default'">Salvar</Button>
                    </div>
                </form>
            </div>
        </template>
    </Modal>
    <Modal ref="editModalRef">
        <template #title>
            <Title title="Editar Fornecedor" :level="1" />
        </template>
        <template #body>
            <form @submit.prevent="submitFormUpdate" class="space-y-4 grid grid-cols-2 gap-4">
                <div class="col-span-2">
                    <Label for="edit-name">Nome</Label>
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
                    <Label for="edit-phone">Telefone</Label>
                    <Input v-model="selectedSupplier.phone" id="edit-phone" type="tel" />
                </div>
                <div>
                    <Label for="edit-street">Rua</Label>
                    <Input v-model="selectedSupplier.street" id="edit-street" type="text" />
                </div>
                <div>
                    <Label for="edit-district">Bairro</Label>
                    <Input v-model="selectedSupplier.district" id="edit-district" type="text" />
                </div>
                <div>
                    <Label for="edit-city">Cidade</Label>
                    <Input v-model="selectedSupplier.city" id="edit-city" type="text" />
                </div>
                <div>
                    <Label for="edit-state">Estado</Label>
                    <Input v-model="selectedSupplier.state" id="edit-state" type="text" />
                </div>
                <div>
                    <Label for="edit-postal_code">CEP</Label>
                    <Input v-model="selectedSupplier.postal_code" id="edit-postal_code" type="text" />
                </div>
                <div class="col-span-2 flex justify-end">
                    <Button type="submit" :variant="'default'">Atualizar Fornecedor</Button>
                </div>
            </form>
        </template>
    </Modal>
</template>