<?php  
verificaPermissaoPagina(2);

if(!isset($_GET['id']) || !(int)$_GET['id'] || $_GET['id'] <= 0) {
    Painel::alert("erro", "Você precisa passar um ID");
    return;
}

$id = (int)$_GET['id'];
$usuarios = Usuario::select('id = ?', array($id));
$usuarioAdmin = Usuario::select('id = ?', array($_SESSION['id']));

if (!$usuarios)  {
    Painel::alert("erro", "ID não encontrado");
    return;
}

if($usuarios->getAcesso() == 2 && $_SESSION['acesso'] == 2) {
    Painel::alert("erro", "Você não pode editar os dados de outro bolsista, consulte o administrador.");
    return;
}

if($usuarios->getAcesso() == 3 && $_SESSION['acesso'] == 2) {
    Painel::alert("erro", "Você não tem permissão para alterar os dados do administrador.");
    return;
}

// 1. FORMULÁRIO DE ATUALIZAÇÃO DE DADOS
if(isset($_POST['acao'])) {
    Usuario::validarEntradasAtualizarUsuarios($usuarios, $_POST);
    Painel::alert("sucesso", "Usuário atualizado com sucesso!");
    echo '<script>setTimeout(() => window.location.href = "'.INCLUDE_PATH_PAINEL.'editar-usuarios?id='.$id.'", 1500);</script>';
    return;
}

// 2. FORMULÁRIO DE BLOQUEIO DE PEDIDOS
if($_SESSION['acesso'] == 3 && isset($_POST['confirmar_bloqueio'])) {
    $motivo = strip_tags(trim($_POST['motivo'] ?? ''));
    
    if($motivo == "") {
        Painel::alert("erro", "Você precisa inserir um motivo para o bloqueio.");
    } else {
        Usuario::bloquearPedidos($usuarios, 1);
        HistoricoBloqueios::criarBloqueio($usuarios->getId(), $usuarioAdmin->getId(), $motivo);
        Painel::alert("sucesso", "Pedidos bloqueados com sucesso!");
        echo '<script>setTimeout(() => window.location.href = "'.INCLUDE_PATH_PAINEL.'editar-usuarios?id='.$id.'", 1500);</script>';
        return;
    }
}

// 3. FORMULÁRIO DE DESBLOQUEIO DE PEDIDOS
if($_SESSION['acesso'] == 3 && isset($_POST['confirmar_desbloqueio'])) {
    $motivo_desbloqueio = trim($_POST['motivo_desbloqueio'] ?? '');
    $id_bloqueio = HistoricoBloqueios::getIdBloqueioPendente($id);

    if($motivo_desbloqueio == "") {
        Painel::alert("erro", "Você precisa inserir um motivo para o desbloqueio.");
    } else {
        Usuario::bloquearPedidos($usuarios, 0);
        HistoricoBloqueios::finalizarBloqueio($id_bloqueio, $usuarioAdmin->getId(), $motivo_desbloqueio);
        Painel::alert("sucesso", "Pedidos desbloqueados com sucesso!");
        echo '<script>setTimeout(() => window.location.href = "'.INCLUDE_PATH_PAINEL.'editar-usuarios?id='.$id.'", 1500);</script>';
        return;
    }
}

// PÁGINA DE MOTIVO PARA BLOQUEIO
if($_SESSION['acesso'] == 3 && isset($_GET['bloquear-pedidos'])) {
?>
<div class="box-content">
    <h2><i class="fa fa-times"></i> Inserir motivo do bloqueio</h2>
    <form method="post">
        <div class="form-group">
            <label for="motivo">Motivo:</label>
            <textarea name="motivo" id="motivo" required placeholder="Descreva o motivo do bloqueio"><?php echo $_POST['motivo'] ?? ''; ?></textarea>
        </div>
        <div class="form-group">
            <input type="submit" name="confirmar_bloqueio" value="Confirmar bloqueio">
            <a href="<?php echo INCLUDE_PATH_PAINEL . 'editar-usuarios?id=' . $id; ?>" class="btn-voltar">Voltar</a>
        </div>
    </form>
</div>
<?php
    return;
}

// PÁGINA DE MOTIVO PARA DESBLOQUEIO
if($_SESSION['acesso'] == 3 && isset($_GET['liberar-pedidos'])) {
?>
<div class="box-content">
    <h2><i class="fa fa-times"></i> Inserir motivo do desbloqueio</h2>
    <form method="post">
        <div class="form-group">
            <label for="motivo_desbloqueio">Motivo:</label>
            <textarea name="motivo_desbloqueio" id="motivo_desbloqueio" required placeholder="Descreva o motivo do desbloqueio"><?php echo $_POST['motivo_desbloqueio'] ?? ''; ?></textarea>
        </div>
        <div class="form-group">
            <input type="submit" name="confirmar_desbloqueio" value="Confirmar desbloqueio">
            <a href="<?php echo INCLUDE_PATH_PAINEL . 'editar-usuarios?id=' . $id; ?>" class="btn-voltar">Voltar</a>
        </div>
    </form>
</div>
<?php
    return;
}

// OPERAÇÕES SIMPLES (REDIRECT IMEDIATO)

