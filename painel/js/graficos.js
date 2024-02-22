// Função para criar um gráfico com os pedidos do ano atual
function pedidosAnoAtual(data) {
    // Mapeie os números de mês para os nomes dos meses
    let meses = data.map(function(item) {
        let monthNames = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
                          "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
        let monthIndex = parseInt(item.mes) - 1;
        return monthNames[monthIndex];
    });

    // Extrai os totais de pedidos dos dados
    let totalPedidos = data.map(function(item) {
        return item.total_resultados;
    });

    // Define cores diferentes para cada mês
    let backgroundColors = [
        'rgba(255, 99, 132, 0.2)', // Janeiro
        'rgba(54, 162, 235, 0.2)', // Fevereiro
        'rgba(255, 206, 86, 0.2)', // Março
        'rgba(75, 192, 192, 0.2)', // Abril
        'rgba(153, 102, 255, 0.2)', // Maio
        'rgba(255, 159, 64, 0.2)', // Junho
        'rgba(255, 99, 132, 0.2)', // Julho
        'rgba(54, 162, 235, 0.2)', // Agosto
        'rgba(255, 206, 86, 0.2)', // Setembro
        'rgba(75, 192, 192, 0.2)', // Outubro
        'rgba(153, 102, 255, 0.2)', // Novembro
        'rgba(255, 159, 64, 0.2)', // Dezembro
    ];

    // Crie um contexto para o gráfico
    let ctx = document.getElementById('pedidos-ano-atual').getContext('2d');

    // Ano atual
    const anoAtual = new Date().getFullYear();

    // Crie um gráfico de barras
    let myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: meses,
            datasets: [{
                label: `Total de Pedidos por Mês no Ano de ${anoAtual}`,
                data: totalPedidos,
                backgroundColor: backgroundColors,
                barThickness: 30, // Assign your width here
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
    
    // Função para exportar o gráfico
    function exportChart() {
        // Cria um link para download
        let a = document.createElement('a');
        a.href = myChart.toBase64Image();
        a.download = `pedidos_ano_atual_${anoAtual}.png`;
        // Dispara o download
        a.click();
    }
    
    // Adiciona um event listener ao botão de exportação
    document.getElementById('export-pedidos-ano-atual').addEventListener('click', exportChart)
}

// Função para criar um gráfico com o número de pedidos por ano.
function pedidosPorAno(data) {
    // Extrai os anos e o total de pedidos dos dados
    let anos = data.map(function(item) {
        return item.ano;
    });

    let totalPedidos = data.map(function(item) {
        return item.total_resultados;
    });

    // Define cores aleatórias para cada ano
    let backgroundColors = anos.map(function() {
        return 'rgba(' + Math.floor(Math.random() * 256) + ',' +
            Math.floor(Math.random() * 256) + ',' +
            Math.floor(Math.random() * 256) + ', 0.2)';
    });

    // Crie um contexto para o gráfico
    let ctx = document.getElementById('pedidos-por-ano').getContext('2d');

    // Ano atual
    const anoAtual = new Date().getFullYear();

    // Crie um gráfico de barras
    let myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: anos,
            datasets: [{
                label: `Total de Pedidos por Ano`,
                data: totalPedidos,
                backgroundColor: backgroundColors,
                barThickness: 30, // Assign your width here
                hoverOffset: 100,
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Função para exportar o gráfico
    function exportChart() {
        // Cria um link para download
        let a = document.createElement('a');
        a.href = myChart.toBase64Image();
        a.download = 'pedidos_por_ano.png';
        // Dispara o download
        a.click();
    }

    // Adiciona um event listener ao botão de exportação
    document.getElementById('export-pedidos-por-ano').addEventListener('click', exportChart);
}

// Função para criar um gráfico com o nome do item mais pedido 
// junto com o mês e sua quantidade
function pedidosPorMes(data) {
    // Ano atual
    const anoAtual = new Date().getFullYear();
    
    // Verifica se há dados para processar
    if (data && data.length > 0) {
        // Extrai os dados do nome do item mais pedido 
        // junto com o mês e sua quantidade
        let resultados = data.map(function(item) {
            // Mapeia o número do mês para o nome do mês
            let monthNames = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
                              "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
            let mes = monthNames[item.mes - 1]; // Obtém o nome do mês com base no 
            // número do mês
            
            // Verifica se todos os dados necessários estão presentes
            if (item.item_mais_pedido && mes && item.quantidade_total) {
                return item.item_mais_pedido + ' no mês de ' + mes + ' - Quantidade: ' + item.quantidade_total;
            } else {
                return "Dados Incompletos";
            }
        });

        // Define cores aleatórias para cada mês
        let backgroundColors = [];
        for (let i = 0; i < data.length; i++) {
            backgroundColors.push('rgba(' + Math.floor(Math.random() * 256) + ',' +
                                  Math.floor(Math.random() * 256) + ',' +
                                  Math.floor(Math.random() * 256) + ', 0.2)');
        }

        // Cria um contexto para o gráfico
        let ctx = document.getElementById('pedidos-por-mes-ano-atual').getContext('2d');

        // Cria um gráfico de barras
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: resultados, // Usando os resultados como 
                // labels para o gráfico
                datasets: [{
                    label: `Item Mais Pedido por Mês no Ano de ${anoAtual}`,
                    data: data.map(item => item.quantidade_total), // Usando a quantidade como dados para o gráfico
                    backgroundColor: backgroundColors,
                    barThickness: 30, // Assign your width here
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Função para exportar o gráfico
        function exportChart() {
            // Cria um link para download
            let a = document.createElement('a');
            a.href = myChart.toBase64Image();
            a.download = `pedidos_por_mes_ano_${anoAtual}.png`;
            // Dispara o download
            a.click();
        }
        
        // Adiciona um event listener ao botão de exportação
        document.getElementById('export-pedidos-por-mes-ano-atual').addEventListener('click', exportChart);
    }
}

// Função para criar um gráfico com o nome do item mais pedido por ano e sua quantidade total
function maisPedidoPorAno(data) {
    // Verifica se há dados para processar
    if (data && data.length > 0) {
        // Extrai os dados do nome do item mais pedido por ano e sua quantidade total
        let resultados = data.map(function(item) {
            return item.ano + ": " + item.item_mais_pedido + ' - Quantidade Total: ' + item.quantidade_total;
        });

        // Define cores aleatórias para cada mês
        let backgroundColors = [];
        for (let i = 0; i < data.length; i++) {
            backgroundColors.push('rgba(' + Math.floor(Math.random() * 256) + ',' +
            Math.floor(Math.random() * 256) + ',' +
            Math.floor(Math.random() * 256) + ', 0.2)');
        }
        // Cria um contexto para o gráfico
        let ctx = document.getElementById('mais-pedido-por-ano').getContext('2d');

        // Cria um gráfico de barras
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: resultados, // Usando os resultados como labels para o gráfico
                datasets: [{
                    label: 'Pedidos por Ano',
                    data: data.map(item => item.quantidade_total), // Usando a quantidade como dados para o gráfico
                    backgroundColor: backgroundColors,
                    barThickness: 30, // Assign your width here
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
        
        // Função para exportar o gráfico
        function exportChart() {
            // Cria um link para download
            let a = document.createElement('a');
            a.href = myChart.toBase64Image();
            a.download = 'mais_pedidos_por_ano.png';
            // Dispara o download
            a.click();
        }
        
        // Adiciona um event listener ao botão de exportação
        document.getElementById('export-mais-pedido-por-ano').addEventListener('click', exportChart);
    }
}

function criarGraficoEstoque(data) {
    let ctx = document.getElementById('estoque-tipo').getContext('2d');

    // Extrair os tipos e quantidades do JSON
    // Mapear os tipos para "Equipamento" e "Componente"
    let tiposLabel = data.map(item => {
        if (item.tipo === '1') {
            return 'Equipamento';
        } else if (item.tipo === '2') {
            return 'Componente';
        }
    });
    let quantidades = data.map(item => item.quantidade_total);

    let myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: tiposLabel, // Usar os tipos como rótulos
            datasets: [{
                label: 'Quantidade de Itens',
                barThickness: 30, // Assign your width here
                data: quantidades, // Usar as quantidades diretamente
                backgroundColor: [
                    'rgba(255, 99, 132, 0.2)', // Cor para equipamento
                    'rgba(54, 162, 235, 0.2)'   // Cor para componente
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Função para exportar o gráfico
    function exportChart() {
        // Cria um link para download
        let a = document.createElement('a');
        a.href = myChart.toBase64Image();
        a.download = 'tipo_itens_estoque.png';
        // Dispara o download
        a.click();
    }
            
   
    // Adiciona um event listener ao botão de exportação
    document.getElementById('export-estoque-tipo').addEventListener('click', exportChart);
}

function statusItem(data) {
    // Mapear valores de is_ativado para 'Ativado' e 'Desativado'
    const mappedData = data.map(item => ({
      is_ativado: item.is_ativado === "0" ? "Ativado" : "Desativado",
      quantidade_total: item.quantidade_total
    }));

    // Organizar os dados para que 'Ativado' venha primeiro
    mappedData.sort((a, b) => {
        if (a.is_ativado === 'Ativado' && b.is_ativado === 'Desativado') {
        return -1;
        } else if (a.is_ativado === 'Desativado' && b.is_ativado === 'Ativado') {
        return 1;
        } else {
        return 0;
        }
    });

    // Extrair rótulos e dados mapeados
    const labels = mappedData.map(item => item.is_ativado);
    const quantidade = mappedData.map(item => item.quantidade_total);
  
    // Inicializar o gráfico
    const ctx = document.getElementById('estoque-status').getContext('2d');
    const myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Status do estoque',
          data: quantidade,
          barThickness: 30, // Assign your width here
          backgroundColor: [
            'rgba(255, 99, 132, 0.2)', // Cor para equipamento
            'rgba(54, 162, 235, 0.2)'   // Cor para componente
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
        scales: {
          y: {
            beginAtZero: true
          }
        }
      }
    });

        // Função para exportar o gráfico
    function exportChart() {
        // Cria um link para download
        let a = document.createElement('a');
        a.href = myChart.toBase64Image();
        a.download = 'status_estoque.png';
        // Dispara o download
        a.click();
    }
            
    // Adiciona um event listener ao botão de exportação
    document.getElementById('export-estoque-status').addEventListener('click', exportChart);
}