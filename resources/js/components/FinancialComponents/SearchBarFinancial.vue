<template>
    <div class="mb-8 rounded-lg border p-6 text-white shadow">
        <h2 class="mb-4 text-xl font-semibold">Filtros e Pesquisa</h2>
        <div class="grid grid-cols-1 gap-4 md:grid-cols-4">
            <input type="text" placeholder="Buscar por Nome..."
                class="rounded-md border-gray-600 bg-gray-700 p-2 text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />
            <select
                class="rounded-md border-gray-600 bg-gray-700 p-2 text-white focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Todos os Atributos</option>
                <option value="Receita">Receita</option>
                <option value="Despesa">Despesa</option>
            </select>
            <select
                class="rounded-md border-gray-600 bg-gray-700 p-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Todas as Categorias</option>
                <option value="Alimentação">Alimentação</option>
                <option value="Transporte">Transporte</option>
                <option value="Salário">Salário</option>
                <option value="Lazer">Lazer</option>
            </select>
            <input type="date"
                class="rounded-md border-gray-600 bg-gray-700 p-2 text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" />

            <Button size="lg"
                class="w-48 rounded-md bg-white text-black hover:bg-gray-600 hover:text-white">Pesquisar</Button>
        </div>
    </div>
</template>