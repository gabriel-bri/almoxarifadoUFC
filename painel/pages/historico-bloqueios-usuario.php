<?php

    verificaPermissaoPagina(3);

    
    $paginaAtual = 1; 
    
    if(isset($_GET['id']) && (int)$_GET['id'] > 0){
        $id_aluno = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
        $historicos_bloqueios_aluno = HistoricoBloqueios::getHistoricoCompleto($id_aluno);
        $dados_aluno = Usuario::select(' id = ?', [$id_aluno]);
    
        // temoporario isso ta errado
        
        if(!$dados_aluno){
            Painel::alert("error", "dados do aluno não encontrados");
            die();
        }

        if(!$historicos_bloqueios_aluno){
            Painel::alert("error", "Historico nao Encontrado");
            die();
        }



    } else {
        Painel::alert("error", "ID de aluno inválido.");
        die();
    }

    function formatarDataHora($dataHora) {
        if (!$dataHora) return ""; // Evita erro em valores vazios

        $dataHora = htmlentities($dataHora);

        // Quebrar data e hora
        $partes = explode(' ', $dataHora);
        $data = $partes[0];
        $hora = $partes[1] ?? "";

        // Converter para BR
        $dataBR = implode("/", array_reverse(explode("-", $data)));

        return $hora ? "$dataBR às $hora" : $dataBR;
    }
?>
<div class="box-content">
    
    <div>
        <h3>
            Ficha de: <?php echo htmlentities($dados_aluno->getNome() . ' ' . $dados_aluno->getSobrenome()); ?>
        </h3>

        <p><strong>Usuário:</strong> <?php echo htmlentities($dados_aluno->getUsuario()); ?></p>
        <p><strong>Matrícula:</strong> <?php echo htmlentities($dados_aluno->getMatricula()); ?></p>
        <p><strong>E-mail:</strong> <?php echo htmlentities($dados_aluno->getEmail()); ?></p>
    </div>
    <div class="wraper-table">
        <table>
            <tr>
                <td>Inicio bloqueio</td>
                <td>Fim do bloqueio</td>
                <td>Admin Bloqueio Nome</td>
                <td>Admin Desbloqueio Nome</td>
                <td> Motivo Bloqueio </td>
                <td> Motivo Desbloqueio </td>

            </tr>
            <?php
            if($historicos_bloqueios_aluno){
                foreach($historicos_bloqueios_aluno as $bloqueios){
            ?>
        
            <tr>
                <td> <?php echo htmlentities(formatarDataHora($bloqueios['data_bloqueio']))?> </td>
                <td> <?php echo htmlentities(formatarDataHora($bloqueios['data_desbloqueio']))?></td>
                <td> <?php echo htmlentities($bloqueios['admin_bloqueio_nome'])?></td>
                <td> <?php echo htmlentities($bloqueios['admin_desbloqueio_nome'])?></td>
                <td> <?php echo htmlentities($bloqueios['motivo_bloqueio'])?></td>
                <td> <?php echo htmlentities($bloqueios['motivo_desbloqueio'])?></td>
            </tr>
        <?php }}?>
        </table>
    </div>

    <div class="box-operacoes">
        <a href="<?php echo INCLUDE_PATH_PAINEL ?>gerar-historico-bloqueio?id=<?php echo htmlentities($dados_aluno->getId()); ?>" class="operacao">
            Gerar relatório de bloqueio <i class="fa fa-file-pdf"></i>
        </a>
    </div>
</div>