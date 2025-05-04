<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import { ref, onMounted, nextTick } from 'vue';
import { Chart, registerables } from 'chart.js';
import type { TooltipItem } from 'chart.js';

Chart.register(...registerables);

// Dados dos gráficos
const meses = ref<string[]>([]);
const valores = ref<number[]>([]);
const produtos = ref<string[]>([]);
const vendas = ref<number[]>([]);

// Indicadores principais
const faturamentoTotal = ref(0);
const margemBruta = ref(0);
const quantidadeVendida = ref(0);
const produtosCadastrados = ref(0);

// Referências aos gráficos
const faturamentoChart = ref<HTMLCanvasElement | null>(null);
const graficoPizza = ref<HTMLCanvasElement | null>(null);

// Breadcrumbs
const breadcrumbs = ref([
  { text: 'Home', href: '/', title: 'Página Inicial' },
  { text: 'Dashboard', href: '/dashboard', title: 'Visão Geral' },
]);

function criarGraficoLinha() {
  if (
    faturamentoChart.value &&
    meses.value.length &&
    valores.value.length
  ) {
    new Chart(faturamentoChart.value, {
      type: 'line',
      data: {
        labels: meses.value,
        datasets: [{
          label: 'Faturamento (R$)',
          data: valores.value,
          borderColor: 'rgba(54, 162, 235, 1)',
          backgroundColor: 'rgba(54, 162, 235, 0.2)',
          borderWidth: 2,
          tension: 0.4,
          fill: true,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: { position: 'top' },
          title: {
            display: true,
            text: 'Faturamento Mensal (Últimos 12 meses)',
            font: { size: 16 },
          },
          tooltip: {
            callbacks: {
              label: (context: TooltipItem<'line'>) => {
                const value = context.parsed.y;
                return `R$ ${value.toLocaleString('pt-BR')}`;
              },
            },
          },
        },
        scales: {
          y: {
            beginAtZero: true,
            ticks: {
              callback: (value: string | number) =>
                `R$ ${value.toLocaleString('pt-BR')}`,
            },
          },
        },
      },
    });
  }
}

function criarGraficoPizza() {
  if (
    graficoPizza.value &&
    produtos.value.length &&
    vendas.value.length
  ) {
    new Chart(graficoPizza.value, {
      type: 'pie',
      data: {
        labels: produtos.value,
        datasets: [{
          label: 'Vendas (R$)',
          data: vendas.value,
          backgroundColor: [
            'rgba(255, 99, 132, 0.7)',
            'rgba(54, 162, 235, 0.7)',
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(153, 102, 255, 0.7)',
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(75, 192, 192, 1)',
            'rgba(153, 102, 255, 1)',
          ],
          borderWidth: 1,
        }],
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            position: 'right',
            labels: {
              padding: 20,
              font: { size: 12 },
            },
          },
          title: {
            display: true,
            text: 'Produtos Mais Vendidos (Valor)',
            font: { size: 16 },
          },
          tooltip: {
            callbacks: {
              label: (context: TooltipItem<'pie'>) => {
                const label = context.label || '';
                const value = context.parsed;
                return `${label}: R$ ${value.toLocaleString('pt-BR')}`;
              },
            },
          },
        },
      },
    });
  }
}

onMounted(async () => {
  try {
    // 1) Busca dados de faturamento (invoicing)
    const resF = await fetch('/dashboard/invoicing');
    if (!resF.ok) throw new Error(`Invoicing: HTTP ${resF.status}`);
    const dadosFaturamento = await resF.json();

    // 2) Busca dados de vendas (sales)
    const resV = await fetch('/dashboard/sales');
    if (!resV.ok) throw new Error(`Sales: HTTP ${resV.status}`);
    const dadosVendas = await resV.json();

    console.log('Invoicing:', dadosFaturamento);
    console.log('Sales:', dadosVendas);

    // Preenche indicadores principais
    meses.value               = dadosFaturamento.labels;
    valores.value             = dadosFaturamento.values;
    faturamentoTotal.value    = dadosFaturamento.total    || 0;
    margemBruta.value         = dadosFaturamento.margem   || 0;
    quantidadeVendida.value   = dadosFaturamento.quantidade || 0;
    produtosCadastrados.value = dadosFaturamento.produtos || 0;

    // Preenche dados do gráfico de pizza
    produtos.value = dadosVendas.labels;
    vendas.value   = dadosVendas.values;

    // Aguarda o DOM e desenha os gráficos
    await nextTick();
    criarGraficoLinha();
    criarGraficoPizza();
  } catch (error) {
    console.error('Erro ao buscar dados do dashboard:', error);
  }
});
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">
      
      <!-- Indicadores -->
      <div class="grid auto-rows-min gap-6 md:grid-cols-4">
        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold">Faturamento Total</h3>
          <p class="text-3xl font-semibold mt-2">
            R$ {{ faturamentoTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
          </p>
        </div>

        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold">Margem Bruta</h3>
          <p class="text-3xl font-semibold mt-2">{{ margemBruta }}%</p>
        </div>

        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold">Quantidade Vendida</h3>
          <p class="text-3xl font-semibold mt-2">
            {{ quantidadeVendida.toLocaleString('pt-BR') }}
          </p>
        </div>

        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold">Produtos Cadastrados</h3>
          <p class="text-3xl font-semibold mt-2">
            {{ produtosCadastrados.toLocaleString('pt-BR') }}
          </p>
        </div>
      </div>

      <!-- Gráficos -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold mb-4">Faturamento Mensal</h3>
          <div class="h-[350px]">
            <canvas ref="faturamentoChart"></canvas>
          </div>
        </div>

        <div class="rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold mb-4">Produtos Mais Vendidos</h3>
          <div class="h-[350px]">
            <canvas ref="graficoPizza"></canvas>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
@media (max-width: 1024px) {
  .lg\:grid-cols-2,
  .md\:grid-cols-4 {
    grid-template-columns: 1fr;
  }
}
</style>