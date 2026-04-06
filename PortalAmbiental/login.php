<?php
require_once 'conexao.php';
require_once 'funcoes.php';

if (estaLogado()) {
    if (ehAdmin()) {
        redirecionar('admin.php');
    } else {
        redirecionar('perfil.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = $_POST['senha'] ?? '';
    
    if ($email && $senha) {
        $pdo = conectar();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();
        
        if ($usuario) {
            if (verificarSenha($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_admin'] = $usuario['admin'];
                
                if ($usuario['admin'] == 1) {
                    mensagem('sucesso', 'Bem-vindo, Administrador ' . htmlspecialchars($usuario['nome']) . '! 🌿');
                    redirecionar('admin.php');
                } else {
                    mensagem('sucesso', 'Bem-vindo de volta, ' . htmlspecialchars($usuario['nome']) . '! 🌿');
                    redirecionar('perfil.php');
                }
            } else {
                mensagem('erro', 'Senha incorreta.');
            }
        } else {
            mensagem('erro', 'E-mail não encontrado.');
        }
    } else {
        mensagem('erro', 'Preencha e-mail e senha.');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar - Impacto Verde</title>
    <link rel="icon" type="image/svg+xml" href="logo-icon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --verde-escuro: #1b5e20;
            --verde-medio: #2e7d32;
            --verde-claro: #4caf50;
            --verde-suave: #e8f5e9;
            --verde-neon: #66bb6a;
            --fundo: #f8fff8;
            --fundo-secundario: #ffffff;
            --texto: #333333;
            --texto-secundario: #666666;
            --borda: #c8e6c9;
            --sombra: 0 2px 15px rgba(27, 94, 32, 0.15);
            --sombra-hover: 0 8px 25px rgba(27, 94, 32, 0.25);
            --gradiente-header: linear-gradient(135deg, #1b5e20 0%, #2e7d32 50%, #4caf50 100%);
            --card-bg: #ffffff;
            --input-bg: #ffffff;
            --hover-bg: rgba(255,255,255,0.1);
        }
        body.dark-mode {
            --verde-escuro: #1b5e20;
            --verde-medio: #66bb6a;
            --verde-claro: #81c784;
            --verde-neon: #a5d6a7;
            --verde-suave: #1b3a1b;
            --fundo: #0f1f0f;
            --fundo-secundario: #1a2f1a;
            --texto: #e0e0e0;
            --texto-secundario: #b0b0b0;
            --borda: #2d4a2d;
            --sombra: 0 2px 15px rgba(0,0,0,0.3);
            --sombra-hover: 0 8px 25px rgba(0,0,0,0.5);
            --gradiente-header: linear-gradient(135deg, #0f1f0f 0%, #1a3a1a 50%, #2d4a2d 100%);
            --card-bg: #1a2f1a;
            --input-bg: #2d4a2d;
            --hover-bg: rgba(255,255,255,0.05);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; transition: background-color 0.3s, color 0.2s; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: var(--fundo); color: var(--texto); line-height: 1.6; min-height: 100vh; }
        header { background: var(--gradiente-header); color: white; padding: 0.75rem 0; box-shadow: var(--sombra); position: sticky; top: 0; z-index: 1000; }
        header .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .logo { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: white; font-size: 1.5rem; font-weight: 700; transition: transform 0.3s; }
        .logo:hover { transform: scale(1.05); }
        .logo img { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
        nav ul { display: flex; list-style: none; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
        nav a { color: white; text-decoration: none; font-weight: 500; padding: 0.6rem 1rem; border-radius: 25px; transition: all 0.3s; display: flex; align-items: center; gap: 0.3rem; }
        nav a:hover, nav a.ativo { background: var(--hover-bg); transform: translateY(-2px); }
        nav .btn-destaque { background: linear-gradient(135deg, var(--verde-claro), var(--verde-neon)); color: #000; font-weight: 600; box-shadow: 0 2px 10px rgba(76, 175, 80, 0.3); }
        nav .btn-destaque:hover { background: linear-gradient(135deg, var(--verde-neon), var(--verde-claro)); color: #000; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.5); }
        .theme-toggle { background: var(--hover-bg); border: 2px solid rgba(255,255,255,0.3); color: white; padding: 0.5rem 0.8rem; border-radius: 25px; cursor: pointer; font-size: 1.2rem; transition: all 0.3s; }
        .theme-toggle:hover { background: rgba(255,255,255,0.2); transform: rotate(15deg); }
        .auth-container { min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; background: linear-gradient(135deg, var(--verde-suave) 0%, var(--fundo) 100%); }
        .auth-card { background: var(--card-bg); border-radius: 20px; padding: 2.5rem; box-shadow: var(--sombra-hover); width: 100%; max-width: 450px; border: 1px solid var(--borda); animation: slideIn 0.5s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-header i { font-size: 3rem; color: var(--verde-medio); margin-bottom: 1rem; display: block; }
        body.dark-mode .auth-header i { color: var(--verde-neon); }
        .auth-header h3 { color: var(--texto); font-size: 1.8rem; margin-bottom: 0.5rem; }
        .auth-header p { color: var(--texto-secundario); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--verde-medio); }
        body.dark-mode .form-group label { color: var(--verde-neon); }
        .form-group input { width: 100%; padding: 0.9rem 1rem; border: 2px solid var(--borda); border-radius: 12px; font-size: 1rem; background: var(--input-bg); color: var(--texto); transition: all 0.3s; }
        .form-group input:focus { outline: none; border-color: var(--verde-medio); box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1); }
        body.dark-mode .form-group input:focus { border-color: var(--verde-neon); box-shadow: 0 0 0 4px rgba(102, 187, 106, 0.1); }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.7rem 1.8rem; background: linear-gradient(135deg, var(--verde-medio), var(--verde-claro)); color: white; text-decoration: none; border-radius: 30px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 10px rgba(46, 125, 50, 0.3); }
        .btn:hover { background: linear-gradient(135deg, var(--verde-escuro), var(--verde-medio)); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(46, 125, 50, 0.4); }
        .btn-lg { padding: 1rem 2.5rem; font-size: 1.1rem; }
        .form-footer { text-align: center; margin-top: 1.5rem; padding-top: 1.5rem; border-top: 1px solid var(--borda); }
        .form-footer a { color: var(--verde-medio); text-decoration: none; font-weight: 600; }
        body.dark-mode .form-footer a { color: var(--verde-neon); }
        .form-footer a:hover { text-decoration: underline; }
        .alert { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; animation: slideIn 0.3s ease; }
        .alert-success { background: rgba(76, 175, 80, 0.15); border: 2px solid var(--verde-medio); color: var(--verde-escuro); }
        body.dark-mode .alert-success { color: var(--verde-neon); }
        .alert-danger { background: rgba(244, 67, 54, 0.15); border: 2px solid #f44336; color: #c62828; }
        body.dark-mode .alert-danger { color: #ef5350; }
        footer { background: var(--gradiente-header); color: white; padding: 3rem 0 1.5rem; margin-top: 4rem; }
        .footer-bottom { text-align: center; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.7); }
        @media (max-width: 768px) {
            header .container { flex-direction: column; text-align: center; }
            nav ul { justify-content: center; gap: 0.3rem; }
            nav a { padding: 0.5rem 0.8rem; font-size: 0.9rem; }
            .auth-card { padding: 1.5rem; }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <img src="logo.svg" alt="Impacto Verde" height="40">
                
            </a>
            <nav>
                <ul>
                    <li><a href="index.php">Início</a></li>
                    <li><a href="sobre.php">Quem Somos</a></li>
                    <li><a href="contato.php">Contato</a></li>
                    <li><button class="theme-toggle" id="themeToggle">🌙</button></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <img src="logo-icon.svg" alt="Impacto Verde" height="80" style="margin-bottom: 1rem;">
                <h3>Bem-vindo!</h3>
                <p>Faça login para acessar o sistema</p>
            </div>
            
            <?php exibirFlash(); ?>
            
            <form method="POST">
                <div class="form-group">
                    <label><i class="bi bi-envelope-fill"></i> E-mail</label>
                    <input type="email" name="email" required placeholder="seu@email.com">
                </div>
                
                <div class="form-group">
                    <label><i class="bi bi-lock-fill"></i> Senha</label>
                    <input type="password" name="senha" required placeholder="••••••••">
                </div>
                
                <button type="submit" class="btn btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Entrar
                </button>
            </form>
            
            <div class="form-footer">
                <p><a href="index.php"><i class="bi bi-arrow-left"></i> Voltar ao início</a></p>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Impacto Verde. Todos os direitos reservados. 🌱</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>