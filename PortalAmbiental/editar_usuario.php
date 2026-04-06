<?php
require_once 'conexao.php';
require_once 'verifica_login.php';

$pdo = conectar();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    mensagem('erro', 'Usuário não encontrado.');
    redirecionar('usuarios.php');
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    mensagem('erro', 'Usuário não encontrado.');
    redirecionar('usuarios.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = sanitizar($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $bio = sanitizar($_POST['bio'] ?? '');
    $senha_atual = $_POST['senha_atual'] ?? '';
    $nova_senha = $_POST['nova_senha'] ?? '';
    
    $erros = [];
    
    if (strlen($nome) < 3) $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
    if (!$email) $erros[] = 'E-mail inválido.';
    
    // Upload de foto
    $foto = $usuario['foto'];
    if (!empty($_FILES['foto']['name'])) {
        $novaFoto = uploadImagem($_FILES['foto'], 'imagens/');
        if ($novaFoto) {
            $foto = $novaFoto;
            if ($usuario['foto'] && file_exists($usuario['foto'])) {
                unlink($usuario['foto']);
            }
        } else {
            $erros[] = 'Erro ao fazer upload da foto.';
        }
    }
    
    if ($nova_senha && strlen($nova_senha) < 6) {
        $erros[] = 'Nova senha deve ter pelo menos 6 caracteres.';
    }
    
    if ($email !== $usuario['email']) {
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        if ($stmt->fetch()) $erros[] = 'Este e-mail já está em uso.';
    }
    
    if (empty($erros)) {
        if ($nova_senha) {
            $hash = hashSenha($nova_senha);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, bio = ?, foto = ?, senha = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $bio, $foto, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome = ?, email = ?, bio = ?, foto = ? WHERE id = ?");
            $stmt->execute([$nome, $email, $bio, $foto, $id]);
        }
        
        mensagem('sucesso', 'Usuário atualizado com sucesso! ✓');
        redirecionar('usuarios.php');
    } else {
        foreach($erros as $erro) mensagem('erro', $erro);
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Portal Ambiental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="oi.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <i class="bi bi-tree-fill"></i> Portal Ambiental
            </a>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="bi bi-house"></i> Início</a></li>
                    <li><a href="usuarios.php"><i class="bi bi-people"></i> Usuários</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    <li><button class="theme-toggle" id="themeToggle">🌙</button></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container py-4">
        <?php exibirFlash(); ?>
        
        <div class="auth-card" style="max-width: 600px;">
            <h3 class="mb-4 text-center"><i class="bi bi-pencil-square"></i> Editar Usuário</h3>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label><i class="bi bi-person-fill"></i> Nome Completo *</label>
                    <input type="text" name="nome" required value="<?= sanitizar($usuario['nome']) ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="bi bi-envelope-fill"></i> E-mail *</label>
                    <input type="email" name="email" required value="<?= sanitizar($usuario['email']) ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="bi bi-card-text"></i> Biografia</label>
                    <textarea name="bio"><?= sanitizar($usuario['bio'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label><i class="bi bi-image"></i> Foto de Perfil</label>
                    <?php if($usuario['foto'] && file_exists($usuario['foto'])): ?>
                        <img src="<?= sanitizar($usuario['foto']) ?>" 
                             alt="Atual" 
                             style="max-height: 100px; border-radius: 8px;" 
                             class="mb-2">
                    <?php endif; ?>
                    <input type="file" name="foto" accept="image/*" class="form-control">
                </div>
                
                <hr>
                
                <div class="form-group">
                    <label><i class="bi bi-lock-fill"></i> Nova Senha (opcional)</label>
                    <input type="password" name="nova_senha" placeholder="Deixe em branco para manter">
                </div>
                
                <div class="d-flex gap-2">
                    <button type="submit" class="btn">
                        <i class="bi bi-check-circle"></i> Salvar
                    </button>
                    <a href="usuarios.php" class="btn btn-outline">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>