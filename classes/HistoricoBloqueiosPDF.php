<?php

class HistoricoBloqueiosPDF extends TCPDF {
    
    // Configuração do Rodapé (Padrão UFC Quixadá - Igual RelatorioEstoque)
    public function Footer() {
        $this->SetY(-15);
        $this->Line(10, 282, 200, 282);
        $this->SetFont('helvetica', '', 8);
        $this->Cell(0, 10, "© " . date("Y") . " - Universidade Federal do Ceará - Campus Quixadá.", 0, false, 'C', 0, '', 0, false, 'T', 'M');
        $this->Cell(0, 10, 'Pág. '.$this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
    }

    // Configuração do Cabeçalho (Padrão UFC Quixadá - Igual RelatorioEstoque)
    public function Header() {
        // Logo
        $image_file = (defined('K_PATH_IMAGES') ? K_PATH_IMAGES : __DIR__ . '/../../img/') . 'ufc_logo.jpg';
        
        if (file_exists($image_file)) {
            $this->Image($image_file, 9, 1, 70, 22, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
        }

        $this->SetFont('helvetica', 'B', 16);
        $this->SetTextColor(0, 0, 0);

        // Títulos (Posicionamento ajustado para bater com RelatorioEstoque)
        $this->SetXY(80, 0);
        $this->Cell(0, 10, "Universidade Federal do Ceará", 0, 1, 'L');
        
        $this->SetFont('helvetica', 'B', 12);
        $this->SetXY(80, 5);
        $this->Cell(0, 10, "Campus Quixadá - Av. José de Freitas Queiroz, 5003, Cedro", 0, 1, 'L');

        $this->SetXY(80, 10);
        $this->Cell(0, 10, "Quixadá – Ceará, CEP: 63902-580", 0, 1, 'L');

        $this->SetXY(80, 15);
        $this->Cell(0, 10, "Coordenação do Curso de Engenharia da Computação", 0, 1, 'L');

        // Linha horizontal
        $this->Line(10, 25, 200, 25);
    }

    // Método principal de geração
    // REMOVIDO "Usuario" da tipagem para aceitar Arrays também
    public function gerarRelatorio($aluno, $historico) {
        try {
            // LÓGICA HÍBRIDA: Prepara as variáveis independente se veio Objeto ou Array
            if (is_object($aluno)) {
                $nomeCompleto = htmlentities($aluno->getNome() . ' ' . $aluno->getSobrenome());
                $matricula = htmlentities($aluno->getMatricula());
            } else {
                // Suporte para quando vem como Array do banco
                $nome = isset($aluno['nome']) ? $aluno['nome'] : '';
                $sobrenome = isset($aluno['sobrenome']) ? $aluno['sobrenome'] : '';
                $nomeCompleto = htmlentities($nome . ' ' . $sobrenome);
                $matricula = isset($aluno['matricula']) ? htmlentities($aluno['matricula']) : '';
            }

            // Metadados
            $this->SetCreator(PDF_CREATOR);
            $this->SetAuthor('Sistema Almoxarifado');
            $this->SetTitle('Histórico - ' . $matricula);
            
            // Margens (Top 35 para não bater no Header)
            $this->SetMargins(15, 35, 15);
            $this->SetHeaderMargin(10);
            $this->SetFooterMargin(10);
            $this->SetAutoPageBreak(TRUE, 15);
            $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
            $this->setFontSubsetting(true);

            // Adiciona Página
            $this->AddPage();
            
            // Fonte Dejavu Sans
            $this->SetFont('dejavusans', '', 10);
            $totalOcorrencias = ($historico ? count($historico) : 0);

            // HTML Conteúdo
            $html = '
            <h2 style="text-align:center;">RELATÓRIO DE HISTÓRICO DE BLOQUEIOS</h2>
            <br>
            <table cellpadding="5" cellspacing="0" border="0" style="width:100%; background-color: #f2f2f2; border: 1px solid #cccccc;">
                <tr>
                    <td>
                        <b>Aluno:</b> ' . $nomeCompleto . '<br>
                        <b>Matrícula:</b> ' . $matricula . '<br>
                        <b>Total de Ocorrências:</b> ' . $totalOcorrencias . '
                    </td>
                </tr>
            </table>
            <br><br>';

            if($historico && count($historico) > 0) {
                $html .= '<table border="1" cellpadding="5" cellspacing="0" style="width:100%; font-size:9pt;">
                    <thead>
                        <tr style="background-color: #e0e0e0; font-weight: bold;">
                            <th width="15%" align="center">Data Bloqueio</th>
                            <th width="15%">Admin</th>
                            <th width="25%">Motivo</th>
                            <th width="15%" align="center">Data Desbloq.</th>
                            <th width="15%">Admin</th>
                            <th width="15%">Motivo</th>
                        </tr>
                    </thead>
                    <tbody>';

                foreach($historico as $bloqueios) {
                    $dataB = date('d/m/Y H:i', strtotime($bloqueios['data_bloqueio']));
                    $adminB = htmlentities($bloqueios['admin_bloqueio_nome']);
                    $motivoB = nl2br(htmlentities($bloqueios['motivo_bloqueio']));
                    
                    $dataD = '-';
                    $adminD = '-';
                    $motivoD = '-';
                    $styleRow = ''; 

                    if ($bloqueios['data_desbloqueio']) {
                        $dataD = date('d/m/Y H:i', strtotime($bloqueios['data_desbloqueio']));
                        $adminD = htmlentities($bloqueios['admin_desbloqueio_nome']);
                        $motivoD = nl2br(htmlentities($bloqueios['motivo_desbloqueio']));
                    } else {
                        $styleRow = 'color: #cc0000;';
                        $dataD = '<b>ATIVO</b>';
                    }

                    $html .= '<tr style="'.$styleRow.'">';
                    $html .= '<td align="center">' . $dataB . '</td>';
                    $html .= '<td>' . $adminB . '</td>';
                    $html .= '<td>' . $motivoB . '</td>';
                    $html .= '<td align="center">' . $dataD . '</td>';
                    $html .= '<td>' . $adminD . '</td>';
                    $html .= '<td>' . $motivoD . '</td>';
                    $html .= '</tr>';
                }

                $html .= '</tbody></table>';
            } else {
                $html .= '<p style="text-align:center; border: 1px dashed #ccc; padding: 10px;">Nenhum registro de bloqueio encontrado para este aluno.</p>';
            }

            $this->writeHTML($html, true, false, true, false, '');
            
            // Saída
            $nome_arquivo = "historico_" . $matricula . ".pdf";
            
            if (ob_get_length()) ob_clean();
            
            $this->Output($nome_arquivo, 'I');
            
        } catch (Exception $e) {
            error_log("PDF Error: " . $e->getMessage());
            die("Erro ao renderizar relatório. Verifique o log.");
        }
    }
}
?>