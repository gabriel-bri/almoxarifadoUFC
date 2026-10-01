function obterContextoGrafico(canvasId, data) {
    if (!Array.isArray(data) || data.length === 0 || typeof Chart === 'undefined') {
        return null;
    }

    const canvas = document.getElementById(canvasId);
    if (!canvas) {
        return null;
    }

    const graficoAtual = Chart.getChart(canvas);
    if (graficoAtual) {
        graficoAtual.destroy();
    }

    return canvas.getContext('2d');
}

if (typeof Chart !== 'undefined') {
    Chart.defaults.color = '#4f4f4f';
    Chart.defaults.font.size = 13;
    Chart.defaults.font.weight = '500';
    Chart.defaults.plugins.legend.labels.color = '#4f4f4f';
}

function tooltipComEmpates({chart, tooltip}) {
    let tooltipEl = document.getElementById('tooltip-grafico-empates');
    if (!tooltipEl) {
        tooltipEl = document.createElement('div');
        tooltipEl.id = 'tooltip-grafico-empates';
        tooltipEl.style.position = 'fixed';
        tooltipEl.style.maxWidth = '320px';
        tooltipEl.style.maxHeight = '180px';
        tooltipEl.style.overflowY = 'auto';
        tooltipEl.style.padding = '8px 10px';
        tooltipEl.style.background = 'rgba(0, 0, 0, 0.85)';
        tooltipEl.style.color = '#fff';
        tooltipEl.style.borderRadius = '4px';
        tooltipEl.style.fontSize = '12px';
        tooltipEl.style.lineHeight = '1.4';
        tooltipEl.style.pointerEvents = 'auto';
        tooltipEl.style.zIndex = '1000';
        tooltipEl.addEventListener('mouseenter', () => {
            tooltipEl._sobreTooltip = true;
            clearTimeout(tooltipEl._timerEsconder);
        });
        tooltipEl.addEventListener('mouseleave', () => {
            tooltipEl._sobreTooltip = false;
            tooltipEl.style.display = 'none';
        });
        document.body.appendChild(tooltipEl);
    }

    if (tooltip.opacity === 0 || !tooltip.dataPoints || tooltip.dataPoints.length === 0) {
        clearTimeout(tooltipEl._timerEsconder);
        if (!tooltipEl._sobreTooltip) {
            tooltipEl._timerEsconder = setTimeout(() => {
                tooltipEl.style.display = 'none';
            }, 350);
        }
        return;
    }

    clearTimeout(tooltipEl._timerEsconder);
    const ponto = tooltip.dataPoints[0];
    const item = chart.data.itensOriginais[ponto.dataIndex] || {};
    const itens = String(item.itens_empatados || item.item_mais_pedido || 'Item não informado')
        .split(' | ');
    tooltipEl.replaceChildren();

    const titulo = document.createElement('strong');
    titulo.textContent = Number(item.total_empatados || 1) > 1
        ? `Empate entre ${item.total_empatados} itens (${ponto.raw} unidades)`
        : `${item.item_mais_pedido || 'Item não informado'} (${ponto.raw} unidades)`;
    tooltipEl.appendChild(titulo);

    if (itens.length > 1) {
        const lista = document.createElement('ul');
        lista.style.margin = '5px 0 0';
        lista.style.paddingLeft = '18px';
        itens.forEach(nome => {
            const linha = document.createElement('li');
            linha.textContent = nome;
            lista.appendChild(linha);
        });
        tooltipEl.appendChild(lista);
    }

    const posicao = chart.canvas.getBoundingClientRect();
    const left = Math.min(
        posicao.left + tooltip.caretX + 8,
        window.innerWidth - tooltipEl.offsetWidth - 8
    );
    const pontoY = posicao.top + tooltip.caretY;
    const acima = pontoY - tooltipEl.offsetHeight - 8;
    const abaixo = pontoY + 8;
    tooltipEl.style.left = `${Math.max(8, left)}px`;
    tooltipEl.style.top = `${acima >= 8 ? acima : abaixo}px`;
    tooltipEl.style.display = 'block';
}

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
        'rgba(255, 99, 132, 0.3)', // Janeiro
        'rgba(54, 162, 235, 0.3)', // Fevereiro
        'rgba(255, 206, 86, 0.3)', // Março
        'rgba(75, 192, 192, 0.3)', // Abril
        'rgba(153, 102, 255, 0.3)', // Maio
        'rgba(255, 159, 64, 0.3)', // Junho
        'rgba(255, 99, 132, 0.3)', // Julho
        'rgba(54, 162, 235, 0.3)', // Agosto
        'rgba(255, 206, 86, 0.3)', // Setembro
        'rgba(75, 192, 192, 0.3)', // Outubro
        'rgba(153, 102, 255, 0.3)', // Novembro
        'rgba(255, 159, 64, 0.3)', // Dezembro
    ];

    // Crie um contexto para o gráfico
    const ctx = obterContextoGrafico('pedidos-ano-atual', data);
    if (!ctx) {
        return;
    }

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
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
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
            Math.floor(Math.random() * 256) + ', 0.3)';
    });

    // Crie um contexto para o gráfico
    const ctx = obterContextoGrafico('pedidos-por-ano', data);
    if (!ctx) {
        return;
    }

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
            maintainAspectRatio: false,
            interaction: {
                mode: 'index',
                intersect: false
            },
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
    const monthNames = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho",
                        "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
    
    // Verifica se há dados para processar
    if (data && data.length > 0) {
        // Extrai os dados do nome do item mais pedido 
        // junto com o mês e sua quantidade
        let resultados = data.map(item => monthNames[Number(item.mes) - 1] || 'Mês inválido');

        // Define cores aleatórias para cada mês
        let backgroundColors = [];
        for (let i = 0; i < data.length; i++) {
            backgroundColors.push('rgba(' + Math.floor(Math.random() * 256) + ',' +
                                  Math.floor(Math.random() * 256) + ',' +
                                  Math.floor(Math.random() * 256) + ', 0.3)');
        }

        // Cria um contexto para o gráfico
        const ctx = obterContextoGrafico('pedidos-por-mes-ano-atual', data);
        if (!ctx) {
            return;
        }

        // Cria um gráfico de barras
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: resultados,
                itensOriginais: data,
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
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        enabled: false,
                        external: tooltipComEmpates
                    }
                },
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
        let resultados = data.map(item => item.ano);

        // Define cores aleatórias para cada mês
        let backgroundColors = [];
        for (let i = 0; i < data.length; i++) {
            backgroundColors.push('rgba(' + Math.floor(Math.random() * 256) + ',' +
            Math.floor(Math.random() * 256) + ',' +
            Math.floor(Math.random() * 256) + ', 0.3)');
        }
            // Cria um contexto para o gráfico com opacidade ajustada
        const ctx = obterContextoGrafico('mais-pedido-por-ano', data);
        if (!ctx) {
            return;
        }

        // Cria um gráfico de barras
        let myChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: resultados,
                itensOriginais: data,
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
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    tooltip: {
                        enabled: false,
                        external: tooltipComEmpates
                    }
                },
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
    const ctx = obterContextoGrafico('estoque-tipo', data);
    if (!ctx) {
        return;
    }

    // Extrair os tipos e quantidades do JSON
    // Mapear os tipos para "Equipamento" e "Componente"
    let tiposLabel = data.map(item => {
        const tipo = String(item.tipo);
        if (tipo === '1') {
            return 'Equipamento';
        } else if (tipo === '2') {
            return 'Componente';
        }
        return 'Outro';
    });
    let quantidades = data.map(item => Number(item.quantidade_total));

    let myChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: tiposLabel, // Usar os tipos como rótulos
            datasets: [{
                label: 'Quantidade de Itens',
                barThickness: 30, // Assign your width here
                data: quantidades, // Usar as quantidades diretamente
                backgroundColor: [
                    'rgba(255, 99, 132, 0.3)', // Cor para equipamento
                    'rgba(54, 162, 235, 0.3)'   // Cor para componente
                ],
                borderColor: [
                    'rgba(255, 99, 132, 1)',
                    'rgba(54, 162, 235, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            maintainAspectRatio: false,
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
    const ctx = obterContextoGrafico('estoque-status', data);
    if (!ctx) {
        return;
    }

    // Mapear valores de is_ativado para 'Ativado' e 'Desativado'
    const mappedData = data.map(item => ({
    is_ativado: String(item.is_ativado) === "1" ? "Ativado" : "Desativado",
    quantidade_total: Number(item.quantidade_total)
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
    const myChart = new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [{
          label: 'Status do estoque',
          data: quantidade,
          barThickness: 30, // Assign your width here
          backgroundColor: [
            'rgba(255, 99, 132, 0.3)', // Cor para equipamento
            'rgba(54, 162, 235, 0.3)'   // Cor para componente
          ],
          borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(54, 162, 235, 1)'
          ],
          borderWidth: 1
        }]
      },
      options: {
                maintainAspectRatio: false,
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
