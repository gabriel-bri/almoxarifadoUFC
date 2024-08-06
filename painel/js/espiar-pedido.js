// Aguarda o carregamento completo do DOM antes de executar o código
document.addEventListener('DOMContentLoaded', function() {
    // Seleciona todos os elementos com a classe 'espiar-pedido' e 
    // adiciona um evento de clique a cada um
    document.querySelectorAll('.espiar-pedido').forEach(function(btn) {
        btn.addEventListener('click', function(event) {
            // Impede o comportamento padrão do clique em um link
            event.preventDefault();

            // Obtém os dados do pedido e do usuário em formato JSON dos atributos
            // de dados do botão clicado
            var itensPedido = JSON.parse(this.getAttribute('data-itensPedido'));
            var usuarios = JSON.parse(this.getAttribute('data-usuario'));

            // Declara variáveis para armazenar as mensagens HTML a serem exibidas no modal
            var mensagem1, mensagem2 = '';

            // Monta a primeira parte da mensagem HTML com os dados do usuário
            mensagem1 = '<div><p>Pedido feito por: <strong>' + usuarios.nome + ' ' + 
                usuarios.sobrenome + '</strong>, matrícula <strong>' +
                usuarios.matricula + '</strong> em <strong>' +
                usuarios.data + '<strong> às </strong>' +
                usuarios.hora + '</strong></p><br> <div>';

            // Itera sobre os itens do pedido e monta a segunda parte da mensagem HTML
            itensPedido.forEach(function(itemPedido) {
                mensagem2 += '<p>Item: ' + itemPedido.nome + '</p><br>' +
                            '<p>Quantidade: ' + itemPedido.quantidade + '</p><br>' +
                            '<p>Tipo: ' + itemPedido.tipo + '</p><br><br>'
            });

            // Exibe um modal utilizando a biblioteca SweetAlert com os
            // detalhes do pedido
            Swal.fire({
                title: 'Detalhes do Pedido',
                html: mensagem1 + '<div class="sweet-alert">' + mensagem2 + '</div>',
                icon: 'info',
                confirmButtonText: 'OK'
            });
        });
    });
});
