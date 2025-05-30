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
const lucroTotal = ref(0);
const roi = ref(0);
const totalReceitas = ref(0);
const totalDespesas = ref(0);

// Referências aos gráficos
const faturamentoChart = ref<HTMLCanvasElement | null>(null);
const graficoPizza = ref<HTMLCanvasElement | null>(null);
const lucroChart = ref<HTMLCanvasElement | null>(null);

// Breadcrumbs
const breadcrumbs = ref([
  { text: 'Home', href: '/', title: 'Página Inicial' },
  { text: 'Dashboard', href: '/dashboard', title: 'Visão Geral' },
]);

// Funções de criação dos gráficos
function criarGraficoLinha() {
  if (faturamentoChart.value && meses.value.length && valores.value.length) {
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
  if (graficoPizza.value && produtos.value.length && vendas.value.length) {
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

function renderLucroChart(ctx: CanvasRenderingContext2D) {
  new Chart(ctx, {
    type: 'line',
    data: {
      labels: meses.value,
      datasets: [{
        label: 'Lucro',
        data: lucrosMensais.value,
        borderColor: '#3b82f6',
        backgroundColor: 'rgba(59, 130, 246, 0.2)',
        fill: true,
        tension: 0.4,
        pointStyle: 'circle',
        pointRadius: 6,
        pointHoverRadius: 8,
        pointBackgroundColor: '#3b82f6',
        pointBorderColor: '#ffffff',
        pointBorderWidth: 2,
      }],
    },
    options: {
      responsive: true,
      plugins: {
        legend: {
          display: true,
          position: 'top',
        },
        tooltip: {
          mode: 'index',
          intersect: false,
        },
      },
      interaction: {
        mode: 'nearest',
        axis: 'x',
        intersect: false,
      },
      scales: {
        x: {
          grid: {
            display: false,
          },
        },
        y: {
          beginAtZero: true,
          grid: {
            color: 'rgba(200, 200, 200, 0.2)',
          },
        },
      },
    },
  });
}

// Estado dos indicadores
const mostrarInfo = ref({
  margem: false,
  roi: false,
});

const saldoFinanceiro = ref(0);
const lucrosMensais = ref<number[]>([]);

onMounted(async () => {
  try {
    const resF = await fetch('/dashboard/invoicing');
    if (!resF.ok) throw new Error(`Invoicing: HTTP ${resF.status}`);
    const dadosFaturamento = await resF.json();

    const resV = await fetch('/dashboard/sales');
    if (!resV.ok) throw new Error(`Sales: HTTP ${resV.status}`);
    const dadosVendas = await resV.json();

    meses.value               = dadosFaturamento.labels;
    valores.value             = dadosFaturamento.values;
    faturamentoTotal.value    = dadosFaturamento.total    || 0;
    margemBruta.value         = dadosFaturamento.margem   || 0;
    lucroTotal.value          = dadosFaturamento.lucro_total || 0;
    roi.value                 = dadosFaturamento.roi || 0;
    quantidadeVendida.value   = dadosFaturamento.quantidade || 0;
    produtosCadastrados.value = dadosFaturamento.produtos || 0;

    // Preenchendo a lista de lucros mensais
    lucrosMensais.value = dadosFaturamento.lucros_mensais || Array(12).fill(0);

    saldoFinanceiro.value = dadosFaturamento.saldo_financeiro || 0;
    totalReceitas.value = dadosFaturamento.total_receitas || 0;
    totalDespesas.value = dadosFaturamento.total_despesas || 0;

    produtos.value = dadosVendas.labels;
    vendas.value   = dadosVendas.values;

    // Criar gráficos
    await nextTick();
    criarGraficoLinha();
    criarGraficoPizza();

    await nextTick();
    setTimeout(() => {
      if (lucroChart.value) {
        const ctx = lucroChart.value.getContext('2d');
        if (ctx) renderLucroChart(ctx);
      }
    }, 0);

  } catch (error) {
    console.error('Erro ao buscar dados do dashboard:', error);
  }
});
</script>

<template>
  <Head title="Dashboard" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-6 rounded-xl p-6">

      <!-- Indicadores financeiros -->
      <div class="grid auto-rows-min gap-6 md:grid-cols-4">
        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold">Faturamento Total</h3>
          <p class="text-3xl font-semibold mt-2">
            R$ {{ faturamentoTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
          </p>
        </div>

        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold mb-2">Lucro Total</h3>
          <p class="text-3xl font-semibold mt-2">
            R$ {{ lucroTotal.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
          </p>
        </div>

        <div class="relative flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold mb-2 flex items-center gap-2">
            ROI (Retorno sobre o Investimento)
            <button @click="mostrarInfo.roi = !mostrarInfo.roi" class="text-white-600 hover:text-white-800">?</button>
          </h3>
          <div v-if="mostrarInfo.roi" class="fixed inset-0 z-10" @click="mostrarInfo.roi = false"></div>
          <div v-if="mostrarInfo.roi" class="absolute z-20 mt-2 p-3 w-64 bg-white text-sm text-gray-700 border rounded shadow-lg dark:bg-gray-800 dark:text-gray-200">
            ROI (Retorno sobre o Investimento): Mede quanto lucro você obteve em relação ao que investiu...
          </div>
          <p class="text-3xl font-semibold mt-2">{{ roi.toFixed(2) }}%</p>
        </div>

        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold mb-2">Saldo Financeiro</h3>
          <p class="text-3xl font-semibold text-green-600 mt-2">
            R$ {{ saldoFinanceiro.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
          </p>
          <p class="text-sm text-gray-600 dark:text-gray-300 mt-2">
            Receitas: R$ {{ totalReceitas.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}<br>
            Despesas: R$ {{ totalDespesas.toLocaleString('pt-BR', { minimumFractionDigits: 2 }) }}
          </p>
        </div>
      </div>

      <!-- Indicadores operacionais -->
      <div class="grid auto-rows-min gap-6 md:grid-cols-3">
        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold">Quantidade Vendida</h3>
          <p class="text-3xl font-semibold mt-2">{{ quantidadeVendida.toLocaleString('pt-BR') }}</p>
        </div>

        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
          <h3 class="text-lg font-bold">Produtos Cadastrados</h3>
          <p class="text-3xl font-semibold mt-2">{{ produtosCadastrados.toLocaleString('pt-BR') }}</p>
        </div>

        <div class="flex flex-col justify-between rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900 relative">
          <h3 class="text-lg font-bold">
            Margem Bruta
            <button @click="mostrarInfo.margem = !mostrarInfo.margem" class="text-white-600 hover:text-white-800">?</button>
          </h3>
          <div v-if="mostrarInfo.margem" class="fixed inset-0 z-10" @click="mostrarInfo.margem = false"></div>
          <div v-if="mostrarInfo.margem" class="absolute z-10 mt-2 p-3 w-64 bg-white text-sm text-gray-700 border rounded shadow-lg dark:bg-gray-800 dark:text-gray-200">
            Margem Bruta: Mostra a porcentagem de lucro após deduzir o custo das mercadorias vendidas...
          </div>
          <p class="text-3xl font-semibold mt-2">{{ margemBruta }}%</p>
        </div>
      </div>

      <!-- Gráficos reorganizados -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Coluna 1: Gráficos de linha empilhados -->
        <div class="lg:col-span-2 flex flex-col gap-6">
          <div class="rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
            <h3 class="text-lg font-bold mb-4">Faturamento Mensal</h3>
            <div class="relative h-[260px]">
              <canvas ref="faturamentoChart"></canvas>
            </div>
          </div>
        </div>

        <!-- Coluna 2: Gráfico de Pizza -->
        <div class="rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900 h-full">
          <h3 class="text-lg font-bold mb-4">Produtos Mais Vendidos</h3>
          <div class="h-[260px]">
            <canvas ref="graficoPizza"></canvas>
          </div>
        </div>
      </div>
      <div class="rounded-xl border p-6 shadow-md bg-white dark:bg-gray-900">
        <h3 class="text-lg font-bold mb-4">Lucro Mensal</h3>
        <div class="relative h-full">
          <canvas ref="lucroChart"></canvas>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<style scoped>
@media (max-width: 1024px) {
  .lg\:grid-cols-2,
  .md\:grid-cols-4,
  .md\:grid-cols-3 {
    grid-template-columns: 1fr;
  }
}
</style>