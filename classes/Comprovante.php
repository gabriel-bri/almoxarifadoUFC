<?php  
    class Comprovante extends TCPDF {
        /**
        * Método responsável por definir o rodapé do documento PDF.
        * Este rodapé exibe informações sobre a universidade e o número da página.
        * @return void
        */        
        public function Footer() {
            // Rodapé UFC
            $this->SetY(285);
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 10, "© " . date("Y") . " - Universidade Federal do Ceará - Campus Quixadá.", 0, false, 'C', 0, '', 0, false, 'T', 'M');
            $this->Cell(0, 10, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        }

        /**
        * Método responsável por definir o cabeçalho do documento PDF.
        * Este cabeçalho inclui o logotipo da universidade e outras informações.
        * @return void
        */
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

        /**
         * Método responsável por exibir as informações do pedido no documento PDF.
         * Este método recebe um objeto PedidoDetalhes e adiciona as informações do pedido ao documento.
         * @param PedidoDetalhes $pedidoDetalhes O objeto que contém os detalhes do pedido.
         * @return void
         */
        public function ExibirInformacoesPedido(PedidoDetalhes $pedidoDetalhes) {
            $this->SetY(40); // Mude para o valor desejado para posicionar mais abaixo
            $this->SetFont('helvetica', 'B', 20);
            $this->Cell(0, 10, 'COMPROVANTE DE PEDIDO', 0, 1, 'C');

            // Configurar a fonte e tamanho para as informações do pedido
            $this->SetFont('helvetica', '', 14);
            $this->SetTextColor(0);

            // Adicionar espaçamento antes das informações do pedido
            $this->Ln(10);

            // Exibir as informações do pedido
            $this->Cell(0, 0, 'Pedido feito por: ' . $pedidoDetalhes->usuario->getNome() . ' ' . $pedidoDetalhes->usuario->getSobrenome(), 0, 1, 'L');
            $this->Cell(0, 0, 'Matrícula: ' . $pedidoDetalhes->usuario->getMatricula(), 0, 1, 'L');
            $this->Cell(0, 0, 'Data do pedido: ' . implode("/",array_reverse(explode("-",$pedidoDetalhes->getDataPedido()))), 0, 1, 'L');
            $this->Cell(0, 0, 'Código do pedido: ' . $pedidoDetalhes->getCodigoPedido(), 0, 1, 'L');
        }

        /**
         * Gera uma tabela com os itens do pedido especificado e adiciona ao documento PDF.
         * Este método utiliza os detalhes do pedido fornecidos para criar uma tabela contendo os itens do pedido.
         * @param PedidoDetalhes $pedidoDetalhes O objeto que contém os detalhes do pedido.
         * @return void
         */
        public function gerarTabela(PedidoDetalhes $pedidoDetalhes) {
            // Configurar cabeçalho da tabela
            $header = array('Item', 'Quantidade', 'Tipo');
            $this->SetFont('helvetica', 'B', 12);
            $this->SetFillColor(169, 169, 169); // Cinza
            $this->SetTextColor(0);
            $this->SetDrawColor(0);
            $this->SetLineWidth(0.3);

            $this->SetY($this->GetY() + 5);
            // Adicionar cabeçalho da tabela
            foreach ($header as $col) {
                $this->Cell(60, 10, $col, 1, 0, 'C', 1);
            }
            $this->Ln();

            $alternarCor = false; // Variável para alternar a cor de fundo
            // Adicionar linhas da tabela
            $itensPedido = PedidoDetalhes::itensViaIDDetalhe($pedidoDetalhes->getId());
            
            $this->SetFont('helvetica', 'B', 12);

            foreach ($itensPedido as $itemPedido) {
                $this->SetFillColor($alternarCor ? 230 : 240, $alternarCor ? 230 : 240, $alternarCor ? 230 : 240);

                $this->Cell(60, 10, $itemPedido->estoque->getNome(), 1, 0, 'C', 1);
                $this->Cell(60, 10, $itemPedido->getQuantidadeItem(), 1, 0, 'C', 1);
                $this->Cell(60, 10, tipoEstoque($itemPedido->estoque->getTipo()), 1, 0, 'C', 1);
                $this->Ln();

                // Alternar a cor para a próxima linha
                $alternarCor = !$alternarCor;
            }
        }

        /**
         * Gera um arquivo PDF contendo os detalhes do pedido especificado e o retorna como uma string.
         * Este método utiliza os detalhes do pedido fornecidos para criar um documento PDF que serve como comprovante do pedido.
         * @param PedidoDetalhes $pedidoDetalhes O objeto que contém os detalhes do pedido.
         * @return string O conteúdo do arquivo PDF gerado.
         */
        public function gerarPDF(PedidoDetalhes $pedidoDetalhes) {
            $pdf = new Comprovante(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // set document information
            $pdf->SetTitle('Comprovante do pedido ' . $pedidoDetalhes->getCodigoPedido());
            $pdf->SetSubject('Comprovante do pedido ' . $pedidoDetalhes->getCodigoPedido());
            $pdf->SetKeywords('comprovante');

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

            $pdf->ExibirInformacoesPedido($pedidoDetalhes);
            $pdf->gerarTabela($pedidoDetalhes);
            
            // ---------------------------------------------------------

            // Close and output PDF document
            // This method has several options, check the source code documentation for more information.
            /*
            * Gera um arquivo PDF do comprovante de pedido e o salva no diretório especificado.
            * O arquivo PDF é gerado com base nos detalhes do pedido fornecidos.
            */
            $pdf->Output(BASE_DIR_PAINEL . 'comprovantes/' . $pedidoDetalhes->getCodigoPedido() .'.pdf', 'F');
        }
    }
?>