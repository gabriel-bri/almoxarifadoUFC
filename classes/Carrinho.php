<?php

class Carrinho {

    /**
     * Valida os parâmetros recebidos do formulário de adição de produtos ao carrinho,
     * verifica se os parâmetros necessários estão presentes e se a quantidade é válida,
     * se o produto já está no carrinho e se a quantidade disponível no estoque é suficiente.
     * Adiciona o produto ao carrinho se todas as validações forem bem-sucedidas.
     * @return void
     */
    public static function validarCarrinho() {
        // Verifica se os parâmetros obrigatórios foram recebidos.
        if(!isset($_POST['id_produto']) || !isset($_POST['qtd_' . $_SESSION['secret'] . "_" . $_POST['id_produto']])) {
            Painel::alert("erro", "Algum parâmetro está ausente.");
            return;
        }

        // Obtém o ID do produto e a quantidade do formulário.
        $idProduto = (int) filter_var($_POST['id_produto'], FILTER_SANITIZE_NUMBER_INT);
        $qtdProduto = (int) filter_var($_POST['qtd_' . $_SESSION['secret'] . "_" . $idProduto], FILTER_SANITIZE_STRING);

        // Verifica se a quantidade é válida (igual ou maior que 1).
        if($qtdProduto < 1) {
            Painel::alert("erro", "Quantidade deve ser igual ou maior que 1");
            return;
        }

        // Verifica se o produto já está no carrinho.
        if(Carrinho::jaNoCarrinho($idProduto)) {
            Painel::alert("erro", "O item já foi adicionado ao seu carrinho.");
            return;
        }

        // Obtém a quantidade disponível em estoque para o produto.
        $quantidadeDisponivel = Estoque::estoqueDisponivelProduto(htmlentities($idProduto));

        // Verifica se a quantidade solicitada está disponível em estoque.
        if($qtdProduto > $quantidadeDisponivel->getQuantidade()) {
            Painel::alert("erro", "A quantidade está acima da disponível em nosso estoque.");
            return;
        }

        // Exibe um alerta de sucesso e adiciona o produto ao carrinho.
        Painel::alert("sucesso", "O item foi adicionado ao seu carrinho.");

        // Cria um novo carrinho
        $carrinho = new Carrinho();
        $carrinho->adicionarProduto($idProduto, $qtdProduto);
    }


    /**
     * Adiciona um produto ao carrinho de compras na sessão.
     * Verifica se a sessão do carrinho já existe, se não, 
     * inicializa ela como um array vazio.
     * Adiciona o produto ao carrinho na sessão, associando-o ao seu ID e 
     * à quantidade especificada.
     * @param int $produtoId O ID do produto a ser adicionado ao carrinho.
     * @param int $quantidade A quantidade do produto a ser adicionada ao carrinho.
     * @return void
     */
    public function adicionarProduto($produtoId, $quantidade) {
        // Verifica se a sessão do carrinho já existe, se não, 
        // a inicializa como um array vazio.
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = array();
        }

