<?php  
    class RelatorioPedidos extends TCPDF {        
        private static $dataFinal;
        private static $dataInicial;
        private static $tipoRelatorio;
        private $pedidosData = [];

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

            $this->SetXY(80, 10);
            $this->Cell(0, 10, "Quixadá – Ceará, CEP: 63902-580", 0, 1, 'L');

            $this->SetXY(80, 15);
            $this->Cell(0, 10, "Coordenação do Curso de Engenharia da Computação", 0, 1, 'L');

            // Desenhar a linha horizontal
            $this->Line(10, 25, 200, 25);
        }

        private function limitarTexto($texto, $limite) {
            $texto = trim((string) $texto);
            return mb_strlen($texto, 'UTF-8') <= $limite
                ? $texto
                : mb_substr($texto, 0, $limite - 3, 'UTF-8') . '...';
        }

        public function ExibirInformacoes() {
            $this->SetY(30);
            $this->SetFont('helvetica', 'B', 16);
            $this->SetTextColor(0);
            $this->Cell(0, 10, 'RELATÓRIO DE PEDIDOS', 0, 1, 'C');

            $this->SetFont('dejavusans', '', 10, '', true);
            $this->Cell(0, 8, 'Relatório gerado em: ' . $this->getDataHoje(), 0, 1, 'L');

            if($this->getTipoRelatorio() == 1) {
                $dataInicial = implode('/', array_reverse(explode('-', explode(' ', $this->getDataInicial())[0])));
                $dataFinal = implode('/', array_reverse(explode('-', explode(' ', $this->getDataFinal())[0])));
                $this->Cell(0, 8, 'Mostrando resultados entre: ' . $dataInicial . ' e ' . $dataFinal, 0, 1, 'L');
            }
            else if($this->getTipoRelatorio() == 2) {
                $this->Cell(0, 8, 'Mostrando resultados para todo o período.', 0, 1, 'L');
            }
            else {
                $this->Cell(0, 8, 'Mostrando somente pedidos ativos atualmente.', 0, 1, 'L');
            }

            $this->Ln(4);
        }

        public function gerarTabela() {  
            $larguraTabela = $this->getPageWidth() - $this->getMargins()['left'] - $this->getMargins()['right'];
            $larguras = [$larguraTabela * 0.40, $larguraTabela * 0.30, $larguraTabela * 0.30];
            $pedidoDetalhes = $this->pedidosData;
            $totalPedidos = count($pedidoDetalhes);
            $totalEmprestimos = 0;

            foreach ($pedidoDetalhes as $pedidoDetalhe) {
                $this->SetFont('dejavusans', 'B', 10, '', true);
                $this->Cell(60, 7, 'Nome', 1, 0, 'L');
                $this->Cell(80, 7, 'Sobrenome', 1, 0, 'L');
                $this->Cell(40, 7, 'Matrícula', 1, 1, 'L');
                $this->SetFont('dejavusans', '', 10, '', true);
                $this->Cell(60, 7, $this->limitarTexto($pedidoDetalhe->usuario->getNome(), 20), 1, 0, 'L');
                $this->Cell(80, 7, $this->limitarTexto($pedidoDetalhe->usuario->getSobrenome(), 28), 1, 0, 'L');
                $this->Cell(40, 7, htmlentities($pedidoDetalhe->usuario->getMatricula()), 1, 1, 'L');
                $this->Ln(3);

                $this->SetFont('dejavusans', 'B', 10, '', true);
                $this->Cell($larguras[0], 7, 'Item', 1, 0, 'L');
                $this->Cell($larguras[1], 7, 'Quantidade', 1, 0, 'L');
                $this->Cell($larguras[2], 7, 'Tipo', 1, 1, 'L');
                $this->SetFont('dejavusans', '', 10, '', true);

                $itensPedido = PedidoDetalhes::itensViaIDDetalhe($pedidoDetalhe->getId());
                foreach ($itensPedido as $itemPedido) {
                    $nomeItem = $itemPedido->estoque->getNome() . ($itemPedido->estoque->isAtivado() ? '' : ' *');
                    $quantidadeItem = htmlentities($itemPedido->getQuantidadeItem());
                    $tipoItem = tipoEstoque((int) $itemPedido->estoque->getTipo());
                    $alturaLinha = max(
                        7,
                        $this->getStringHeight($larguras[0], $nomeItem),
                        $this->getStringHeight($larguras[1], $quantidadeItem),
                        $this->getStringHeight($larguras[2], $tipoItem)
                    ) + 2;

                    if ($this->checkPageBreak($alturaLinha)) {
                        $this->SetFont('dejavusans', 'B', 10, '', true);
                        $this->Cell($larguras[0], 7, 'Item', 1, 0, 'L');
                        $this->Cell($larguras[1], 7, 'Quantidade', 1, 0, 'L');
                        $this->Cell($larguras[2], 7, 'Tipo', 1, 1, 'L');
                        $this->SetFont('dejavusans', '', 10, '', true);
                    }

                    $x = $this->GetX();
                    $y = $this->GetY();
                    $this->MultiCell($larguras[0], $alturaLinha, $nomeItem, 1, 'L', false, 0, $x, $y);
                    $this->MultiCell($larguras[1], $alturaLinha, $quantidadeItem, 1, 'L', false, 0, $x + $larguras[0], $y);
                    $this->MultiCell($larguras[2], $alturaLinha, $tipoItem, 1, 'L', false, 1, $x + $larguras[0] + $larguras[1], $y);
                    $totalEmprestimos++;
                }

                $dataPedido = explode(' ', $pedidoDetalhe->getDataPedido());
                $this->Ln(2);
                $this->Cell(0, 7, 'Data pedido: ' . implode('/', array_reverse(explode('-', $dataPedido[0]))) . ' às ' . ($dataPedido[1] ?? ''), 0, 1, 'L');
                if($this->getTipoRelatorio() == 1 || $this->getTipoRelatorio() == 2) {
                    $dataFinalizado = explode(' ', $pedidoDetalhe->getDataFinalizado());
                    $textoFinalizado = 'Data finalização: ' . implode('/', array_reverse(explode('-', $dataFinalizado[0]))) . ' às ' . ($dataFinalizado[1] ?? '');
                }
                else {
                    $textoFinalizado = 'Data finalização: Não finalizado.';
                }
                $this->Cell(0, 7, $textoFinalizado, 0, 1, 'L');
                $this->Cell(0, 7, 'Código do pedido: ' . htmlentities($pedidoDetalhe->getCodigoPedido()), 0, 1, 'L');
                $this->Ln(5);
            }

            $this->SetFont('dejavusans', 'B', 11, '', true);
            $this->Cell(0, 8, 'Total de pedidos: ' . $totalPedidos, 0, 1, 'L');
            $this->Cell(0, 8, 'Total de empréstimos: ' . $totalEmprestimos, 0, 1, 'L');
            $this->SetFont('dejavusans', '', 10, '', true);
            $this->Cell(0, 7, 'Itens marcados com * estão temporariamente desativados para empréstimos.', 0, 1, 'L');
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
            $dataHoje = date("Y-m-d");

            if (!is_int($tipo) || !in_array($tipo, [1, 2, 3], true)) {
                Painel::alert("erro", "Não foi possível gerar o relatório.");
                return;
            }

            // Define o tipo de relatório
            $this->setTipoRelatorio($tipo);

            // Tratamento preventivo para POST (Evita warnings no PHP 8+)
            $postDataInicial = $_POST['dataInicial'] ?? '';
            $postDataFinal = $_POST['dataFinal'] ?? '';

            // Verifica se as datas iniciais e finais foram fornecidas
            if($this->getTipoRelatorio() == 1 && (empty($postDataInicial) || empty($postDataFinal))) {
                Painel::alert("erro", "As datas não podem ser vazias");
                return;
            }

            // Verifica se a data inicial é maior que a data final
            if($this->getTipoRelatorio() == 1 && ($postDataInicial > $postDataFinal)){
                Painel::alert("erro", "A data inicial não pode ser maior que a data final");
                return;
            }

            // Verifica se a data final é menor que a data inicial
            if($this->getTipoRelatorio() == 1 && ($postDataFinal < $postDataInicial)){
                Painel::alert("erro", "A data final não pode ser menor que a data inicial");
                return;
            }

            // Verifica se a data final é maior que a data atual
            if($this->getTipoRelatorio() == 1 && ($postDataFinal > $dataHoje)) {
                Painel::alert("erro", "A data final não pode ser maior que hoje");
                return;
            }

            // Valida as datas no formato Y-m-d
            $validaDataInicial = DateTime::createFromFormat('Y-m-d', $postDataInicial);
            $validaDataFinal = DateTime::createFromFormat('Y-m-d', $postDataFinal);
            $errosDataInicial = DateTime::getLastErrors();
            $errosDataFinal = DateTime::getLastErrors();
            $dataInicialInvalida = $errosDataInicial !== false && ($errosDataInicial['warning_count'] > 0 || $errosDataInicial['error_count'] > 0);
            $dataFinalInvalida = $errosDataFinal !== false && ($errosDataFinal['warning_count'] > 0 || $errosDataFinal['error_count'] > 0);

            // Se as datas não são válidas, exibe um erro
            if ($this->getTipoRelatorio() == 1 && (!$validaDataInicial || !$validaDataFinal || $dataInicialInvalida || $dataFinalInvalida || $validaDataInicial->format('Y-m-d') !== $postDataInicial || $validaDataFinal->format('Y-m-d') !== $postDataFinal)) {
                Painel::alert("erro", "Data inválida");
                return;
            }

            // Obtém as datas inicial e final fornecidas
            $dataInicial = $postDataInicial . ' 00:00:00';
            $dataFinal = $postDataFinal . ' 23:59:59';

            // CARREGAMENTO ÚNICO: Obtém os detalhes dos pedidos com base no tipo de relatório e guarda na memória
            switch ($this->getTipoRelatorio()) {
                case 1:
                    $this->pedidosData = PedidoDetalhes::retornaPedidosFinalizadosByData($dataInicial, $dataFinal);
                    break;
                case 2:
                    $this->pedidosData = PedidoDetalhes::retornaTodosPedidosFinalizados();
                    break;
                case 3:
                    $this->pedidosData = PedidoDetalhes::retornaPedidosNaoFinalizados();
                    break;
                default:
                    $this->pedidosData = PedidoDetalhes::retornaTodosPedidosFinalizados();
                    break;
            }

            // Se nenhum registro foi encontrado, exibe uma mensagem de erro
            if($this->pedidosData == false) {
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
            $pdf->pedidosData = $this->pedidosData;
            $pdf->setTipoRelatorio($this->getTipoRelatorio());
            $pdf->setDataInicial($this->getDataInicial());
            $pdf->setDataFinal($this->getDataFinal());
            // set document information

            $pdf->SetTitle("Relatório de pedidos - " . $this->getDataHoje());
            $pdf->SetSubject("Relatório de pedidos - " . $this->getDataHoje());
            $pdf->SetKeywords('relatorio, pedidos');

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