<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$pdo = conectar();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    mensagem('erro', 'Notícia não encontrada.');
    redirecionar('index.php');
}

$stmt = $pdo->prepare("
    SELECT n.*, u.nome as autor_nome, u.foto as autor_foto, u.bio as autor_bio
    FROM noticias n 
    JOIN usuarios u ON n.autor = u.id 
    WHERE n.id = ?
");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    mensagem('erro', 'Notícia não encontrada.');
    redirecionar('index.php');
}

$pdo->prepare("UPDATE noticias SET visualizacoes = visualizacoes + 1 WHERE id = ?")->execute([$id]);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($noticia['titulo']) ?> - Impacto Verde</title>
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
        .logo img { filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2)); }
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        .page-header {
            background: var(--gradiente-header);
            color: white;
            padding: 3rem 0;
            margin-bottom: 2rem;
            text-align: center;
        }
        .page-header h1 {
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }
        .noticia-completa {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: var(--sombra);
            margin: 2rem 0;
            border: 1px solid var(--borda);
        }
        .noticia-completa h1 {
            color: var(--verde-medio);
            margin-bottom: 1rem;
        }
        body.dark-mode .noticia-completa h1 {
            color: var(--verde-neon);
        }
        .noticia-completa img {
            max-width: 100%;
            border-radius: 12px;
            margin: 1.5rem 0;
            box-shadow: var(--sombra);
        }
        .noticia-imagem-container {
            position: relative;
            display: inline-block;
            margin: 1.5rem 0;
        }
        .noticia-imagem-container .watermark {
            position: absolute;
            top: 10px;
            right: 10px;
            background: rgba(27, 94, 32, 0.85);
            color: white;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.3rem;
        }
        .noticia-meta {
            display: flex;
            gap: 1.5rem;
            color: var(--texto-secundario);
            margin: 1.5rem 0;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--borda);
            flex-wrap: wrap;
            align-items: center;
        }
        .noticia-meta span {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        .noticia-meta a {
            color: var(--verde-medio);
            text-decoration: none;
            font-weight: 600;
        }
        body.dark-mode .noticia-meta a {
            color: var(--verde-neon);
        }
        .conteudo-noticia {
            font-size: 1.1rem;
            line-height: 1.8;
            color: var(--texto);
            white-space: pre-wrap;
        }
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
        .btn-danger {
            background: linear-gradient(135deg, #c62828, #d32f2f);
            box-shadow: 0 4px 10px rgba(198, 40, 40, 0.3);
        }
        .btn-danger:hover {
            background: linear-gradient(135deg, #b71c1c, #c62828);
            box-shadow: 0 6px 15px rgba(198, 40, 40, 0.4);
        }
        .noticias-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }
        .card-noticia {
            background: var(--card-bg);
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--sombra);
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            border: 1px solid var(--borda);
        }
        .card-noticia:hover {
            transform: translateY(-8px);
            box-shadow: var(--sombra-hover);
        }
        .card-imagem {
            height: 200px;
            background: linear-gradient(45deg, var(--verde-escuro), var(--verde-claro));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 3rem;
            position: relative;
            overflow: hidden;
        }
        .card-imagem img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }
        .card-noticia:hover .card-imagem img {
            transform: scale(1.1);
        }
        .card-conteudo {
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        .card-conteudo h3 {
            color: var(--verde-medio);
            margin-bottom: 0.75rem;
            font-size: 1.3rem;
        }
        body.dark-mode .card-conteudo h3 {
            color: var(--verde-neon);
        }
        .card-conteudo p {
            color: var(--texto-secundario);
            font-size: 0.95rem;
            margin-bottom: 1rem;
            flex: 1;
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
            header .container {
                flex-direction: column;
                text-align: center;
            }
            nav ul {
                justify-content: center;
                gap: 0.3rem;
            }
            nav a {
                padding: 0.5rem 0.8rem;
                font-size: 0.9rem;
            }
            .noticia-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            .noticias-grid {
                grid-template-columns: 1fr;
            }
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
                    <li><a href="index.php"><i class="bi bi-house"></i> Início</a></li>
                    <li><a href="sobre.php"><i class="bi bi-info-circle"></i> Quem Somos</a></li>
                    <li><a href="contato.php"><i class="bi bi-envelope"></i> Contato</a></li>
                    <?php if(estaLogado()): ?>
                        <?php if(ehAdmin()): ?>
                            <li><a href="admin.php" class="btn-destaque"><i class="bi bi-shield-lock"></i> Admin</a></li>
                        <?php endif; ?>
                        <li><a href="perfil.php"><i class="bi bi-person"></i> Perfil</a></li>
                        <li><a href="nova_noticia.php" class="btn-destaque"><i class="bi bi-plus-circle"></i> Publicar</a></li>
                        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn-destaque"><i class="bi bi-box-arrow-in-right"></i> Entrar</a></li>
                    <?php endif; ?>
                    <li><button class="theme-toggle" id="themeToggle">🌙</button></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="page-header">
        <div class="container">
            <h1><i class="bi bi-newspaper"></i> Notícia Completa</h1>
        </div>
    </section>

    <main class="container">
        <a href="index.php" class="btn btn-outline mb-4">
            <i class="bi bi-arrow-left"></i> Voltar para Início
        </a>

        <article class="noticia-completa">
            <?php if(!empty($noticia['imagem']) && file_exists($noticia['imagem'])): ?>
                <div class="noticia-imagem-container">
                    <span class="watermark">
                        <i class="bi bi-patch-check-fill"></i> Impacto Verde
                    </span>
                    <img src="<?= htmlspecialchars($noticia['imagem']) ?>" 
                         alt="<?= htmlspecialchars($noticia['titulo']) ?>" 
                         class="img-fluid rounded">
                </div>
            <?php endif; ?>
            
            <h1><?= htmlspecialchars($noticia['titulo']) ?></h1>
            
            <div class="noticia-meta">
                <a href="perfil_publico.php?id=<?= $noticia['autor'] ?>" 
                   class="d-flex align-items-center gap-2" 
                   title="Ver perfil de <?= htmlspecialchars($noticia['autor_nome']) ?>">
                    <?php if(!empty($noticia['autor_foto']) && file_exists($noticia['autor_foto'])): ?>
                        <img src="<?= htmlspecialchars($noticia['autor_foto']) ?>" 
                             alt="<?= htmlspecialchars($noticia['autor_nome']) ?>"
                             style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover; border: 2px solid var(--verde-medio);">
                    <?php else: ?>
                        <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--verde-medio); display: flex; align-items: center; justify-content: center; color: white;">
                            <?= strtoupper(substr($noticia['autor_nome'], 0, 1)) ?>
                        </div>
                    <?php endif; ?>
                    <div>
                        <strong style="display: block;"><?= htmlspecialchars($noticia['autor_nome']) ?></strong>
                        <small style="color: var(--texto-secundario); font-size: 0.85rem;">Clique para ver perfil</small>
                    </div>
                </a>
                <span><i class="bi bi-calendar3"></i> <strong>Data:</strong> <?= formatarData($noticia['data']) ?></span>
                <span><i class="bi bi-eye"></i> <strong>Visualizações:</strong> <?= (int)$noticia['visualizacoes'] ?></span>
            </div>
            
            <div class="conteudo-noticia">
                <?= nl2br(htmlspecialchars($noticia['noticia'])) ?>
            </div>

            <?php if(estaLogado() && (ehAdmin() || $_SESSION['usuario_id'] == $noticia['autor'])): ?>
                <hr class="my-4">
                <div class="d-flex gap-2 flex-wrap">
                    <a href="editar_noticia.php?id=<?= $noticia['id'] ?>" class="btn">
                        <i class="bi bi-pencil"></i> Editar
                    </a>
                    <a href="excluir_noticia.php?id=<?= $noticia['id'] ?>" 
                       class="btn btn-danger"
                       onclick="return confirm('Tem certeza que deseja excluir esta notícia?')">
                        <i class="bi bi-trash"></i> Excluir
                    </a>
                </div>
            <?php endif; ?>
        </article>

        <?php
        $stmt = $pdo->prepare("
            SELECT * FROM noticias 
            WHERE autor = ? AND id != ? 
            ORDER BY data DESC 
            LIMIT 3
        ");
        $stmt->execute([$noticia['autor'], $id]);
        $outras = $stmt->fetchAll();
        ?>
        
        <?php if($outras): ?>
        <section class="mt-5">
            <h3 class="mb-4" style="color: var(--verde-medio);">
                <i class="bi bi-collection"></i> Mais de <?= htmlspecialchars($noticia['autor_nome']) ?>
            </h3>
            <div class="noticias-grid">
                <?php foreach($outras as $outra): ?>
                <article class="card-noticia">
                    <div class="card-imagem">
                        <span class="watermark">
                            <i class="bi bi-patch-check-fill"></i> Impacto Verde
                        </span>
                        <?php if(!empty($outra['imagem']) && file_exists($outra['imagem'])): ?>
                            <img src="<?= htmlspecialchars($outra['imagem']) ?>" alt="<?= htmlspecialchars($outra['titulo']) ?>">
                        <?php else: ?>
                            <i class="bi bi-image"></i>
                        <?php endif; ?>
                    </div>
                    <div class="card-conteudo">
                        <h3><?= htmlspecialchars($outra['titulo']) ?></h3>
                        <p><?= resumirTexto($outra['noticia'], 100) ?></p>
                        <a href="noticia.php?id=<?= $outra['id'] ?>" class="btn btn-outline btn-sm">
                            Ler Mais
                        </a>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </main>

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