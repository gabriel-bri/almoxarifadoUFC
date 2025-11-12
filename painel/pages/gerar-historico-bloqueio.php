<?php
    @ob_end_clean();
    @ob_end_clean(); 
    
    verificaPermissaoPagina(3);

    if(isset($_GET['id']) && (int)$_GET['id'] > 0){
        $idAluno = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        $dadosAluno = Usuario::select('id = ?', [$idAluno]);
        $historico_bloqueio = HistoricoBloqueios::getHistoricoCompleto($idAluno);

        if(!$dadosAluno){
            die("Error: Aluno nao encontrado");
        }
    } else{
        die("Erro: ID de aluno inválido.");
    }

    try{

        $pdf = new HistoricoBloqueiosPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->gerarRelatorio($dadosAluno, $historico_bloqueio);
        
    } catch(Exception $e){
        die('Erro ao gerar PDF' . $e->getMessage());
    }
exit;
die();
?>