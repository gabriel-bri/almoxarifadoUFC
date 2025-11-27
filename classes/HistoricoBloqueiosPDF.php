<?php
// Ativar exibição de erros para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// Log de erros personalizado
ini_set('log_errors', 1);
ini_set('error_log', '/tmp/php_errors.log');

class HistoricoBloqueiosPDF extends TCPDF {
    protected $codigoSeguranca;

    public function Footer() {
        try {
            // Posição a 1.5 cm do final 
            $this->SetY(-15);
            // Fonte
            $this->SetFont('helvetica', 'I', 8);
            // Texto de copyright
            $this->Cell(0, 10, "Gerado em: " . date("d/m/Y H:i") . " - © UFC Quixadá", 0, false, 'L', 0, '', 0, false, 'T', 'M');
            // Número da página
            $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        } catch (Exception $e) {
            error_log("Erro no Footer: " . $e->getMessage());
        }
    }

    //Page header
    public function Header() {
        try {
            // Logo
            $image_file = K_PATH_IMAGES.'ufc_logo.jpg';
            if (!file_exists($image_file)) {
                throw new Exception("Arquivo de imagem não encontrado: " . $image_file);
            }
            $this->Image($image_file, 10, 5, 70, 22, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            
            $this->SetFont('helvetica', 'B', 16);
            $this->SetTextColor(0, 0, 0);

            $this->SetXY(85, 2);
            $this->Cell(0, 10, "Universidade Federal do Ceará", 0, 1, 'L');
            
            $this->SetFont('helvetica', 'B', 12);
            $this->SetXY(85, 8);
            $this->Cell(0, 10, "Campus Quixadá", 0, 1, 'L');

            $this->SetXY(85, 13);
            $this->Cell(0, 10, "Av. José de Freitas Queiroz, 5003, Cedro, Quixadá – Ceará ", 0, 1, 'L');

            $this->SetXY(85, 18);
            $this->Cell(0, 10, "CEP: 63902-580", 0, 1, 'L');

            $this->SetXY(85, 23);
            $this->Cell(0, 10, "Coordenação do Curso de Engenharia da Computação", 0, 1, 'L');

            // Desenhar a linha horizontal
            $this->Line(10, 35, 200, 35);
        } catch (Exception $e) {
            error_log("Erro no Header: " . $e->getMessage());
        }
    }

    // ✅ CORREÇÃO: Método de INSTÂNCIA (não estático)
    public function gerarRelatorio(Usuario $aluno, $historico) {
        try {
            // Configurações básicas do documento
            $this->SetCreator(PDF_CREATOR);
            $this->SetAuthor('Sistema Almoxarifado');
            $this->SetTitle('Relatório de Histórico - ' . $aluno->getMatricula());
            $this->SetSubject('Relatório de Histórico de Bloqueios');
            $this->SetKeywords('relatorio, historico, bloqueio');

            // Configurações de fonte e margens
            $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $this->SetMargins(15, 40, 15); // left, top, right
            $this->SetHeaderMargin(10);
            $this->SetFooterMargin(10);
            $this->SetAutoPageBreak(TRUE, 15);
            $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $this->setFontSubsetting(true);

            // Add a page
            $this->AddPage();
            $this->SetFont('helvetica', '', 10);

            // --- CONTEÚDO DO PDF ---
            $html = '
            <h1 style="text-align:center;">Relatório de Histórico de Bloqueios</h1>
            
            <div style="background-color: #f9f9f9; border: 1px solid #eee; padding: 10px; margin-bottom: 10px;">
                <p><strong>Aluno:</strong> ' . htmlentities($aluno->getNome() . ' ' . $aluno->getSobrenome()) . '</p>
                <p><strong>Matrícula:</strong> ' . htmlentities($aluno->getMatricula()) . '</p>
                <p><strong>Total de Ocorrências:</strong> ' . ($historico ? count($historico) : 0) . '</p>
            </div>';

            if($historico && count($historico) > 0) {
                $html .= '<h3>Histórico de Ocorrências</h3>
                <table border="1" cellpadding="4" cellspacing="0" style="width:100%; font-size:8pt;">
                    <thead style="background-color: #f0f0f0; font-weight: bold;">
                        <tr>
                            <th width="15%">Início Bloqueio</th>
                            <th width="15%">Admin Bloqueio</th>
                            <th width="25%">Motivo Bloqueio</th>
                            <th width="15%">Fim Bloqueio</th>
                            <th width="15%">Admin Desbloqueio</th>
                            <th width="15%">Motivo Desbloqueio</th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach($historico as $bloqueios) {
                    $html .= '<tr>';
                    $html .= '<td>' . date('d/m/Y H:i', strtotime($bloqueios['data_bloqueio'])) . '</td>';
                    $html .= '<td>' . htmlentities($bloqueios['admin_bloqueio_nome']) . '</td>';
                    $html .= '<td>' . nl2br(htmlentities($bloqueios['motivo_bloqueio'])) . '</td>';
                
                    if ($bloqueios['data_desbloqueio']) {
                        $html .= '<td>' . date('d/m/Y H:i', strtotime($bloqueios['data_desbloqueio'])) . '</td>';
                        $html .= '<td>' . htmlentities($bloqueios['admin_desbloqueio_nome']) . '</td>';
                        $html .= '<td>' . nl2br(htmlentities($bloqueios['motivo_desbloqueio'])) . '</td>';
                    } else {
                        $html .= '<td colspan="3" style="text-align:center; color:red; font-weight:bold;">BLOQUEIO ATIVO</td>';
                    }
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
            } else {
                $html .= '<p style="text-align:center; font-style:italic;">Nenhum registro de bloqueio encontrado.</p>';
            }

            $this->writeHTML($html, true, false, true, false, '');
            
            // Gerar o PDF
            $nome_arquivo = "historico_" . $aluno->getMatricula() . ".pdf";
            
            // Limpar buffer de saída
            if (ob_get_length()) ob_clean();
            
            // Output do PDF
            $this->Output($nome_arquivo, 'I');
            
        } catch (Exception $e) {
            // Log do erro
            error_log("Erro ao gerar PDF: " . $e->getMessage());
            
            // Mensagem de erro amigável
            echo "Erro ao gerar relatório: " . $e->getMessage();
            echo "<br>Entre em contato com o administrador do sistema.";
            exit;
        }
    }
}
?>