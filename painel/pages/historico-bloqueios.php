<?php  

// Ativa exibição de erros
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);

// Define o nível de relatório de erros
//error_reporting(E_ALL);

// Opcional: definir log de erros em arquivo (caso não queira exibir na tela)
//ini_set('log_errors', 1);
//ini_set('error_log', __DIR__ . '/php_errors.log');

    verificaPermissaoPagina(3);
?>

<?php 
    if(isset($_GET['pagina']) && (int)$_GET['pagina'] && $_GET['pagina'] > 0) {
        $paginaAtual = filter_var($_GET['pagina'], FILTER_SANITIZE_NUMBER_INT);
    } 
    
    else {
        $paginaAtual = 1;
    }

    $porPagina = 10;
    $offset = ($paginaAtual - 1) * $porPagina;

    if($_SESSION['acesso'] == 3){
        //função pra selecionar as galerosas
    }




    // --- iniciar filtros para a busca ---
    $termoBusca = "";
    $filtroColuna = "usuarios.nome";

    // Verifica se uma busca foi realizada
    if(isset($_GET['buscar'])) {
        $termoBusca = htmlspecialchars(strip_tags($_GET["busca"]), ENT_QUOTES, 'UTF-8');

        // Processa o filtro de rádio (nome, matricula, email)
        if(isset($_GET['opcao'])) {
            $filtroOpcao = htmlspecialchars(strip_tags($_GET["opcao"]), ENT_QUOTES, 'UTF-8');

            switch ($filtroOpcao) {
                case 'matricula':
                    $filtroColuna = "usuarios.matricula";
                    break;
                case 'email':
                    $filtroColuna = "usuarios.email";
                    break;
                default:
                    $filtroColuna = "usuarios.nome";
                    break;
            }
        }
        
        // Processa o filtro de "apenas bloqueados"
        //$apenasBloqueados = isset($_GET['filtro_ativos']) && $_GET['filtro_ativos'] == '1';

        //Processa filtros de data
        //$dataInicio = filter_var($_GET["data_bloqueio"], ENT_QUOTES, 'UTF-8');
        //$dataFim = filter_var($_GET["data_desbloqueio"], ENT_QUOTES, 'UTF-8');
    }
    $alunosComHistorico = [];
    // Busca os dados no banco usando a nova classe
    // Busca os dados no banco usando a nova classe
    
    $alunosComHistorico = HistoricoBloqueios::selectAll($offset, $porPagina, $termoBusca, $filtroColuna);
    
    // Lógica para recalcular total de páginas (ESSENCIAL para paginação de busca)
    $totalResultados = HistoricoBloqueios::contarTotalBusca($termoBusca, $filtroColuna); 
    $totalPaginas = ceil($totalResultados / $porPagina);

?>
<div class="box-content">
    <h2> <i class="fas fa-user-lock"></i> Consulta de Histórico de Bloqueios</h2>
    
    <form class="buscador" method="GET" action="<?php echo INCLUDE_PATH_PAINEL ?>consultar-bloqueios"> 
        <div class="form-group">
            <label for="campo">Busque por alunos com histórico de bloqueio: <i class="fa fa-search"></i></label>
            <input type="text" name="busca" placeholder="Ex: Fulano" id="campo" value="<?php echo htmlentities($termoBusca); ?>">
            
            <div class="filtro">
                <input type="radio" id="opcao-nome" name="opcao" value="nome" <?php echo ($filtroColuna == 'usuarios.nome') ? 'checked' : ''; ?>>
                <label for="opcao-nome">Nome</label>

                <input type="radio" id="opcao-matricula" name="opcao" value="matricula" <?php echo ($filtroColuna == 'usuarios.matricula') ? 'checked' : ''; ?>>
                <label for="opcao-matricula">Matrícula</label>

                <input type="radio" id="opcao-email" name="opcao" value="email" <?php echo ($filtroColuna == 'usuarios.email') ? 'checked' : ''; ?>>
                <label for="opcao-email">E-mail</label>
            </div>
            <input type="submit" name="buscar" value="Buscar">          
        </div>
    </form>
    
    <div class="wraper-table">
        <table>
            <tr>
                <td>Nome</td>
                <td>Sobrenome</td>
                <td>Matrícula</td>
                <td>Status Atual</td>
                <td>Total de Bloqueios</td>
                <td>#</td>
            </tr>
            <?php
                if($alunosComHistorico){
                    foreach ($alunosComHistorico as $aluno) {
            ?>
                <tr>
                    <td><?php echo htmlentities($aluno['nome']); ?></td>
                    <td><?php echo htmlentities($aluno['sobrenome']); ?></td>
                    <td><?php echo htmlentities($aluno['matricula']); ?></td>
                    <td>
                        <?php 
                            echo $aluno['is_bloqueado'] 
                                ? '<span style="color: red; font-weight: bold;">Bloqueado</span>' 
                                : '<span style="color: green;">Liberado</span>';
                        ?>
                    </td>
                    <td><?php echo htmlentities($aluno['total_bloqueios']); ?></td>
                    <td><a href="<?php echo INCLUDE_PATH_PAINEL?>historico-aluno?id=<?php echo $aluno['id']; ?>" class="btn edit">Ver Histórico Detalhado <i class="fa fa-eye"></i></a></td>
                </tr>
            <?php } } else { ?>
                <tr>
                    <td colspan="6" style="text-align:center;">Nenhum aluno encontrado com esses critérios.</td>
                </tr>
            <?php } ?>
        </table>
    </div>

<div class="paginacao">
        <?php
            // Prepara a URL base para os links de paginação, mantendo os filtros
            $params = $_GET;
            unset($params['pagina']); 
            $queryString = http_build_query($params);
            $baseUrl = INCLUDE_PATH_PAINEL . 'consultar-bloqueios?' . $queryString;

            for($i = 1; $i <= $totalPaginas; $i++) {
                $url = $baseUrl . '&pagina=' . $i;
                if($i == $paginaAtual) {
                    echo '<a href="' . $url . '" class="page-selected">' . $i . '</a>';
                } else {
                    echo '<a href="' . $url . '">' . $i . '</a>';
                }
            }
        ?>
    </div>
</div>