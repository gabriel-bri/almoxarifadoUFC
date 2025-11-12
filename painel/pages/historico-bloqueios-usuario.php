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
    <a href="<?php echo INCLUDE_PATH_PAINEL?>gerar-historico-bloqueio?id=<?php echo $dados_aluno->getId()?>">
        Gerar Relatorio em PDF
    </a>
    
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
                <td> <?php echo htmlentities($bloqueios['data_bloqueio'])?> </td>
                <td> <?php echo htmlentities($bloqueios['data_desbloqueio'])?></td>
                <td> <?php echo htmlentities($bloqueios['admin_bloqueio_nome'])?></td>
                <td> <?php echo htmlentities($bloqueios['admin_desbloqueio_nome'])?></td>
                <td> <?php echo htmlentities($bloqueios['motivo_bloqueio'])?></td>
                <td> <?php echo htmlentities($bloqueios['motivo_desbloqueio'])?></td>
            </tr>
        <?php }}?>
        </table>
    </div>
    <td><a href="<?php echo INCLUDE_PATH_PAINEL?>gerar-historico-bloqueio?id=<?php echo $dados_aluno->getId()?>"> </td>
</div>