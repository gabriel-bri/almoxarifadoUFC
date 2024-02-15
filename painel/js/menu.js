// Variável de controle para verificar se o menu está aberto.
let open = true;

document.addEventListener('DOMContentLoaded', function () {
  let menu = document.querySelector('.menu');
  let menuBtn = document.querySelector('.menu-btn');
  let header = document.querySelector('header');
  let content = document.querySelector('.content');

  // Pega o tamanho da tela atual. 
  let windowSize = window.innerWidth;

  // Armazena o novo tamanho do menu.
  let targetSizeMenu = windowSize <= 400 ? 200 : 250;

  //Verifica se o tamanho da tela é menor que 768
  if (windowSize <= 768) {
    // Caso verdade altera o tamanho e o padding para 0 e
    // define o valor de open para false.
    console.log("menu sumiu");
    menu.style.width = 0;
    menu.style.padding = 0;
    open = false;
  }

  content.style.transitionTimingFunction = "linear";
  content.style.transition = 'left 0.5s'; // Define a duração da animação

  header.style.transitionTimingFunction = "linear";
  header.style.transition = 'all 0.5s'; // Define a duração da animação

  menu.style.transitionTimingFunction = "linear";
  menu.style.transition = 'all 0.5s'; // Define a duração da animação  
  
  menuBtn.addEventListener('click', function () {
    // O menu está aberto, precisamos fechar e adaptar nosso conteúdo geral do painel
    console.log("Menu fechou");
    if (open) {      
      content.style.width = '100%'
      content.style.left = '0'

      header.style.width = '100%';
      header.style.left = '0';

      menu.style.width = '0';
      menu.style.padding = '0';

      open = false;
    }

    else {
      //O menu está fechado
      console.log("Menu abriu");
    
      menu.style.display = 'block';
      menu.style.width = `${targetSizeMenu}px`;
      menu.style.padding = '10px 0';

      header.style.left = `${targetSizeMenu}px`;

      content.style.left = `${targetSizeMenu}px`;
      
      if(windowSize > 768) {
        header.style.width = 'calc(100% - 250px)';
        header.style.left = `${targetSizeMenu}px`;

        content.style.width = 'calc(100% - 250px)';
        content.style.left = `${targetSizeMenu}px`;
      }      

      open = true;    
    }
  });

  window.addEventListener('resize', function() {
    let windowSize = window.innerWidth;
    let targetSizeMenu = (windowSize <= 400) ? 200 : 250;

    console.log(windowSize, window.innerWidth, targetSizeMenu);

    if(windowSize <= 768) {
      menu.style.width = '0';
      menu.style.padding = '0px';

      content.style.width = '100%';
      content.style.left = '0';

      header.style.width = '100%';
      header.style.left = '0';

      open = false;
    } 

    else {
      menu.style.width = `${targetSizeMenu}px`;
      menu.style.padding = '10px 0';
      header.style.width = 'calc(100% - 250px)';
      header.style.left = `${targetSizeMenu}px`;

      content.style.width = 'calc(100% - 250px)';
      content.style.left = `${targetSizeMenu}px`;
      
      open = true;
    }
  });
});