if($_SESSION['acesso'] == 3) {
    if(isset($_GET['bloquear-login'])) {
        Usuario::bloquearLogin($usuarios, 0);
        Painel::alert("sucesso", "Login bloqueado com sucesso!");
        echo '<script>setTimeout(() => window.location.href = "'.INCLUDE_PATH_PAINEL.'editar-usuarios?id='.$id.'", 1500);</script>';
        return;
    }

    if(isset($_GET['liberar-login'])) {
        Usuario::bloquearLogin($usuarios, 1);
        Painel::alert("sucesso", "Login liberado com sucesso!");
        echo '<script>setTimeout(() => window.location.href = "'.INCLUDE_PATH_PAINEL.'editar-usuarios?id='.$id.'", 1500);</script>';
        return;
    }

    if(isset($_GET['confirmar-conta'])) {
        Usuario::reConfirmarConta($usuarios);
        Painel::alert("sucesso", "Link de confirmação reenviado com sucesso!");
        echo '<script>setTimeout(() => window.location.href = "'.INCLUDE_PATH_PAINEL.'editar-usuarios?id='.$id.'", 1500);</script>';
        return;
    }
}

// PÁGINA PRINCIPAL - EDIÇÃO DE USUÁRIO
?>

<div class="box-content">
    <h2><i class="fa fa-pencil-alt"></i> Editar Usuário</h2>

    <form method="post">
        <div class="form-group">
            <p>Status da conta: <b><?php echo statusConta($usuarios->isAtivada());?></b></p>

            <?php if($usuarios->getAcesso() == 2 || $usuarios->getAcesso() == 1) { ?>
            <p>Status de pedidos: <b><?php echo emprestimoBloqueado($usuarios->isBloqueado());?></b></p>
            <?php } ?>
        </div>
        
        <div class="form-group">
            <label for="nome">Nome:</label>
            <input id="nome" type="text" name="nome" required value="<?php echo htmlentities($usuarios->getNome()); ?>" placeholder="Nome">
        </div>

        <div class="form-group">
            <label for="sobrenome">Sobrenome:</label>
            <input type="text" name="sobrenome" id="sobrenome" value="<?php echo htmlentities($usuarios->getSobrenome()) ?>" placeholder="Sobrenome" required>
        </div>

        <div class="form-group">
            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlentities($usuarios->getEmail()) ?>" placeholder="E-mail" required>
        </div>

        <?php if($_SESSION['acesso'] == 3) { ?>
        <div class="form-group">
            <label for="acesso">Cargo:</label>
            <select name="acesso" id="acesso">
                <?php 
                foreach (Usuario::$acessos as $key => $value) {
                    if ($key == $usuarios->getAcesso()) {
                        echo "<option value='".htmlentities($usuarios->getAcesso())."' selected>".pegaCargo(htmlentities($usuarios->getAcesso()))."</option>";
                    } else {
                        echo "<option value='$key'>$value</option>";
                    }
                }
                ?>
            </select>
        </div>
        <?php } ?>

        <?php if($usuarios->getAcesso() == 1 || $usuarios->getAcesso() == 2) { ?>
        <div class="form-group">
            <label for="curso">Curso:</label>
            <select name="curso" id="curso">
                <?php 
                foreach (Usuario::$cursos as $key => $value) {
                    if ($key == $usuarios->getCurso()) {
                        echo "<option value='".htmlentities($usuarios->getCurso())."' selected>".qualCurso(htmlentities($usuarios->getCurso()))."</option>";
                    } else {
                        echo "<option value='$key'>$value</option>";
                    }
                }
                ?>
            </select>
        </div>
        <?php } ?>
        
        <?php if($usuarios->getAcesso() == 1 || $usuarios->getAcesso() == 2) { ?>
        <div class="form-group">
            <label for="matricula">Matrícula:</label>
            <input id="matricula" type="text" name="matricula" maxlength="6" pattern="[0-9]{6}" value="<?php echo htmlentities($usuarios->getMatricula()) ?>" placeholder="Matrícula">
        </div>
        <?php } ?>

        <div class="form-group">
            <input type="submit" value="Atualizar" name="acao">
            <input type="hidden" name="id" value="<?php echo htmlentities($usuarios->getId()); ?>">
        </div>
    </form>

    <div class="box-operacoes">
        <?php if($_SESSION['acesso'] == 3) { ?>
            <?php if($usuarios->isBloqueado() == 0 && ($usuarios->getAcesso() == 2 || $usuarios->getAcesso() == 1)) { ?>
            <a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?bloquear-pedidos&id=<?php echo htmlentities($usuarios->getId()); ?>" class="operacao">Bloquear pedidos <i class="fa fa-times"></i></a>
            <?php } ?>

            <?php if($usuarios->isBloqueado() == 1 && ($usuarios->getAcesso() == 2 || $usuarios->getAcesso() == 1)) { ?>
            <a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?liberar-pedidos&id=<?php echo htmlentities($usuarios->getId()); ?>" class="operacao">Liberar pedidos <i class="fa fa-thumbs-up"></i></a>
            <?php } ?>
            
            <?php if($usuarios->isAtivada() == 0) { ?>
            <a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?confirmar-conta&id=<?php echo htmlentities($usuarios->getId()); ?>" class="operacao">Reenviar link de confirmação <i class="fa fa-mail-bulk"></i></a>
            <?php } ?>

            <?php if($usuarios->isAtivada() == 1) { ?>
            <a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?bloquear-login&id=<?php echo htmlentities($usuarios->getId()); ?>" class="operacao">Bloquear login <i class="fa fa-ban"></i></a>
            <?php } ?>

            <?php if($usuarios->isAtivada() == 0) { ?>
            <a href="<?php echo INCLUDE_PATH_PAINEL ?>editar-usuarios?liberar-login&id=<?php echo htmlentities($usuarios->getId()); ?>" class="operacao">Liberar login <i class="fa fa-door-open"></i></a>
            <?php } ?>
        <?php } ?>
    </div>
</div>