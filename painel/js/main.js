document.addEventListener('DOMContentLoaded', function() {
    //OBS: Função de deletar temporiamente desativada.
    // Evento para validar o botão de excluir.
    var deleteButtons = document.querySelectorAll('[actionBtn=delete]');
    
    deleteButtons.forEach(function(button) {
        button.addEventListener('click', function(event) {
            var txt;
            var r = confirm("Deseja excluir o registro?");
            if (r !== true) {
                event.preventDefault(); // Impede a ação padrão do clique se o usuário cancelar
            }
        });
    });

    // Evento para validar o botão de apagar item do carrinho.

    var botaoCarrinho = document.querySelectorAll('[actionBtn=apagarCarrinho]');
    
    botaoCarrinho.forEach(function(button) {
        button.addEventListener('click', function(event) {
            var txt;
            var r = confirm("Deseja excluir este item do seu carrinho?");
            if (r !== true) {
                event.preventDefault(); // Impede a ação padrão do clique se o usuário cancelar
            }
        });
    });

    // Válida matrícula
    var matriculaInput = document.getElementById('matricula');
    if (matriculaInput) {
        matriculaInput.addEventListener('keyup', function() {
            this.value = this.value.match(/[0-9]*/)[0];
        });
    }

    // Filtros de pesquisa 
    var radioInputs = document.querySelectorAll('input[type="radio"]');
    var campoInput = document.getElementById('campo');

    radioInputs.forEach(function(radio) {
        
        // Função para obter a data de hoje no formato "YYYY-MM-DD"
	    function getFormattedDate() {
            var today = new Date();
            var dd = String(today.getDate()).padStart(2, '0');
            var mm = String(today.getMonth() + 1).padStart(2, '0');
            var yyyy = today.getFullYear();
            return yyyy + '-' + mm + '-' + dd;
        }

        radio.addEventListener('change', function() {
            var radioValue = this.value;

            if (radioValue === 'nome') {
                campoInput.value = '';
                campoInput.placeholder = 'Ex: Fulano';
                campoInput.type = 'text';
                campoInput.removeAttribute('maxlength');
                campoInput.removeAttribute('pattern');
                campoInput.removeEventListener('input', inputEventListener);
            } 

            else if (radioValue === 'email') {
                campoInput.value = '';
                campoInput.placeholder = 'Ex: fulano@alu.ufc.br';
                campoInput.type = 'email';
                campoInput.removeAttribute('maxlength');
                campoInput.removeAttribute('pattern');
                campoInput.removeEventListener('input', inputEventListener);
            }
            
            else if (radioValue === 'matricula') {
                campoInput.value = '';
                campoInput.placeholder = 'Ex: 000000';
                campoInput.type = 'text';
                campoInput.setAttribute('maxlength', '6');
                campoInput.setAttribute('pattern', '([0-9]{6})');
                
                campoInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, ''); // Remove caracteres não numéricos
                    this.value = this.value.slice(0, 6); // Limita o valor a 6 dígitos
                });
            }

            else if (radioValue === 'data') {
                campoInput.value = '';
                campoInput.placeholder = 'Ex: DD/MM/AAAA';
                campoInput.type = 'date';
                campoInput.value = getFormattedDate();
                campoInput.removeAttribute('maxlength');
                campoInput.setAttribute('pattern', '([0-3][0-9]/[0-1][0-9]/[0-9]{4})');
            }
        });
    });

    function inputEventListener() {
       this.value = this.value.replace(/[^0-9]/g, ''); // Remove caracteres não numéricos
       this.value = this.value.slice(0, 6); // Limita o valor a 6 dígitos
    }
});
