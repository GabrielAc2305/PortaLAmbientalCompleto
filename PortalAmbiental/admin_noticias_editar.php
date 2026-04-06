<?php
require_once 'conexao.php';
require_once 'verifica_login.php';
requireAdmin();

$pdo = conectar();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) { mensagem('erro', 'Notícia não encontrada.'); redirecionar('admin_noticias.php'); }

$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();
if (!$noticia) { mensagem('erro', 'Notícia não encontrada.'); redirecionar('admin_noticias.php'); }

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = trim($_POST['titulo'] ?? '');
    $conteudo = trim($_POST['noticia'] ?? '');
    $erros = [];
    if (strlen($titulo) < 5) $erros[] = 'Título curto.';
    if (strlen($conteudo) < 20) $erros[] = 'Conteúdo curto.';
    
    $imagem = $noticia['imagem'];
    if (isset($_FILES['imagem']) && $_FILES['imagem']['error'] === UPLOAD_ERR_OK) {
        $tmpName = $_FILES['imagem']['tmp_name'];
        $fileType = mime_content_type($tmpName);
        $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
        if (!in_array($fileType, $allowed)) $erros[] = 'Formato inválido.';
        elseif ($_FILES['imagem']['size'] > 2097152) $erros[] = 'Máx 2MB.';
        else {
            $ext = pathinfo($_FILES['imagem']['name'], PATHINFO_EXTENSION);
            $novo = 'noticia_' . $id . '_' . time() . '.' . $ext;
            if (!is_dir('imagens/')) mkdir('imagens/', 0777, true);
            $destino = 'imagens/' . $novo;
            if (move_uploaded_file($tmpName, $destino)) {
                if ($imagem && file_exists($imagem)) unlink($imagem);
                $imagem = $destino;
            } else $erros[] = 'Erro ao salvar imagem.';
        }
    }
    
    if (empty($erros)) {
        $stmt = $pdo->prepare("UPDATE noticias SET titulo=?, noticia=?, imagem=? WHERE id=?");
        if ($stmt->execute([$titulo, $conteudo, $imagem, $id])) {
            mensagem('sucesso', 'Notícia atualizada pelo Admin! ✏️'); redirecionar('admin_noticias.php');
        } else mensagem('erro', 'Erro ao atualizar.');
    } else { foreach($erros as $e) mensagem('erro', $e); }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Notícia (Admin) - Impacto Verde</title>
    <link rel="icon" type="image/svg+xml" href="logo-icon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root { --verde-escuro: #1b5e20; --verde-medio: #2e7d32; --verde-claro: #4caf50; --verde-suave: #e8f5e9; --verde-neon: #66bb6a; --fundo: #f8fff8; --fundo-secundario: #ffffff; --texto: #333333; --texto-secundario: #666666; --borda: #c8e6c9; --sombra: 0 2px 15px rgba(27, 94, 32, 0.15); --sombra-hover: 0 8px 25px rgba(27, 94, 32, 0.25); --gradiente-header: linear-gradient(135deg, #1b5e20 0%, #2e7d32 50%, #4caf50 100%); --card-bg: #ffffff; --input-bg: #ffffff; --hover-bg: rgba(255,255,255,0.1); }
        body.dark-mode { --verde-escuro: #1b5e20; --verde-medio: #66bb6a; --verde-claro: #81c784; --verde-neon: #a5d6a7; --verde-suave: #1b3a1b; --fundo: #0f1f0f; --fundo-secundario: #1a2f1a; --texto: #e0e0e0; --texto-secundario: #b0b0b0; --borda: #2d4a2d; --sombra: 0 2px 15px rgba(0,0,0,0.3); --sombra-hover: 0 8px 25px rgba(0,0,0,0.5); --gradiente-header: linear-gradient(135deg, #0f1f0f 0%, #1a3a1a 50%, #2d4a2d 100%); --card-bg: #1a2f1a; --input-bg: #2d4a2d; --hover-bg: rgba(255,255,255,0.05); }
        * { margin: 0; padding: 0; box-sizing: border-box; transition: background-color 0.3s, color 0.2s; }
        body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; background: var(--fundo); color: var(--texto); line-height: 1.6; min-height: 100vh; }
        header { background: var(--gradiente-header); color: white; padding: 0.75rem 0; box-shadow: var(--sombra); position: sticky; top: 0; z-index: 1000; }
        header .container { max-width: 1200px; margin: 0 auto; padding: 0 1.5rem; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .logo { display: flex; align-items: center; gap: 0.5rem; text-decoration: none; color: white; font-size: 1.5rem; font-weight: 700; transition: transform 0.3s; }
        .logo:hover { transform: scale(1.05); }
        .logo img { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); height: 45px; width: auto; }
        nav ul { display: flex; list-style: none; gap: 0.5rem; align-items: center; flex-wrap: wrap; }
        nav a { color: white; text-decoration: none; font-weight: 500; padding: 0.6rem 1rem; border-radius: 25px; transition: all 0.3s; display: flex; align-items: center; gap: 0.3rem; }
        nav a:hover, nav a.ativo { background: var(--hover-bg); transform: translateY(-2px); }
        nav .btn-destaque { background: linear-gradient(135deg, var(--verde-claro), var(--verde-neon)); color: #000; font-weight: 600; box-shadow: 0 2px 10px rgba(76, 175, 80, 0.3); }
        nav .btn-destaque:hover { background: linear-gradient(135deg, var(--verde-neon), var(--verde-claro)); color: #000; box-shadow: 0 4px 15px rgba(76, 175, 80, 0.5); }
        .theme-toggle { background: var(--hover-bg); border: 2px solid rgba(255,255,255,0.3); color: white; padding: 0.5rem 0.8rem; border-radius: 25px; cursor: pointer; font-size: 1.2rem; transition: all 0.3s; }
        .theme-toggle:hover { background: rgba(255,255,255,0.2); transform: rotate(15deg); }
        .auth-container { min-height: calc(100vh - 200px); display: flex; align-items: center; justify-content: center; padding: 2rem 1rem; background: linear-gradient(135deg, var(--verde-suave) 0%, var(--fundo) 100%); }
        .auth-card { background: var(--card-bg); border-radius: 20px; padding: 2.5rem; box-shadow: var(--sombra-hover); width: 100%; max-width: 700px; border: 1px solid var(--borda); animation: slideIn 0.5s ease; }
        @keyframes slideIn { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        .auth-header { text-align: center; margin-bottom: 2rem; }
        .auth-header i { font-size: 3rem; color: var(--verde-medio); margin-bottom: 1rem; display: block; }
        body.dark-mode .auth-header i { color: var(--verde-neon); }
        .auth-header h3 { color: var(--texto); font-size: 1.8rem; margin-bottom: 0.5rem; }
        .auth-header p { color: var(--texto-secundario); }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--verde-medio); }
        body.dark-mode .form-group label { color: var(--verde-neon); }
        .form-group input, .form-group textarea { width: 100%; padding: 0.9rem 1rem; border: 2px solid var(--borda); border-radius: 12px; font-size: 1rem; background: var(--input-bg); color: var(--texto); transition: all 0.3s; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--verde-medio); box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1); }
        body.dark-mode .form-group input:focus, body.dark-mode .form-group textarea:focus { border-color: var(--verde-neon); box-shadow: 0 0 0 4px rgba(102, 187, 106, 0.1); }
        .form-group textarea { min-height: 200px; resize: vertical; font-family: inherit; }
        .form-group small { color: var(--texto-secundario); font-size: 0.85rem; }
        .btn { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.7rem 1.8rem; background: linear-gradient(135deg, var(--verde-medio), var(--verde-claro)); color: white; text-decoration: none; border-radius: 30px; font-weight: 600; border: none; cursor: pointer; transition: all 0.3s; box-shadow: 0 4px 10px rgba(46, 125, 50, 0.3); }
        .btn:hover { background: linear-gradient(135deg, var(--verde-escuro), var(--verde-medio)); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(46, 125, 50, 0.4); }
        .btn-outline { background: transparent; border: 2px solid var(--verde-medio); color: var(--verde-medio); box-shadow: none; }
        body.dark-mode .btn-outline { border-color: var(--verde-neon); color: var(--verde-neon); }
        .btn-outline:hover { background: var(--verde-medio); color: white; }
        body.dark-mode .btn-outline:hover { background: var(--verde-neon); color: #000; }
        .btn-lg { padding: 1rem 2.5rem; font-size: 1.1rem; }
        .alert { padding: 1rem 1.5rem; border-radius: 12px; margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; animation: slideIn 0.3s ease; }
        .alert-success { background: rgba(76, 175, 80, 0.15); border: 2px solid var(--verde-medio); color: var(--verde-escuro); }
        body.dark-mode .alert-success { color: var(--verde-neon); }
        .alert-danger { background: rgba(244, 67, 54, 0.15); border: 2px solid #f44336; color: #c62828; }
        footer { background: var(--gradiente-header); color: white; padding: 3rem 0 1.5rem; margin-top: 4rem; }
        .footer-bottom { text-align: center; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.2); color: rgba(255,255,255,0.7); }
        @media (max-width: 768px) { header .container { flex-direction: column; text-align: center; } nav ul { justify-content: center; gap: 0.3rem; } nav a { padding: 0.5rem 0.8rem; font-size: 0.9rem; } .auth-card { padding: 1.5rem; } }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo"><img src="logo.svg" alt="Impacto Verde" height="45"></a>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="bi bi-house"></i> Início</a></li>
                    <li><a href="admin.php" class="ativo"><i class="bi bi-shield-lock"></i> Admin</a></li>
                    <li><a href="admin_usuarios.php"><i class="bi bi-people"></i> Usuários</a></li>
                    <li><a href="admin_noticias.php" class="btn-destaque"><i class="bi bi-newspaper"></i> Notícias</a></li>
                    <li><a href="perfil.php"><i class="bi bi-person"></i> Perfil</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    <li><button class="theme-toggle" id="themeToggle">🌙</button></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-header"><i class="bi bi-pencil-square"></i><h3>Editar Notícia (Admin)</h3><p>Atualizar publicação do sistema</p></div>
            <?php exibirFlash(); ?>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label><i class="bi bi-card-heading"></i> Título *</label>
                    <input type="text" name="titulo" required value="<?= htmlspecialchars($noticia['titulo']) ?>">
                </div>
                <div class="form-group">
                    <label><i class="bi bi-image"></i> Imagem</label>
                    <?php if(!empty($noticia['imagem']) && file_exists($noticia['imagem'])): ?>
                        <div class="mb-2"><img src="<?= htmlspecialchars($noticia['imagem']) ?>?t=<?= time() ?>" style="max-height:150px; border-radius:8px; border:2px solid var(--verde-medio);"><p class="text-muted small">Imagem atual</p></div>
                    <?php endif; ?>
                    <input type="file" name="imagem" accept="image/*" class="form-control">
                    <small>Deixe em branco para manter a atual</small>
                </div>
                <div class="form-group">
                    <label><i class="bi bi-file-text"></i> Conteúdo *</label>
                    <textarea name="noticia" required><?= htmlspecialchars($noticia['noticia']) ?></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-lg"><i class="bi bi-check-circle"></i> Salvar Alterações</button>
                    <a href="admin_noticias.php" class="btn btn-outline btn-lg"><i class="bi bi-x-circle"></i> Cancelar</a>
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