        // Adiciona o produto ao carrinho na sessão, associando-o 
        // ao seu ID e à quantidade especificada.
        $_SESSION['carrinho'][$produtoId] = $quantidade;
    }


    /**
     * Remove um produto do carrinho de compras na sessão.
     * Verifica se o produto com o ID especificado está presente no carrinho.
     * Se estiver presente, remove o produto do carrinho e exibe uma mensagem de sucesso.
     * @param int $produtoId O ID do produto a ser removido do carrinho.
     * @return void
     */
    public static function removerProduto($produtoId) {
        // Verifica se o produto com o ID especificado está presente no carrinho.
        if(isset($_SESSION['carrinho'][$produtoId])) {
            // Remove o produto do carrinho.
            unset($_SESSION['carrinho'][$produtoId]);
            // Atualiza a URL no navegador para refletir a remoção do produto.
            echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'editar-emprestimo');</script>";
            // Exibe uma mensagem de sucesso.
            Painel::alert("sucesso", "O item foi excluído do seu carrinho.");
        }
    }


    /**
     * Esvazia o carrinho de compras na sessão.
     * Verifica se a sessão do carrinho existe e, se sim, a remove.
     * Atualiza a URL no navegador para refletir a operação e 
     * exibe uma mensagem de sucesso.
     * @return void
     */
    public static function esvaziarCarrinho() {
        // Verifica se a sessão do carrinho não existe.
        if (!isset($_SESSION['carrinho'])) {
            Painel::alert("erro", "O seu carrinho está vazio.");
            return;
        }

        // Remove a sessão do carrinho.
        unset($_SESSION['carrinho']);
        // Atualiza a URL no navegador para refletir a operação.
        echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
        // Gera um novo segredo para evitar que o usuário 
        // acesse o carrinho anterior.
        Carrinho::novoSegredo();
        // Exibe uma mensagem de sucesso.
        Painel::alert("sucesso", "O seu carrinho foi limpo.");

    }

    /**
     * Verifica se um produto com o ID especificado já está no carrinho.
     * @param int $produtoId O ID do produto a ser verificado.
     * @return bool Retorna true se o produto estiver no carrinho, caso contrário, 
     * retorna false.
     */
    public static function jaNoCarrinho($produtoId){
        // Verifica se o produto com o ID especificado está presente no carrinho.
        return isset($_SESSION['carrinho'][$produtoId]);
    }


    /**
     * Atualiza a quantidade de um produto no carrinho de compras na sessão.
     * Verifica se os parâmetros necessários foram recebidos do formulário de atualização.
     * Obtém o ID do produto e a nova quantidade do formulário.
     * Verifica se a quantidade é válida (maior que 0) e se está disponível em estoque.
     * Atualiza a quantidade do produto no carrinho na sessão e exibe uma mensagem de sucesso.
     * @return void
     */
    public static function atualizar() {
        // Verifica se os parâmetros obrigatórios foram recebidos.
        if(!isset($_POST['id_produto']) || !isset($_POST['qtd_' . $_SESSION['secret'] . "_" . $_POST['id_produto']])) {
            Painel::alert("erro", "Algum parâmetro está ausente.");
            return;
        }

        // Obtém o ID do produto e a nova quantidade do formulário.
        $idProdutoArray = (int) filter_var($_POST['id_produto'], FILTER_SANITIZE_NUMBER_INT);
        $qtdProduto = (int) filter_var($_POST['qtd_' . $_SESSION['secret'] . "_" . $idProdutoArray], FILTER_SANITIZE_NUMBER_INT);

        // Verifica se a nova quantidade é válida (maior que 0).
        if($qtdProduto <= 0) {
            Painel::alert("erro", "Quantidade deve ser igual ou maior que 1");
            return;
        }

        // Obtém a quantidade disponível em estoque para o produto.
        $quantidadeDisponivel = Estoque::estoqueDisponivelProduto(htmlentities($idProdutoArray));

        // Verifica se a nova quantidade está disponível em estoque.
        if($qtdProduto > $quantidadeDisponivel->getQuantidade()) {
            Painel::alert("erro", "A quantidade está acima da disponível em nosso estoque.");
            return;
        }

        // Atualiza a quantidade do produto no carrinho na sessão.
        $_SESSION['carrinho'][$idProdutoArray] = $qtdProduto;
        // Exibe uma mensagem de sucesso.
        Painel::alert("sucesso", "O seu carrinho foi atualizado.");
    }


    /**
     * Fecha o carrinho de compras, realizando o pedido dos produtos contidos nele.
     * Verifica se o carrinho não está vazio.
     * Cria um código único para o pedido.
     * Cria um destinatário para o pedido com base nas informações da sessão do usuário.
     * Cria um detalhe de pedido e cadastra-o no banco de dados, 
     * obtendo o ID do último detalhe inserido.
     * Para cada produto no carrinho, cria um pedido e o cadastra no banco de dados 
     * associado ao detalhe de pedido.
     * Atualiza a URL no navegador para refletir a operação e exibe uma mensagem de sucesso.
     * Limpa o carrinho e gera um novo segredo para evitar que o 
     * usuário acesse o carrinho anterior.
     * @return void
     */
    public static function fecharCarrinho() {
        // Verifica se o carrinho está vazio.
        if(!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) <= 0) {
            // Atualiza a URL no navegador para refletir a operação
            // e exibe uma mensagem de erro.
            echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
            Painel::alert("erro", "O seu carrinho está vazio.");
            return;
        }

        // Cria um código único para o pedido.
        $codigo = bin2hex(random_bytes(10));

        // Cria um destinatário para o pedido com base nas informações da sessão do usuário.
        $destinatario = new Usuario(
            NULL,
            $_SESSION['nome'],
            $_SESSION['sobrenome'],
            $_SESSION['email'],
            NULL, NULL, NULL, NULL
        );

        // Cria um detalhe de pedido e cadastra-o no banco de dados,
        // obtendo o ID do último detalhe inserido.
        $detalhePedido = new PedidoDetalhes(
            $_SESSION['id'], $codigo,
            NULL, NULL, NULL, NULL, NULL, $destinatario
        );
        $ultimoIDDetalhes = $detalhePedido->cadastrarPedido($detalhePedido);

        // Para cada produto no carrinho, cria um pedido e o
        // cadastra no banco de dados associado ao detalhe de pedido.
        foreach ($_SESSION['carrinho'] as $idProduto => $quantidade){
            $pedido = new Pedido($quantidade, $idProduto, $ultimoIDDetalhes);
            Pedido::cadastrarPedido($pedido);
        }

        // Atualiza a URL no navegador para refletir a operação
        // e exibe uma mensagem de sucesso.
        echo "<script>window.history.pushState('solicitar-emprestimo', 'Title', 'solicitar-emprestimo');</script>";
        Painel::alert("sucesso", "O seu pedido foi realizado e você recebeu por e-mail uma notificação. A partir de agora você tem até 1 hora para retirar seu pedido.");

        // Limpa o carrinho e gera um novo segredo para evitar que o usuário 
        // acesse o carrinho anterior.
        unset($_SESSION['carrinho']);
        Carrinho::novoSegredo();
    }


    /**
     * Verifica o status do carrinho de compras.
     * Retorna "VAZIO" se o carrinho estiver vazio.
     * Retorna o número de itens no carrinho seguido de uma mensagem 
     * indicando o número de itens.
     * @return string O status do carrinho.
     */
    public static function statusCarrinho() {
        // Verifica se o carrinho está vazio.
        if(!isset($_SESSION['carrinho']) || count($_SESSION['carrinho']) == 0) {
            return "VAZIO";
        } 
        
        else {
            // Gera uma mensagem com base no número de itens no carrinho.
            $mensagem = count($_SESSION['carrinho']) > 1 ? " itens adicionados." : " item adicionado.";
            return count($_SESSION['carrinho']) . $mensagem;
        }
    }

    /**
     * Gera um segredo único para ser usado na sessão, se ainda não estiver definido.
     * @return void
     */
    public static function gerarSegredo() {
        // Verifica se o segredo ainda não está definido.
        if(!isset($_SESSION['secret'])) {
            // Gera um novo segredo.
            $_SESSION['secret']  = bin2hex(random_bytes(8));
        }
    }

    /**
     * Gera um novo segredo único para ser usado na sessão.
     * @return void
     */
    public static function novoSegredo() {
        // Gera um novo segredo.
        $_SESSION['secret']  = bin2hex(random_bytes(8));
    }

    public static function refazerPedido($itensPedido) {
        foreach ($itensPedido as $itemPedido) {
            // Obtém a quantidade disponível em estoque para o produto.
            $quantidadeDisponivel = Estoque::estoqueDisponivelProduto(htmlentities($itemPedido->estoque->getID()));

            // Verifica se a nova quantidade está disponível em estoque.
            if($itemPedido->getQuantidadeItem() > $quantidadeDisponivel->getQuantidade()) {
                Painel::alert("erro", "O item " . $itemPedido->estoque->getNome() . " está com a quantidade acima da disponível em nosso estoque, solicite o empréstimo de maneira manual.");
                return;
            }
        }

        // Cria um código único para o pedido.
        $codigo = bin2hex(random_bytes(10));

        // Cria um destinatário para o pedido com base nas informações da sessão do usuário.
        $destinatario = new Usuario(
            NULL,
            $_SESSION['nome'],
            $_SESSION['sobrenome'],
            $_SESSION['email'],
            NULL, NULL, NULL, NULL
        );
        
        // Cria um detalhe de pedido e cadastra-o no banco de dados,
        // obtendo o ID do último detalhe inserido.
        $detalhePedido = new PedidoDetalhes(
            $_SESSION['id'], $codigo,
            NULL, NULL, NULL, NULL, NULL, $destinatario
        );
  
        $ultimoIDDetalhes = $detalhePedido->cadastrarPedido($detalhePedido);

        // Para cada produto no carrinho, cria um pedido e o
        // cadastra no banco de dados associado ao detalhe de pedido.
        foreach ($itensPedido as $itemPedido){
            $pedido = new Pedido($itemPedido->getQuantidadeItem(), $itemPedido->estoque->getID(), $ultimoIDDetalhes);
            Pedido::cadastrarPedido($pedido);
        }

        Painel::alert("sucesso", "O seu pedido foi realizado e você recebeu por e-mail uma notificação.");
    }
}
?>