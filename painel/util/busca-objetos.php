<?php
	include_once '../../config.php';

	// Verifica se a variável 'query' foi enviada via POST
	if (isset($_POST['query'])) {

		// Inicializa um array para armazenar os dados
		$data = array();

		// Remove caracteres especiais da variável 'query' usando expressão regular
		$nome = preg_replace('/[^A-Za-z0-9\- ]/', '', $_POST["query"]);

		// Chama o método returnData da classe Estoque para obter 
		// dados com base no nome fornecido
		$objetos = Estoque::returnData($nome, "nome");

		// String usada para destacar o nome pesquisado nos resultados
		$replace_string = '<b>'. strtoupper($nome). '</b>';

		// Verifica se a consulta retornou resultados
		if($objetos != false) {

			// Itera sobre os resultados retornados
			for($i = 0; $i < count($objetos); $i++) {

				// Adiciona os dados formatados ao array de saída
				$data[] = array(
					// destaca o nome pesquisado
					'nome' => str_ireplace($nome, $replace_string, $objetos[$i]->getNome()),
					// destaca o ID pesquisado 
					'id' => str_ireplace($nome, $replace_string, $objetos[$i]->getId()) 
				);
			}
		}

		// Converte o array de dados em formato JSON e o envia de volta
		echo json_encode($data);
	}
?>