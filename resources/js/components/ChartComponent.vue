<template>
    <div class="chart-container">
      <canvas ref="chartCanvas"></canvas>
    </div>
  </template>
  
  <script setup lang="ts">
  import { ref, onMounted } from 'vue';
  import Chart from 'chart.js/auto';
  
  // Tipagem para a referência do canvas
  const chartCanvas = ref<HTMLCanvasElement | null>(null);
      
  onMounted(async () => {
    try {
      // Busca os dados da API Laravel
      const response = await fetch('/api/chart-data');
      const { labels, values }: { labels: string[], values: number[] } = await response.json();
      
      if (chartCanvas.value) {
        // Cria o gráfico
        new Chart(chartCanvas.value, {
          type: 'bar',
          data: {
            labels: labels,
            datasets: [{
              label: 'Meus Dados',
              data: values,
              backgroundColor: 'rgba(75, 192, 192, 0.2)',
              borderColor: 'rgba(75, 192, 192, 1)',
              borderWidth: 1
            }]
          },
          options: {
            responsive: true,
            scales: {
              y: {
                beginAtZero: true
              }
            }
          }
        });
      }
    } catch (error) {
      console.error('Erro ao carregar dados do gráfico:', error);
    }
  });
  </script>
  
  <style>
  .chart-container {
    width: 80%;
    margin: 0 auto;
  }
  </style>