<?php
    // Inicia o buffer (Segurança contra outputs acidentais)
    ob_start();

    // Verificação de permissão
    verificaPermissaoPagina(3);

    if(isset($_GET['id']) && (int)$_GET['id'] > 0){
        $idAluno = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);

        // Busca os dados (Se retornar Array ou Objeto, a classe PDF nova lida com ambos)
        $dadosAluno = Usuario::select('id = ?', [$idAluno]);
        
        if(!$dadosAluno){
            ob_end_clean();
            die("Erro: Aluno não encontrado no banco de dados.");
        }

        // Busca histórico
        $historico_bloqueio = HistoricoBloqueios::getHistoricoCompleto($idAluno);

    } else {
        ob_end_clean();
        die("Erro: ID de aluno inválido.");
    }

    try {
        // PONTO CRÍTICO: Limpa todo o lixo do buffer antes de iniciar o PDF
        ob_end_clean(); 

        // Instancia a classe
        // PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT vêm do config do TCPDF
        $pdf = new HistoricoBloqueiosPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        
        // Gera o relatório
        $pdf->gerarRelatorio($dadosAluno, $historico_bloqueio);
        
    } catch(Exception $e){
        // Caso extremo de erro
        die('Erro crítico ao gerar PDF: ' . $e->getMessage());
    }
    
    // Encerra execução
    exit;
?>