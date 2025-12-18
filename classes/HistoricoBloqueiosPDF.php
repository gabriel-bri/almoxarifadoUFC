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

        public function gerarRelatorio(Usuario $aluno, $historico) {
            $this->SetTitle('Relatório de Histórico - ' . $aluno->getMatricula());
            $this->SetSubject('Relatório de Histórico de Bloqueios');
            $this->SetKeywords('relatorio, historico, bloqueio');

            $this->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $this->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
            $this->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // ajustar margens para dar espaço ao cabeçalho/rodapé
            $this->SetMargins(10, PDF_MARGIN_TOP + 5, 10);
            $this->SetHeaderMargin(PDF_MARGIN_HEADER);
            $this->SetFooterMargin(PDF_MARGIN_FOOTER);
            $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM + 5);
            $this->SetImageScale(PDF_IMAGE_SCALE_RATIO);
            $this->setFontSubsetting(true);
            $this->SetFont('dejavusans', '', 12, '', true);

            $this->AddPage();

            // Título
            $this->SetFont('helvetica', 'B', 16);
            $this->SetY(40);
            $this->writeHTML("<h1 style=\"text-align:center; margin:0 0 6px 0;\">Relatório de Histórico de Bloqueios</h1>", true, false, true, false, '');

            // Dados do aluno (caixa)
            $this->SetFont('helvetica', '', 11);
            $html_aluno = '
            <table cellpadding="6" cellspacing="0" style="width:100%; border:0; background:#f9f9f9; border-collapse:collapse;">
                <tr>
                    <td style="border:1px solid #eee; font-size:12px;">
                        <strong>Aluno:</strong> ' . htmlentities($aluno->getNome() . ' ' . $aluno->getSobrenome()) . '<br/>
                        <strong>Matrícula:</strong> ' . htmlentities($aluno->getMatricula()) . '<br/>
                        <strong>Total de Ocorrências:</strong> ' . ($historico ? count($historico) : 0) . '
                    </td>
                </tr>
            </table>
            ';
            $this->writeHTML($html_aluno, true, false, true, false, '');

            $this->SetY($this->GetY() + 5);
            $this->SetFont('helvetica', 'B', 14);
            $this->writeHTML("<h3 style=\"margin:6px 0;\">Histórico de Ocorrências</h3>", true, false, true, false, '');
            $this->SetFont('helvetica', '', 9);

            // CSS/HTML da tabela com estilo para evitar "linhas tortas"
            $html_tabela = '
            <style>
                table.historico { table-layout: fixed; width:100%; border-collapse: collapse; font-size:10px; }
                table.historico th, table.historico td { border:1px solid #9b9b9b; padding:6px; vertical-align: middle; word-wrap:break-word; white-space:normal; }
                table.historico thead th { background:#f0f0f0; font-weight:bold; text-align:center; }
                .motivo { font-size:10px; }
                .ativo { color:#c00000; font-weight:bold; text-align:center; }
            </style>
            <table class="historico" cellpadding="4" cellspacing="0">
                <thead>
                    <tr>
                        <th style="width:15%;">Início Bloqueio</th>
                        <th style="width:15%;">Admin Bloqueio</th>
                        <th style="width:25%;">Motivo Bloqueio</th>
                        <th style="width:15%;">Fim Bloqueio</th>
                        <th style="width:15%;">Admin Desbloqueio</th>
                        <th style="width:15%;">Motivo Desbloqueio</th>
                    </tr>
                </thead>
                <tbody>';

            if ($historico) {
                foreach ($historico as $bloqueios) {
                    $html_tabela .= '<tr>';
                    $html_tabela .= '<td style="text-align:center;">' . ($bloqueios['data_bloqueio'] ? date('d/m/Y H:i', strtotime($bloqueios['data_bloqueio'])) : '-') . '</td>';
                    $html_tabela .= '<td style="text-align:center;">' . htmlentities($bloqueios['admin_bloqueio_nome'] ?? '-') . '</td>';
                    // motivo com quebras tratadas
                    $motivoBloq = nl2br(htmlentities($bloqueios['motivo_bloqueio'] ?? '-'));
                    $html_tabela .= '<td class="motivo">' . $motivoBloq . '</td>';
                
                    if (!empty($bloqueios['data_desbloqueio'])) {
                        $html_tabela .= '<td style="text-align:center;">' . date('d/m/Y H:i', strtotime($bloqueios['data_desbloqueio'])) . '</td>';
                        $html_tabela .= '<td style="text-align:center;">' . htmlentities($bloqueios['admin_desbloqueio_nome'] ?? '-') . '</td>';
                        $motivoDesb = nl2br(htmlentities($bloqueios['motivo_desbloqueio'] ?? '-'));
                        $html_tabela .= '<td class="motivo">' . $motivoDesb . '</td>';
                    } else {
                        // mantém colunas com altura e alinhamento corretos
                        $html_tabela .= '<td class="ativo" colspan="3">BLOQUEIO ATIVO</td>';
                    }
                
                    $html_tabela .= '</tr>';
                }
            } else {
                $html_tabela .= '<tr><td colspan="6" style="text-align:center;">Nenhum registro encontrado.</td></tr>';
            }
        
            $html_tabela .= '</tbody></table>';
        
            $this->writeHTML($html_tabela, true, false, true, false, '');
        
            // nome do arquivo - sanitiza
            $nome_arquivo = "historico_" . preg_replace('/[^a-z0-9_]/i', '', $aluno->getUsuario()) . ".pdf";
        
            // limpa buffers (igual NadaConsta)
            if (ob_get_length()) { @ob_end_clean(); }
        
            // Output inline
            $this->Output($nome_arquivo, 'I'); 
        }


    }
?>
