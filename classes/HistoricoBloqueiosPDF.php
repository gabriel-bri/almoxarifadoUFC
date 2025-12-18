<?php

class HistoricoBloqueiosPDF extends TCPDF {

    public function Header() {
        $image_file = K_PATH_IMAGES . 'ufc_logo.jpg';
        $this->Image($image_file, 10, 5, 70, 22, 'JPG');

        $this->SetFont('helvetica', 'B', 16);
        $this->SetXY(85, 5);
        $this->Cell(0, 8, "Universidade Federal do Ceará", 0, 1);

        $this->SetFont('helvetica', 'B', 12);
        $this->SetX(85);
        $this->Cell(0, 6, "Campus Quixadá", 0, 1);

        $this->SetFont('helvetica', '', 10);
        $this->SetX(85);
        $this->Cell(0, 6, "Av. José de Freitas Queiroz, 5003, Cedro, Quixadá – Ceará", 0, 1);

        $this->SetX(85);
        $this->Cell(0, 6, "CEP: 63902-580", 0, 1);

        $this->SetX(85);
        $this->Cell(0, 6, "Coordenação do Curso de Engenharia da Computação", 0, 1);

        $this->Line(10, 35, 200, 35);
    }

    public function Footer() {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 8);
        $this->Cell(0, 10, "Gerado em: " . date("d/m/Y H:i") . " - © UFC Quixadá", 0, 0, 'L');
        $this->Cell(0, 10, 'Página ' . $this->getAliasNumPage() . '/' . $this->getAliasNbPages(), 0, 0, 'R');
    }

    public function gerarRelatorio(Usuario $aluno, array $historico) {

        $this->AddPage();
        $this->SetY(40);

        // Título
        $this->SetFont('helvetica', 'B', 16);
        $this->Cell(0, 10, "Relatório de Histórico de Bloqueios", 0, 1, 'C');

        $this->SetFont('helvetica', '', 11);
        $htmlAluno = '
        <table cellpadding="6" cellspacing="0" style="width:100%; background:#f9f9f9;">
            <tr>
                <td style="border:1px solid #ccc;">
                    <strong>Aluno:</strong> '.$aluno->getNome().' '.$aluno->getSobrenome().'<br>
                    <strong>Matrícula:</strong> '.$aluno->getMatricula().'<br>
                    <strong>Total de Ocorrências:</strong> '.count($historico).'
                </td>
            </tr>
        </table>';
        $this->writeHTML($htmlAluno, true, false, true, false, '');

        $this->Ln(5);

        $this->SetFont('helvetica', 'B', 9);

        $w = [30, 30, 50, 30, 30, 50];
        $headers = [
            'Início Bloqueio',
            'Admin Bloqueio',
            'Motivo Bloqueio',
            'Fim Bloqueio',
            'Admin Desbloqueio',
            'Motivo Desbloqueio'
        ];

        foreach ($headers as $i => $h) {
            $this->MultiCell($w[$i], 8, $h, 1, 'C', false, 0);
        }
        $this->Ln();

        $this->SetFont('helvetica', '', 9);

        foreach ($historico as $b) {

            $inicio = $b['data_bloqueio']
                ? date('d/m/Y H:i', strtotime($b['data_bloqueio']))
                : '-';

            $adminBloq = $b['admin_bloqueio_nome'] ?? '-';
            $motivoBloq = $b['motivo_bloqueio'] ?? '-';

            if (!empty($b['data_desbloqueio'])) {
                $fim = date('d/m/Y H:i', strtotime($b['data_desbloqueio']));
                $adminDes = $b['admin_desbloqueio_nome'] ?? '-';
                $motivoDes = $b['motivo_desbloqueio'] ?? '-';
            } else {
                $fim = 'ATIVO';
                $adminDes = '-';
                $motivoDes = '-';
            }

            $h = max(
                $this->getStringHeight($w[0], $inicio),
                $this->getStringHeight($w[1], $adminBloq),
                $this->getStringHeight($w[2], $motivoBloq),
                $this->getStringHeight($w[3], $fim),
                $this->getStringHeight($w[4], $adminDes),
                $this->getStringHeight($w[5], $motivoDes)
            );

            $x = $this->GetX();
            $y = $this->GetY();

            $this->MultiCell($w[0], $h, $inicio, 1, 'C', false, 0, $x, $y);
            $this->MultiCell($w[1], $h, $adminBloq, 1, 'C', false, 0, $x+$w[0], $y);
            $this->MultiCell($w[2], $h, $motivoBloq, 1, 'L', false, 0, $x+$w[0]+$w[1], $y);
            $this->MultiCell($w[3], $h, $fim, 1, 'C', false, 0, $x+$w[0]+$w[1]+$w[2], $y);
            $this->MultiCell($w[4], $h, $adminDes, 1, 'C', false, 0, $x+$w[0]+$w[1]+$w[2]+$w[3], $y);
            $this->MultiCell($w[5], $h, $motivoDes, 1, 'L', false, 1, $x+$w[0]+$w[1]+$w[2]+$w[3]+$w[4], $y);
        }

        if (ob_get_length()) {
            ob_end_clean();
        }

        $this->Output('historico_bloqueios.pdf', 'I');
    }
}
