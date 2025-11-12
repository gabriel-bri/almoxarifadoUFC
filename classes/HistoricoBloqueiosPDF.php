<?php
    
    class HistoricoBloqueiosPDF extends TCPDF {
        protected $codigoSeguranca;

        public function Footer() {
            // Posição a 1.5 cm do final
            $this->SetY(-15);
            // Fonte
            $this->SetFont('helvetica', 'I', 8);
            // Texto de copyright
            $this->Cell(0, 10, "Gerado em: " . date("d/m/Y H:i") . " - © UFC Quixadá", 0, false, 'L', 0, '', 0, false, 'T', 'M');
            // Número da página
            $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        }

        //Page header
        public function Header() {
            // Logo
            $image_file = K_PATH_IMAGES.'ufc_logo.jpg';
            $this->Image($image_file, 10, 5, 70, 22, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            // Set font
            $this->SetFont('helvetica', 'B', 20);

            // Definir a fonte em negrito e o tamanho para o primeiro título
            $this->SetFont('helvetica', 'B', 16);
            $this->SetTextColor(0, 0, 0); // Cor preta

            // Adicionar o primeiro título em negrito
            $this->SetXY(85, 2);
            $this->Cell(0, 10, "Universidade Federal do Ceará", 0, 1, 'L');
            // Definir a fonte em negrito e o tamanho para o segundo título
            $this->SetFont('helvetica', 'B', 12);

            // Adicionar o segundo título em negrito
            $this->SetXY(85, -2 + 10);
            $this->Cell(0, 10, "Campus Quixadá", 0, 1, 'L');

            $this->SetXY(85, 3 + 10);
            $this->Cell(0, 10, "Av. José de Freitas Queiroz, 5003, Cedro, Quixadá – Ceará ", 0, 1, 'L');

            $this->SetXY(85, 8 + 10);
            $this->Cell(0, 10, "CEP: 63902-580", 0, 1, 'L');

            $this->SetXY(85, 13 + 10);
            $this->Cell(0, 10, "Coordenação do Curso de Engenharia da Computação", 0, 1, 'L');

            // Desenhar a linha horizontal
            $this->Line(10, 35, 200, 35);
        }

        // Este é o nosso novo método que vai no lugar do nadaconsta()
        public function gerarRelatorio(Usuario $aluno, $historico) {

            // Configurações do documento (copiadas do seu nadaconsta)
            $this->SetTitle('Relatório de Histórico - ' . $aluno->getMatricula());
            $this->SetSubject('Relatório de Histórico de Bloqueios');
            $this->SetKeywords('relatorio', 'historico', 'bloqueio');
        
            $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);
            $this->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $this->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->SetFooterMargin(PDF_MARGIN_FOOTER);
            $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
            $this->SetImageScale(PDF_IMAGE_SCALE_RATIO);
            $this->setFontSubsetting(true);
            $this->SetFont('dejavusans', '', 12, '', true);
        
            // Adiciona a página
            $this->AddPage();
        
            // --- CONTEÚDO DO PDF ---
            $this->SetFont('helvetica', 'B', 16);
            $this->SetY(40); // Pula o cabeçalho
            $this->writeHTML("<h1>Relatório de Histórico de Bloqueios</h1>", true, false, true, false, 'C');
            $this->SetFont('helvetica', '', 11);
        
            // HTML dos dados do aluno
            $html_aluno = '
            <div style="background-color: #f9f9f9; border: 1px solid #eee; padding: 10px; font-size: 12px;">
                <p><strong>Aluno:</strong> ' . htmlentities($aluno->getNome() . ' ' . $aluno->getSobrenome()) . '</p>
                <p><strong>Matrícula:</strong> ' . htmlentities($aluno->getMatricula()) . '</p>
                <p><strong>Total de Ocorrências:</strong> ' . ($historico ? count($historico) : 0) . '</p>
            </div>
            ';
            $this->writeHTML($html_aluno, true, false, true, false, '');
        
            $this->SetY($this->GetY() + 5); 
            $this->SetFont('helvetica', 'B', 14);
            $this->writeHTML("<h3>Histórico de Ocorrências</h3>", true, false, true, false, 'L');
            $this->SetFont('helvetica', '', 9); // Fonte menor para a tabela
        
            // HTML da tabela
            $html_tabela = '
            <table border="1" cellpadding="4" cellspacing="0">
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
        
            if($historico) {
                foreach($historico as $bloqueios) {
                    $html_tabela .= '<tr>';
                    $html_tabela .= '<td width="15%">' . date('d/m/Y H:i', strtotime($bloqueios['data_bloqueio'])) . '</td>';
                    $html_tabela .= '<td width="15%">' . htmlentities($bloqueios['admin_bloqueio_nome']) . '</td>';
                    $html_tabela .= '<td width="25%">' . nl2br(htmlentities($bloqueios['motivo_bloqueio'])) . '</td>';
                
                    if ($bloqueios['data_desbloqueio']) {
                        $html_tabela .= '<td width="15%">' . date('d/m/Y H:i', strtotime($bloqueios['data_desbloqueio'])) . '</td>';
                        $html_tabela .= '<td width="15%">' . htmlentities($bloqueios['admin_desbloqueio_nome']) . '</td>';
                        $html_tabela .= '<td width="15%">' . nl2br(htmlentities($bloqueios['motivo_desbloqueio'])) . '</td>';
                    } else {
                        $html_tabela .= '<td colspan="3" style="text-align:center; color:red; font-weight:bold;">BLOQUEIO ATIVO</td>';
                    }
                    $html_tabela .= '</tr>';
                }
            } else {
                $html_tabela .= '<tr><td colspan="6" style="text-align:center;">Nenhum registro encontrado.</td></tr>';
            }
        
            $html_tabela .= '</tbody></table>';
        
            $this->writeHTML($html_tabela, true, false, true, false, '');
            

            $nome_arquivo = "historico_" . preg_replace('/[^a-z0-9_]/i', '', $aluno->getUsuario()) . ".pdf";
            //ob_clean();
            $this->Output($nome_arquivo, 'I'); 
        }
    }
?>
