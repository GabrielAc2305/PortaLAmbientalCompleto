<?php
require_once 'conexao.php';
require_once 'verifica_login.php';
requireAdmin();

$pdo = conectar();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    mensagem('erro', 'Usuário não encontrado.');
    redirecionar('admin_usuarios.php');
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    mensagem('erro', 'Usuário não encontrado.');
    redirecionar('admin_usuarios.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $bio = trim($_POST['bio'] ?? '');
    $admin = isset($_POST['admin']) ? 1 : 0;
    $nova_senha = $_POST['nova_senha'] ?? '';
    
    $erros = [];
    if (strlen($nome) < 3) $erros[] = 'Nome deve ter pelo menos 3 caracteres.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $erros[] = 'E-mail inválido.';
    
    $foto = $usuario['foto'];
    
    // 📸 UPLOAD DA FOTO (MESMA LÓGICA DO PERFIL)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['foto']['tmp_name'];
        $fileType = mime_content_type($tmpName);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        $maxSize = 2 * 1024 * 1024; // 2MB
        
        if (!in_array($fileType, $allowedTypes)) {
            $erros[] = 'Formato inválido. Use JPG, PNG, WebP ou GIF.';
        } elseif ($_FILES['foto']['size'] > $maxSize) {
            $erros[] = 'A imagem deve ter no máximo 2MB.';
        } else {
            $extensao = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
            $novoNome = 'perfil_' . $id . '_' . time() . '.' . $extensao;
            $pasta = 'imagens/';
            
            if (!is_dir($pasta)) {
                mkdir($pasta, 0777, true);
            }
            
            $caminhoDestino = $pasta . $novoNome;
            
            if (move_uploaded_file($tmpName, $caminhoDestino)) {
                if ($foto && file_exists($foto)) {
                    unlink($foto);
                }
                $foto = $caminhoDestino;
            } else {
                $erros[] = 'Erro ao salvar a imagem. Verifique permissões da pasta.';
            }
        }
    }
    
    if (empty($erros)) {
        if ($nova_senha !== '' && strlen($nova_senha) < 6) {
            $erros[] = 'Senha deve ter pelo menos 6 caracteres.';
        }
    }
    
    if (empty($erros)) {
        if ($nova_senha !== '') {
            $hash = hashSenha($nova_senha);
            $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, bio=?, foto=?, admin=?, senha=? WHERE id=?");
            $stmt->execute([$nome, $email, $bio, $foto, $admin, $hash, $id]);
        } else {
            $stmt = $pdo->prepare("UPDATE usuarios SET nome=?, email=?, bio=?, foto=?, admin=? WHERE id=?");
            $stmt->execute([$nome, $email, $bio, $foto, $admin, $id]);
        }
        
        mensagem('sucesso', 'Usuário atualizado com sucesso! Foto e dados salvos. ✓');
        redirecionar('admin_usuarios.php');
    }
    
    foreach($erros as $e) mensagem('erro', $e);
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - Admin | Impacto Verde</title>
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
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background: var(--fundo);
            color: var(--texto);
            line-height: 1.6;
            min-height: 100vh;
        }
        header {
            background: var(--gradiente-header);
            color: white;
            padding: 0.75rem 0;
            box-shadow: var(--sombra);
            position: sticky;
            top: 0;
            z-index: 1000;
        }
        header .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .logo {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            transition: transform 0.3s;
        }
        .logo:hover { transform: scale(1.05); }
        .logo img { 
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
            height: 45px;
            width: auto;
        }
        nav ul {
            display: flex;
            list-style: none;
            gap: 0.5rem;
            align-items: center;
            flex-wrap: wrap;
        }
        nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            padding: 0.6rem 1rem;
            border-radius: 25px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        nav a:hover, nav a.ativo {
            background: var(--hover-bg);
            transform: translateY(-2px);
        }
        nav .btn-destaque {
            background: linear-gradient(135deg, var(--verde-claro), var(--verde-neon));
            color: #000;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(76, 175, 80, 0.3);
        }
        nav .btn-destaque:hover {
            background: linear-gradient(135deg, var(--verde-neon), var(--verde-claro));
            color: #000;
            box-shadow: 0 4px 15px rgba(76, 175, 80, 0.5);
        }
        .theme-toggle {
            background: var(--hover-bg);
            border: 2px solid rgba(255,255,255,0.3);
            color: white;
            padding: 0.5rem 0.8rem;
            border-radius: 25px;
            cursor: pointer;
            font-size: 1.2rem;
            transition: all 0.3s;
        }
        .theme-toggle:hover {
            background: rgba(255,255,255,0.2);
            transform: rotate(15deg);
        }
        .auth-container {
            min-height: calc(100vh - 200px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: linear-gradient(135deg, var(--verde-suave) 0%, var(--fundo) 100%);
        }
        .auth-card {
            background: var(--card-bg);
            border-radius: 20px;
            padding: 2.5rem;
            box-shadow: var(--sombra-hover);
            width: 100%;
            max-width: 600px;
            border: 1px solid var(--borda);
            animation: slideIn 0.5s ease;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        .auth-header i {
            font-size: 3rem;
            color: var(--verde-medio);
            margin-bottom: 1rem;
            display: block;
        }
        body.dark-mode .auth-header i {
            color: var(--verde-neon);
        }
        .auth-header h3 {
            color: var(--texto);
            font-size: 1.8rem;
            margin-bottom: 0.5rem;
        }
        .auth-header p {
            color: var(--texto-secundario);
        }
        .form-group {
            margin-bottom: 1.5rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--verde-medio);
        }
        body.dark-mode .form-group label {
            color: var(--verde-neon);
        }
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 0.9rem 1rem;
            border: 2px solid var(--borda);
            border-radius: 12px;
            font-size: 1rem;
            background: var(--input-bg);
            color: var(--texto);
            transition: all 0.3s;
        }
        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--verde-medio);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
        }
        body.dark-mode .form-group input:focus,
        body.dark-mode .form-group textarea:focus {
            border-color: var(--verde-neon);
            box-shadow: 0 0 0 4px rgba(102, 187, 106, 0.1);
        }
        .form-group textarea {
            min-height: 100px;
            resize: vertical;
            font-family: inherit;
        }
        .form-group small {
            color: var(--texto-secundario);
            font-size: 0.85rem;
        }
        .form-check {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 0;
        }
        .form-check-input { width: 1.2rem; height: 1.2rem; cursor: pointer; }
        .form-check-label { color: var(--texto); font-weight: 500; cursor: pointer; }
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.7rem 1.8rem;
            background: linear-gradient(135deg, var(--verde-medio), var(--verde-claro));
            color: white;
            text-decoration: none;
            border-radius: 30px;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 10px rgba(46, 125, 50, 0.3);
        }
        .btn:hover {
            background: linear-gradient(135deg, var(--verde-escuro), var(--verde-medio));
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(46, 125, 50, 0.4);
        }
        .btn-outline {
            background: transparent;
            border: 2px solid var(--verde-medio);
            color: var(--verde-medio);
            box-shadow: none;
        }
        body.dark-mode .btn-outline {
            border-color: var(--verde-neon);
            color: var(--verde-neon);
        }
        .btn-outline:hover {
            background: var(--verde-medio);
            color: white;
        }
        body.dark-mode .btn-outline:hover {
            background: var(--verde-neon);
            color: #000;
        }
        .btn-lg {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
        }
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        .alert-success {
            background: rgba(76, 175, 80, 0.15);
            border: 2px solid var(--verde-medio);
            color: var(--verde-escuro);
        }
        body.dark-mode .alert-success { color: var(--verde-neon); }
        .alert-danger {
            background: rgba(244, 67, 54, 0.15);
            border: 2px solid #f44336;
            color: #c62828;
        }
        .alert-info {
            background: rgba(76, 175, 80, 0.15);
            border: 2px solid var(--verde-medio);
            color: var(--verde-escuro);
        }
        footer {
            background: var(--gradiente-header);
            color: white;
            padding: 3rem 0 1.5rem;
            margin-top: 4rem;
        }
        .footer-bottom {
            text-align: center;
            padding-top: 2rem;
            border-top: 1px solid rgba(255,255,255,0.2);
            color: rgba(255,255,255,0.7);
        }
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
                <img src="logo.svg" alt="Impacto Verde" height="45">
            </a>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="bi bi-house"></i> Início</a></li>
                    <li><a href="admin.php" class="ativo"><i class="bi bi-shield-lock"></i> Admin</a></li>
                    <li><a href="admin_usuarios.php" class="btn-destaque"><i class="bi bi-people"></i> Usuários</a></li>
                    <li><a href="admin_noticias.php"><i class="bi bi-newspaper"></i> Notícias</a></li>
                    <li><a href="perfil.php"><i class="bi bi-person"></i> Perfil</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    <li><button class="theme-toggle" id="themeToggle">🌙</button></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header">
                <i class="bi bi-pencil-square"></i>
                <h3>Editar Usuário</h3>
                <p><?= htmlspecialchars($usuario['nome']) ?></p>
            </div>
            
            <?php exibirFlash(); ?>
            
            <!-- ✅ ENCTYPE É OBRIGATÓRIO PARA UPLOAD FUNCIONAR -->
            <form method="POST" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="nome"><i class="bi bi-person-fill"></i> Nome Completo *</label>
                            <input type="text" id="nome" name="nome" required value="<?= htmlspecialchars($usuario['nome']) ?>">
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="email"><i class="bi bi-envelope-fill"></i> E-mail *</label>
                            <input type="email" id="email" name="email" required value="<?= htmlspecialchars($usuario['email']) ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="bio"><i class="bi bi-card-text"></i> Biografia</label>
                    <textarea id="bio" name="bio" placeholder="Conte um pouco sobre este usuário..."><?= htmlspecialchars($usuario['bio'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="foto"><i class="bi bi-image"></i> Foto de Perfil</label>
                    <?php if(!empty($usuario['foto']) && file_exists($usuario['foto'])): ?>
                        <div class="mb-2">
                            <!-- ?t=<?= time() ?> FORÇA O NAVEGADOR A BAIXAR A IMAGEM NOVA -->
                            <img src="<?= htmlspecialchars($usuario['foto']) ?>?t=<?= time() ?>" 
                                 alt="Foto atual" 
                                 style="max-height: 100px; border-radius: 8px; border: 2px solid var(--verde-medio);">
                            <p class="text-muted small mt-1">Foto atual</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="foto" name="foto" accept="image/*" class="form-control">
                    <small>Formatos: JPG, PNG, WebP. Máx: 2MB</small>
                </div>
                
                <div class="form-group">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="admin" id="admin" value="1" <?= $usuario['admin'] ? 'checked' : '' ?>>
                        <label class="form-check-label" for="admin">
                            <i class="bi bi-shield-lock"></i> Este usuário é Administrador
                        </label>
                    </div>
                </div>
                
                <hr class="my-4">
                
                <div class="form-group">
                    <label for="nova_senha"><i class="bi bi-lock-fill"></i> Nova Senha</label>
                    <input type="password" id="nova_senha" name="nova_senha" placeholder="Deixe em branco para manter a senha atual">
                    <small>Informe apenas se desejar trocar a senha do usuário.</small>
                </div>
                
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Nota:</strong> As alterações serão salvas imediatamente após clicar em "Salvar".
                </div>
                
                <div class="d-flex gap-2 flex-wrap">
                    <button type="submit" class="btn btn-lg">
                        <i class="bi bi-check-circle"></i> Salvar Alterações
                    </button>
                    <a href="admin_usuarios.php" class="btn btn-outline btn-lg">
                        <i class="bi bi-x-circle"></i> Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Impacto Verde. Painel Administrativo. 🌱</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>