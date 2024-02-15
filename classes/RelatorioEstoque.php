<?php  
    class RelatorioEstoque extends TCPDF {
        private static $tipoRelatorio;
        
        public function setTipoRelatorio($tipoRelatorio){
            self::$tipoRelatorio = $tipoRelatorio;
        }

        public function getTipoRelatorio(){
            return self::$tipoRelatorio;
        }
        
        public function getDataHoje(){
            return date("d/m/Y");
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
            $this->SetY(30); // Mude para o valor desejado para posicionar mais abaixo
            $this->SetFont('helvetica', 'B', 20);
            $this->Cell(0, 10, 'RELATÓRIO DO ESTOQUE', 0, 1, 'C');

            // Configurar a fonte e tamanho para as informações
            $this->SetFont('helvetica', '', 14);
            $this->SetTextColor(0);

            // Adicionar espaçamento antes das informações
            $this->Ln(10);

            // Exibir as informações
            $this->Cell(0, 0, 'Relatório gerado em: ' . $this->getDataHoje(), 0, 1, 'L');
        }

        public function gerarTabela() {            
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
            
            $this->SetFont('helvetica', '', 12);

            $itensEstoque = ($this->getTipoRelatorio() == 3) ? Estoque::selectAll() : Estoque::retornaPeloTipo($this->getTipoRelatorio());

            $contaItens = 0;
        
            foreach ($itensEstoque as $itemEstoque) {
                $this->SetFillColor($alternarCor ? 230 : 240, $alternarCor ? 230 : 240, $alternarCor ? 230 : 240);

                $verificaEmprestimo = PedidoDetalhes::verificaEmprestimoProduto($itemEstoque->getId());
                
                // var_dump($verificaEmprestimo);
                if($itemEstoque->isAtivado() == false) {
                    $itemEstoque->setNome($itemEstoque->getNome() . ' *');
                }

                if($verificaEmprestimo != false && $verificaEmprestimo->getFinalizado() == 0) {
                    $itemEstoque->setNome($itemEstoque->getNome() . ' **');
                }

                $this->Cell(60, 10, htmlentities($itemEstoque->getNome()), 1, 0, 'C', 1);
                $this->Cell(60, 10, htmlentities($itemEstoque->getQuantidade()), 1, 0, 'C', 1);
                $this->Cell(60, 10, htmlentities(tipoEstoque($itemEstoque->getTipo())), 1, 0, 'C', 1);
                $this->Ln();

                // Alternar a cor para a próxima linha
                $alternarCor = !$alternarCor;
                $contaItens++;
            }

            $this->Ln();

            $mensagemRelatorio = "<p>Itens marcados com um <b>*(asterisco)</b> estão temporiamente desativados para empréstimos.</p>";
            $mensagemRelatorio .= "<p>Itens marcados com dois <b>**(asterisco)</b> estão com empréstimos ativos ou com revisão pendente.</p>";
            $mensagemRelatorio .= "<p>Total de itens: <b>{$contaItens}</b></p>";
                        
            $this->writeHTML($mensagemRelatorio, true, false, true, false, '');

        }

        /**
         * Valida e processa a geração do relatório com base no tipo especificado.
         *
         * @param int $tipo Tipo do relatório a ser gerado (1, 2 ou 3).
         * @return void
         */
        
        //  1 - Todos os equipamentos.
        //  2 - Todos os componentes.
        //  3 - Todos os itens do estoque.
        public function validarRelatorio($tipo) {
            // Verifica se o tipo fornecido é um número inteiro e está entre 1, 2 ou 3
            if (!is_int($tipo) || !in_array($tipo, [1, 2, 3])) {
                Painel::alert("erro", "Não foi possível gerar o relatório.");
                return;
            }

            // Define o tipo de relatório
            $this->setTipoRelatorio($tipo);
            
            // Obtém os itens do estoque com base no tipo de relatório selecionado
            $itensEstoque = ($this->getTipoRelatorio() == 3) ? Estoque::selectAll() : Estoque::retornaPeloTipo($this->getTipoRelatorio());

            // Se não houver dados disponíveis para o tipo de relatório, exibe uma mensagem de erro
            if ($itensEstoque == false) {
                Painel::alert("erro", "Não há dados cadastrados.");
                return;
            }
            
            // Gera o PDF com os dados do estoque
            $this->gerarPDF();
        }

        private function gerarPDF() {
            $pdf = new RelatorioEstoque(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
            // set document information

            $pdf->SetTitle("Relatório do estoque - " . $this->getDataHoje());
            $pdf->SetSubject("Relatório do estoque - " . $this->getDataHoje());
            $pdf->SetKeywords('relatorio', 'estoque');

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
            $pdf->Output('relatorio_estoque.pdf', 'I');
        }
    }
?>