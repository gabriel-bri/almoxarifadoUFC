// Função para carregar dados via AJAX com base em uma consulta de pesquisa
function load_data(query) {
    // Verifica se a consulta possui mais de 2 caracteres
    if(query.length > 2){
        // Obtém o domínio atual com o protocolo
        let currentDomain = window.location.origin + '/almoxarifado/painel/';

        // Cria um objeto FormData para enviar dados via POST
        let form_data = new FormData();

        // Adiciona a consulta ao FormData
        form_data.append('query', query);

        // Inicializa uma nova solicitação AJAX
        let ajax_request = new XMLHttpRequest();

        // Configura a solicitação AJAX para enviar os dados para o script
		// de busca de objetos
        ajax_request.open('POST', `${currentDomain}util/busca-objetos`);

        // Envia os dados para o servidor
        ajax_request.send(form_data);

        // Função de retorno de chamada para manipular a resposta do servidor
        ajax_request.onreadystatechange = function(){
            // Verifica se a solicitação AJAX foi concluída e bem-sucedida
            if(ajax_request.readyState == 4 && ajax_request.status == 200){
                // Converte a resposta JSON em um objeto JavaScript
                let response = JSON.parse(ajax_request.responseText);
                
                // Exibe a resposta no console do navegador para depuração
                console.table(response);

                // Cria uma string HTML para exibir os resultados da pesquisa
                let html = '<div class="list-group">';

                // Inicia uma lista não ordenada
                html += '<ul>';

                // Verifica se a resposta contém dados
                if(response.length > 0){
                    // Itera sobre os resultados e cria entradas HTML para cada item
                    for(let count = 0; count < response.length; count++){
                        html += `<li class="list-group-item list-group-item-action">*
						 ${response[count].nome}
						 <br><br><a href="${currentDomain}editar-estoque?id=${response[count].id}">
						 Visualizar item <i class="fa fa-eye"></i></a></li></ul><p>
						</li>`;
                    }

                    // Adiciona uma mensagem informativa sobre itens já cadastrados
                    html += `</ul><p>Ops! Talvez você já tenha 
					adicionado esse item antes. 
					Itens marcados com * já foram cadastrados.`;
                }

                // Fecha as tags HTML necessárias
                html += '</div>';

                // Atualiza o conteúdo do elemento HTML com o ID 'search_result'
                document.getElementById('search_result').innerHTML = html;
            }
        }
    } 
	else {
        // Se a consulta tiver menos de 3 caracteres, limpa o conteúdo do elemento HTML
        document.getElementById('search_result').innerHTML = '';
    }
}
