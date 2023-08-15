<?php  
    class Comprovante extends TCPDF {
        protected $company;

        public function setCompany($var){
            $this->company = $var;
        }        
        
        public function Footer() {
            // Rodapé UFC
            $this->SetY(285);
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 10, "© " . date("Y") . " - Universidade Federal do Ceará - Campus Quixadá.", 0, false, 'C', 0, '', 0, false, 'T', 'M');
            $this->Cell(0, 10, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');

            // Desenhar a linha horizontal
            $this->Line(10, 257, 200, 257);
            // Desenhar a linha horizontal
            $this->Line(10, 287, 200, 287);     

            // Coordenadas X e Y onde o texto será posicionado na página
            $x = 50;
            $y = 265;

            // Largura da célula onde o texto será inserido
            $larguraCelula = 130;

            // Altura da célula (espaçamento vertical entre as linhas do texto)
            $alturaCelula = 10;

            // Texto a ser adicionado com quebras de linha
            $texto = "Para verificar a autenticidade deste documento, escaneie o QR Code ou acesse\nhttp://almoxarfiadoec.quixada.ufc.br/painel/nada-consta?codigo=1";

            // Definir a fonte e o tamanho para o texto
            $this->SetFont('helvetica', '', 12);

            // Adicionar o texto com quebras de linha usando MultiCell
            $this->SetXY($x, $y); // Define a posição na página para adicionar a célula
            $this->MultiCell($larguraCelula, $alturaCelula, $texto);

            // set style for barcode
            $style = array(
                'border' => false,
                // 'vpadding' => 'auto',
                // 'hpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            );            
            
            // QRCODE,H : QR-CODE Best error correction
            $this->write2DBarcode('www.tcpdf.org', 'QRCODE,H', 15, 260, 25, 25, $style, 'N');
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

        public function gerarPDF($codigoPedido, $dados) {
            $pdf = new Comprovante(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // set document information
            // $pdf->SetTitle('Comprovante do pedido ' . $codigoPedido);
            // $pdf->SetSubject('Comprovante do pedido ' . $codigoPedido);
            // $pdf->SetKeywords('comprovante');
            // $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,"Comprovante de pedido.", "Código: " . $codigoPedido);
            // $pdf->setFooterData(array(0,64,0), array(0,64,128));

            // // set header and footer fonts
            // $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            // $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $pdf->setLanguageArray($l);
            }

            // ---------------------------------------------------------

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            // dejavusans is a UTF-8 Unicode font, if you only need to
            // print standard ASCII chars, you can use core fonts like
            // helvetica or times to reduce file size.
            $pdf->SetFont('dejavusans', '', 14, '', true);

            // Add a page
            // This method has several options, check the source code documentation for more information.
            $pdf->AddPage();

            // set text shadow effect
            $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

            // Set some content to print
                $html = $dados;

            // Print text using writeHTMLCell()
            $pdf->writeHTMLCell(0, 0, '', '', $html, 0, 1, 0, true, '', true);

            // ---------------------------------------------------------

            // Close and output PDF document
            // This method has several options, check the source code documentation for more information.
            // $pdf->Output('example_001.pdf', 'I');
            $pdf->Output(BASE_DIR_PAINEL . '/comprovantes/' . $codigoPedido .'.pdf', 'F');
        }

        public function nadaconsta($codigoPedido, $dados) {
            $pdf = new Comprovante(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            $pdf->setCompany("olá gay");
            // set document information
            $pdf->SetTitle('Comprovante do pedido ' . $codigoPedido);
            $pdf->SetSubject('Comprovante do pedido ' . $codigoPedido);
            $pdf->SetKeywords('comprovante');
            $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH,"Comprovante de pedido.", "Código: " . $codigoPedido);
            $pdf->setFooterData(array(0,64,0), array(0,64,128));

            // set header and footer fonts
            $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
            $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

            // set default monospaced font
            $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

            // set margins
            $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
            $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
            $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

            // set auto page breaks
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            // set image scale factor
            $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

            // set some language-dependent strings (optional)
            if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
                require_once(dirname(__FILE__).'/lang/eng.php');
                $pdf->setLanguageArray($l);
            }

            // ---------------------------------------------------------

            // set default font subsetting mode
            $pdf->setFontSubsetting(true);

            // Set font
            // dejavusans is a UTF-8 Unicode font, if you only need to
            // print standard ASCII chars, you can use core fonts like
            // helvetica or times to reduce file size.
            $pdf->SetFont('dejavusans', '', 14, '', true);

            // Add a page
            // This method has several options, check the source code documentation for more information.
            $pdf->AddPage();

            // set text shadow effect
            $pdf->setTextShadow(array('enabled'=>true, 'depth_w'=>0.2, 'depth_h'=>0.2, 'color'=>array(196,196,196), 'opacity'=>1, 'blend_mode'=>'Normal'));

            // Set some content to print

            function dataHoje() {
                $fmt = new IntlDateFormatter(
                    'pt_BR',
                    IntlDateFormatter::FULL,IntlDateFormatter::FULL,
                    'America/Fortaleza',
                    IntlDateFormatter::GREGORIAN,
                    'dd/MMMM/YYYY'
                );

                $dataExplode = explode("/", $fmt->format(new DateTime()));
                $data = str_replace($dataExplode[1], ucfirst($dataExplode[1]), $fmt->format(new DateTime()));
                $data = str_replace("/", " de ", $data);
                return $data;
            }

            $pdf->SetXY(10, 70);
            $pdf->writeHTML("<h1>DECLARAÇÃO</h1>", false, false, false, false, 'C');
            
            $pdf->SetXY(10, 100); // Define a posição na página para adicionar a célula
            $texto = '<p>Declaro para os devidos fins que o aluno(a) <b>TESTE TESTE</b>, matrícula <b>111111</b>, do curso de <b>NADA</b> da Universidade Federal do Ceará - Campus de Quixadá, não possui empréstimos ativos ou pendências a resolver com o almoxarifado do referido curso. Esta declaração é válida até a presente data.</p>';

            // Imprima o texto no PDF usando writeHTML()
            $pdf->writeHTML($texto, false, false, false, false, '');
            // Declaro para os devidos fins que o aluno(a), matrícula 111111, do curso de NADA da Universidade Federal do Ceará - Campus de Quixadá, não possui empréstimos ativos ou pendências a resolver com o almoxarifado do referido curso. Esta declaração é válida até a presente data
            $pdf->SetXY(120, 150); // Define a posição na página para adicionar a célula
            $pdf->MultiCell(190, 100, "Quixadá, " . dataHoje(), 0, 'L');
            // // ---------------------------------------------------------
// 
            // Close and output PDF document
            // This method has several options, check the source code documentation for more information.
            $pdf->Output('example_001.pdf', 'I');
            // $pdf->Output(BASE_DIR_PAINEL . '/comprovantes/' . $codigoPedido .'.pdf', 'F');
        }
    }
?>