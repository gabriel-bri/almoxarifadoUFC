<?php  
    class RelatorioPedidos extends TCPDF {        
        private static $dataFinal;
        private static $dataInicial;
        private static $tipoRelatorio;

        public function getDataHoje(){
            return date("d/m/Y");
        }
        
        public function setTipoRelatorio($tipoRelatorio){
            self::$tipoRelatorio = $tipoRelatorio;
        }

        public function getTipoRelatorio(){
            return self::$tipoRelatorio;
        }

        public function setDataInicial($dataInicial){
            self::$dataInicial = $dataInicial;
        }

        public function setDataFinal($dataFinal){
            self::$dataFinal = $dataFinal;
        }

        public function getDataInicial(){
           return self::$dataInicial;
        }

        public function getDataFinal(){
            return self::$dataFinal;
        }
        
        public function Footer() {
            // Rodapé UFC
            $this->SetY(285);
            $this->SetFont('helvetica', '', 8);
            $this->Cell(0, 10, "© " . date("Y") . " - Universidade Federal do Ceará - Campus Quixadá.", 0, false, 'C', 0, '', 0, false, 'T', 'M');
            $this->Cell(0, 10, $this->getAliasNumPage().'/'.$this->getAliasNbPages(), 0, false, 'R', 0, '', 0, false, 'T', 'M');
        }

        //Page header
        public function Header() {
            // Logo
            $image_file = K_PATH_IMAGES.'ufc_logo.jpg';
            $this->Image($image_file, 9, 1, 70, 22, 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
            // Set font
            $this->SetFont('helvetica', 'B', 20);

            // Definir a fonte em negrito e o tamanho para o primeiro título
            $this->SetFont('helvetica', 'B', 16);
            $this->SetTextColor(0, 0, 0); // Cor preta

            // Adicionar o primeiro título em negrito
            $this->SetXY(80, 0);
            $this->Cell(0, 10, "Universidade Federal do Ceará", 0, 1, 'L');
            // Definir a fonte em negrito e o tamanho para o segundo título
            $this->SetFont('helvetica', 'B', 12);

            // Adicionar o segundo título em negrito
            $this->SetXY(80, 5);
            $this->Cell(0, 10, "Campus Quixadá - Av. José de Freitas Queiroz, 5003, Cedro", 0, 1, 'L');

            // $this->SetXY(85, 10);
            // $this->Cell(0, 10, "Av. José de Freitas Queiroz, 5003, Cedro, Quixadá – Ceará ", 0, 1, 'L');

            $this->SetXY(80, 10);
            $this->Cell(0, 10, "Quixadá – Ceará, CEP: 63902-580", 0, 1, 'L');

            $this->SetXY(80, 15);
            $this->Cell(0, 10, "Coordenação do Curso de Engenharia da Computação", 0, 1, 'L');

            // Desenhar a linha horizontal
            $this->Line(10, 25, 200, 25);
        }

        public function ExibirInformacoes() {
            // Configurar a fonte e tamanho para as informações
            $this->SetFont('helvetica', '', 14);
            $this->SetTextColor(0);

            // Exibir as informações
            $this->Cell(0, 0, 'Relatório gerado em: ' . $this->getDataHoje(), 0, 1, 'L');

            // Exibir as informações
            if($this->getTipoRelatorio() == 1) {
                // Extrair apenas a parte da data
                $dataInicial = explode(' ', $this->getDataInicial())[0]; // 'YYYY-MM-DD'

                // Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
                $dataInicial = implode("/", array_reverse(explode("-", $dataInicial)));

                // Extrair apenas a parte da data
                $dataFinal = explode(' ', $this->getDataFinal())[0]; // 'YYYY-MM-DD'

                // Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
                $dataFinal = implode("/", array_reverse(explode("-", $dataFinal)));

                $this->Cell(0, 0, 'Mostrando resultados entre: ' . $dataInicial . ' e ' . $dataFinal, 0, 1, 'L');
            }

            else if($this->getTipoRelatorio() == 2) {
                $this->Cell(0, 0, 'Mostrando resultados para todo o período.', 0, 1, 'L');
            }

            else {
                $this->Cell(0, 0, 'Mostrando somente pedidos ativos atualmente.', 0, 1, 'L');
            }
        }

        public function gerarTabela() {  
            // Configurar cabeçalho da tabela
            $headerUsuario = array('Nome',     'Sobrenome', 'Matrícula');
            $headerPedido = array('Item', 'Quantidade', 'Tipo');
            $this->SetFont('helvetica', '', 15);

            $this->SetY($this->GetY() + 5);

            // Adicionar linhas da tabela
            $this->SetFont('dejavusans', '', 15, '', true);            
            $this->Ln(10);
            
            // Obtém os detalhes dos pedidos com base no tipo de relatório
            switch ($this->getTipoRelatorio()) {
                case 1:
                    $pedidoDetalhes = PedidoDetalhes::retornaPedidosFinalizadosByData($this->getDataInicial(), $this->getDataFinal());
                    break;
                case 2:
                    $pedidoDetalhes = PedidoDetalhes::retornaTodosPedidosFinalizados();
                    break;
                case 3:
                    $pedidoDetalhes = PedidoDetalhes::retornaPedidosNaoFinalizados();
                    break;
                default:
                    $pedidoDetalhes = PedidoDetalhes::retornaTodosPedidosFinalizados();
                    break;
            }

            $contaItens = 0;

            foreach ($pedidoDetalhes as $pedidoDetalhe) {
                // Adicionar cabeçalho da tabela
                foreach ($headerUsuario as $col) {
                    $this->Cell(60, 10, $col, 0, 0, 'L');   
                }

                $this->Line(16, $this->GetY() - 5, 200, $this->GetY() - 5);

                $this->Ln(7);
                $this->Cell(60, 10, $pedidoDetalhe->usuario->getNome(), 0, 0, 'L');
                $this->Cell(60, 10, $pedidoDetalhe->usuario->getSobrenome(), 0, 0, 'L');
                $this->Cell(60, 10, htmlentities($pedidoDetalhe->usuario->getMatricula()), 0, 0, 'L');
                $this->Ln(30);
                
                // Adicionar cabeçalho da tabela
                foreach ($headerPedido as $col) {
                    $this->Cell(60, 10, $col, 0, 0, 'L');
                }
                                
                $itensPedido = PedidoDetalhes::itensViaIDDetalhe($pedidoDetalhe->getId());

                foreach ($itensPedido as $itemPedido) {
                    $this->Ln();
                                        
                    if($itemPedido->estoque->isAtivado() == false) {
                        $itemPedido->estoque->setNome($itemPedido->estoque->getNome() . ' *');
                    }

                    $this->Cell(60, 10, $itemPedido->estoque->getNome(), 0, 0, 'L');
                    $this->Cell(60, 10, htmlentities($itemPedido->getQuantidadeItem()), 0, 0, 'L');
                    $this->Cell(60, 10, tipoEstoque(htmlentities($itemPedido->estoque->getTipo())), 0, 0, 'L');
                    $this->Ln();
                }
                // Extrair apenas a parte da data
                $dataPedido = explode(' ', $pedidoDetalhe->getDataPedido())[0]; // 'YYYY-MM-DD'

                // Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
                $dataPedido = implode("/", array_reverse(explode("-", $dataPedido)));

                // Extrair apenas a parte da hora
                $horaCompletaPedido = explode(' ', $pedidoDetalhe->getDataPedido())[1]; // 'HH:MM:SS'

                $this->Cell(60, 10, "Data pedido: " . $dataPedido . " às " . $horaCompletaPedido, 0, 0, 'L');
                $this->Ln();
                if($this->getTipoRelatorio() == 1 || $this->getTipoRelatorio() == 2) {
                    // Extrair apenas a parte da data
                    $dataFinalizado = explode(' ', $pedidoDetalhe->getDataFinalizado())[0]; // 'YYYY-MM-DD'

                    // Converter o formato de 'YYYY-MM-DD' para 'DD/MM/YYYY'
                    $dataFinalizado = implode("/", array_reverse(explode("-", $dataFinalizado)));

                    // Extrair apenas a parte da hora
                    $horaCompletaFinalizado = explode(' ', $pedidoDetalhe->getDataFinalizado())[1]; // 'HH:MM:SS'

                    $this->Cell(60, 10, "Data finalização: " . $dataFinalizado . " às " . $horaCompletaFinalizado, 0, 0, 'L');
                }

                else {
                    $this->Cell(60, 10, "Data finalização: Não finalizado.", 0, 0, 'L');
                }
                $this->Ln();
                $this->Cell(60, 10, "Código do pedido: " . htmlentities($pedidoDetalhe->getCodigoPedido()), 0, 0, 'L');
                $this->Ln();

                $this->Ln();
                  
                $contaItens++;
            }

            $this->Ln();

            $pedidosTexto = $contaItens == 1 ? " pedido" : " pedidos";
            
            $mensagemRelatorio = "<p> Ao todo foram <strong>{$contaItens} {$pedidosTexto}</strong> ao longo do período.</p>";
            $mensagemRelatorio .= "<p>Itens marcados com um <b>*(asterisco)</b> estão temporiamente desativados para empréstimos.</p>";                        
            $this->writeHTML($mensagemRelatorio, true, false, true, false, '');
        }

        /**
         * Valida e processa a geração do relatório com base no tipo especificado.
         *
         * @param string $tipo Tipo do relatório a ser gerado.
         * @return void
         */

        //  Relatório do tipo 1 -> Período de datas espeficado pelo o usuário.
        //  Relatório do tipo 2 -> Todo os anos.
        //  Relatório do tipo 3 -> Todo os pedidos ativos.
        public function validarRelatorio($tipo) {
            // Obtém a data atual
            $dataHoje = date("Y-m-d");;

            // Define o tipo de relatório
            $this->setTipoRelatorio($tipo);
            
            // Verifica se as datas iniciais e finais foram fornecidas
            if($this->getTipoRelatorio() == 1 && (empty($_POST['dataInicial']) || empty($_POST['dataFinal']))) {
                Painel::alert("erro", "As datas não podem ser vazias");
                return;
            }
            
            // Verifica se a data inicial é maior que a data final
            if($this->getTipoRelatorio() == 1 && ($_POST['dataInicial'] > $_POST['dataFinal'])){
                Painel::alert("erro", "A data inicial não pode ser maior que a data final");
                return;
            }
            
            // Verifica se a data final é menor que a data inicial
            if($this->getTipoRelatorio() == 1 && ($_POST['dataFinal'] < $_POST['dataInicial'])){
                Painel::alert("erro", "A data final não pode ser menor que a data inicial");
                return;
            }
            
            // Verifica se a data final é maior que a data atual
            if($this->getTipoRelatorio() == 1 && ($_POST['dataFinal'] > $dataHoje)) {
                Painel::alert("erro", "A data final não pode ser maior que hoje");
                return;
            }
            
            // Valida as datas no formato Y-m-d
            $validaDataInicial = DateTime::createFromFormat('Y-m-d', $_POST['dataInicial']);
            $validaDataFinal = DateTime::createFromFormat('Y-m-d', $_POST['dataFinal']);
            
            // Se as datas não são válidas, exibe um erro
            if ($this->getTipoRelatorio() == 1 && (!$validaDataInicial || !$validaDataFinal)) {
                Painel::alert("erro", "Data inválida");
                return;
            }
            
            // Obtém as datas inicial e final fornecidas
            $dataInicial = $_POST['dataInicial'] . ' 00:00:00';
            $dataFinal = $_POST['dataFinal'] . ' 23:59:59';

            // Obtém os detalhes dos pedidos com base no tipo de relatório
            switch ($this->getTipoRelatorio()) {
                case 1:
                    $pedidoDetalhes = PedidoDetalhes::retornaPedidosFinalizadosByData($dataInicial, $dataFinal);
                    break;
                case 2:
                    $pedidoDetalhes = PedidoDetalhes::retornaTodosPedidosFinalizados();
                    break;
                case 3:
                    $pedidoDetalhes = PedidoDetalhes::retornaPedidosNaoFinalizados();
                    break;
                default:
                    $pedidoDetalhes = PedidoDetalhes::retornaTodosPedidosFinalizados();
                    break;
            }

            // Se nenhum registro foi encontrado, exibe uma mensagem de erro
            if($pedidoDetalhes == false) {
                Painel::alert("erro", "Nenhum registro foi encontrado para o período informado");
                return;
            }

            // Define as datas inicial e final no objeto e gera o PDF
            $this->setDataInicial($dataInicial);
            $this->setDataFinal($dataFinal);
            $this->gerarPDF();
        }


        private function gerarPDF() {
            $pdf = new RelatorioPedidos(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // set document information

            $pdf->SetTitle("Relatório de pedidos - " . $this->getDataHoje());
            $pdf->SetSubject("Relatório de pedidos - " . $this->getDataHoje());
            $pdf->SetKeywords('relatorio', 'pedidos');

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

            $pdf->ExibirInformacoes();
            $pdf->gerarTabela();
            // ---------------------------------------------------------

            // Close and output PDF document
            // This method has several options, check the source code documentation for more information.
            ob_end_clean();
            $pdf->Output('relatorio_pedidos.pdf', 'I');
        }
    }
?>