
<?php
    function redirect() {
        // Esta função está definida mas não é chamada neste fluxo.
        echo "<meta http-equiv='refresh' content='5;url=" . INCLUDE_PATH_PAINEL . "'>";
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <title>Aviso de Pedidos Atrasados</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: "Roboto", sans-serif;
            background: linear-gradient(135deg, #ece9e6, #ffffff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            color: #333;
        }

        .aviso-pedido-atrasado {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            width: 90%;
            max-width: 600px;
            padding: 30px;
            box-sizing: border-box;
        }
        
        .aviso-pedido-atrasado img {
            display: block;
            margin: 0 auto 20px auto;
            max-height: 100px;
        }

        .aviso-pedido-atrasado p {
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .aviso-pedido-atrasado h2 {
            margin-top: 0;
            font-weight: 500;
        }

        .btn-continuar, .btn-pedido {
            display: inline-block;
            padding: 10px 20px;
            background: #007bff;
            color: #fff !important;
            text-decoration: none;
            border-radius: 4px;
            transition: background 0.3s ease;
            font-weight: 500;
        }

        .btn-continuar:hover, .btn-pedido:hover {
            background: #0056b3;
        }

        .btn-pedido {
            display: inline-block;
            margin-bottom: 10px;
        }

        .cta-container {
            margin-top: 20px;
            text-align: right;
        }
        
        .lista-pedidos {
            max-height: 100px; /* Define uma altura máxima */
            overflow-y: auto; /* Adiciona scroll vertical quando necessário */
            border: 1px solid #ddd; /* Opcional: borda para destacar a lista */
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .lista-pedidos p {
            margin: 1px 0; /* Ajusta o espaçamento entre os itens */
        }
    </style>
</head>
<body>
    <div class="aviso-pedido-atrasado">
        <img src="<?= INCLUDE_PATH ?>assets/img/brasao-vertical-colorido.png" alt="Logo da Faculdade">
        
        <h2>Olá, <?= $nome_do_aluno ?></h2>

        <p>Gostaríamos de lembrá-lo(a) que ainda há materiais emprestados em seu nome, que é muito importante devolvê-los dentro do prazo estabelecido. Isso ajuda a garantir que outros colegas também possam utilizar esses recursos em suas atividades acadêmicas.</p>
        
        <p>Por favor, pedimos que façam a devolução até a data limite que é <strong>03/01/2024</strong>.</p>
        
        <p>Lembramos que a não devolução dentro deste prazo resultará em restrições nos futuros empréstimos, mesmo para atividades acadêmicas.</p>
        <p>Agradecemos a sua compreensão e a colaboração.</p>
        <p>Caso tenha qualquer dúvida ou necessite de ajuda, não hesite em entrar em contato conosco.</p>
        <p>Um abraço e sucesso nos estudos!</p>
        
        <div class="lista-pedidos">
            <?= $dados_dos_emprestimos ?>
        </div>

        <p><em>Atenciosamente,<br>
        Coordenação do Curso de Engenharia de Computação<br>
        Direção do Campus Quixadá</em></p>
        
        <div class="cta-container">
            <a class="btn-continuar" href="<?= INCLUDE_PATH_PAINEL ?>">Continuar para o painel</a>
        </div>
    </div>
</body>
</html>