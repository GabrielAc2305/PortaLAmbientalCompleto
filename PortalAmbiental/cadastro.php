<?php
require_once 'conexao.php';
require_once 'funcoes.php';

if (estaLogado()) redirecionar('dashboard.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmar'] ?? '';
    
    $erros = [];
    
    if (strlen($nome) < 3) $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
    if (strlen($senha) < 6) $erros[] = 'Senha deve ter pelo menos 6 caracteres.';
    if ($senha !== $confirmar) $erros[] = 'As senhas não conferem.';
    
    if (empty($erros)) {
        $pdo = conectar();
        
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $erros[] = 'Este e-mail já está cadastrado.';
        } else {
            $hash = hashSenha($senha);
            $stmt = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            if ($stmt->execute([$nome, $email, $hash])) {
                mensagem('sucesso', 'Cadastro realizado com sucesso! Faça login para continuar. 🎉');
                redirecionar('login.php');
            } else {
                $erros[] = 'Erro ao cadastrar. Tente novamente.';
            }
        }
    }
    
    foreach($erros as $erro) mensagem('erro', $erro);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Conta - Impacto Verde</title>
    <link rel="icon" type="image/svg+xml" href="logo-icon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="oi.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <img src="logo.svg" alt="Impacto Verde" height="40">
            </a>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="bi bi-house"></i> Início</a></li>
                    <li><a href="sobre.php"><i class="bi bi-info-circle"></i> Quem Somos</a></li>
                    <li><a href="contato.php"><i class="bi bi-envelope"></i> Contato</a></li>
                    <li><a href="login.php" class="btn-destaque"><i class="bi bi-box-arrow-in-right"></i> Entrar</a></li>
                    <li><button class="theme-toggle" id="themeToggle">🌙</button></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="bi bi-person-plus-fill"></i>
                <h3>Criar Conta</h3>
                <p>Junte-se ao Impacto Verde!</p>
            </div>
            
            <?php exibirFlash(); ?>
            
            <form method="POST">
                <div class="form-group">
                    <label><i class="bi bi-person-fill"></i> Nome Completo</label>
                    <input type="text" name="nome" required value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="bi bi-envelope-fill"></i> E-mail</label>
                    <input type="email" name="email" required value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                </div>
                
                <div class="form-group">
                    <label><i class="bi bi-lock-fill"></i> Senha</label>
                    <input type="password" name="senha" required>
                    <small class="text-muted">Mínimo 6 caracteres</small>
                </div>
                
                <div class="form-group">
                    <label><i class="bi bi-lock-fill"></i> Confirmar Senha</label>
                    <input type="password" name="confirmar" required>
                </div>
                
                <button type="submit" class="btn w-100 btn-lg">
                    <i class="bi bi-person-plus"></i> Cadastrar
                </button>
            </form>
            
            <div class="form-footer">
                <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
                <p><a href="index.php"><i class="bi bi-arrow-left"></i> Voltar para o início</a></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>