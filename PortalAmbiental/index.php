<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$pdo = conectar();
$stmt = $pdo->prepare("
    SELECT n.*, u.nome as autor_nome, u.foto as autor_foto 
    FROM noticias n 
    JOIN usuarios u ON n.autor = u.id 
    ORDER BY n.data DESC 
    LIMIT 6
");
$stmt->execute();
$noticias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Impacto Verde - Notícias Ambientais</title>
    <link rel="icon" type="image/svg+xml" href="logo-icon.svg">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="oi.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <img src="logo.svg" alt="Impacto Verde" height="45">
            </a>
            <nav>
                <ul>
                    <li><a href="index.php" class="ativo"><i class="bi bi-house"></i> Início</a></li>
                    <li><a href="sobre.php"><i class="bi bi-info-circle"></i> Quem Somos</a></li>
                    <li><a href="contato.php"><i class="bi bi-envelope"></i> Contato</a></li>
                    <?php if(estaLogado()): ?>
                        <?php if(ehAdmin()): ?>
                            <li><a href="admin.php" class="btn-destaque"><i class="bi bi-shield-lock"></i> Admin</a></li>
                        <?php endif; ?>
                        <li><a href="perfil.php"><i class="bi bi-person"></i> Perfil</a></li>
                        <li><a href="minhas_noticias.php" class="btn-destaque"><i class="bi bi-newspaper"></i> Minhas Notícias</a></li>
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

    <section class="hero">
        <div class="falling-leaves">
            <span>🍃</span><span>🌿</span><span>🍀</span><span>🍃</span><span>🌱</span>
        </div>
        <div class="container">
            <h1>
                <span class="leaf-icon">🍃</span>
                <span class="title-text">Impacto Verde</span>
            </h1>
            <p>Notícias que transformam. Sustentabilidade que inspira. Junte-se a nós na construção de um futuro mais verde!</p>
            <div class="hero-buttons">
                <?php if(!estaLogado()): ?>
                <a href="login.php" class="btn btn-lg">
                    <i class="bi bi-box-arrow-in-right"></i> Fazer Login
                </a>
                <?php else: ?>
                <a href="nova_noticia.php" class="btn btn-lg">
                    <i class="bi bi-plus-circle"></i> Publicar Notícia
                </a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <main class="container">
        <?php exibirFlash(); ?>
        
        <h2 class="mb-4" style="color: var(--verde-medio);">
            <i class="bi bi-newspaper"></i> Últimas Publicações
        </h2>
        
        <div class="noticias-grid">
            <?php foreach($noticias as $noticia): ?>
            <article class="card-noticia">
                <div class="card-imagem">
                    <span class="watermark">
                        <i class="bi bi-patch-check-fill"></i> Impacto Verde
                    </span>
                    <?php if(!empty($noticia['imagem']) && file_exists($noticia['imagem'])): ?>
                        <img src="<?= htmlspecialchars($noticia['imagem']) ?>" alt="<?= htmlspecialchars($noticia['titulo']) ?>">
                    <?php else: ?>
                        <i class="bi bi-image"></i>
                    <?php endif; ?>
                </div>
                <div class="card-conteudo">
                    <h3><?= htmlspecialchars($noticia['titulo']) ?></h3>
                    <p><?= resumirTexto($noticia['noticia'], 120) ?></p>
                    <div class="card-meta">
                        <a href="perfil_publico.php?id=<?= $noticia['autor'] ?>" class="autor" title="Ver perfil">
                            <?php if(!empty($noticia['autor_foto']) && file_exists($noticia['autor_foto'])): ?>
                                <img src="<?= htmlspecialchars($noticia['autor_foto']) ?>" alt="<?= htmlspecialchars($noticia['autor_nome']) ?>" style="width: 30px; height: 30px; border-radius: 50%; object-fit: cover; border: 2px solid var(--verde-medio);">
                            <?php else: ?>
                                <i class="bi bi-person-circle" style="font-size: 1.2rem;"></i>
                            <?php endif; ?>
                            <?= htmlspecialchars($noticia['autor_nome']) ?>
                        </a>
                        <span><i class="bi bi-calendar3"></i> <?= formatarData($noticia['data']) ?></span>
                    </div>
                    <a href="noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-outline mt-3">
                        Ler Mais <i class="bi bi-arrow-right-short"></i>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if(empty($noticias)): ?>
            <div class="text-center py-5">
                <i class="bi bi-leaf" style="font-size: 5rem; color: var(--verde-medio);"></i>
                <h4 class="mt-3">Nenhuma publicação ainda</h4>
                <p class="text-muted">Seja o primeiro a compartilhar!</p>
                <?php if(estaLogado()): ?>
                    <a href="nova_noticia.php" class="btn btn-lg">
                        <i class="bi bi-plus-circle"></i> Publicar
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-lg">
                        <i class="bi bi-box-arrow-in-right"></i> Fazer Login
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </main>

    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <img src="logo.svg" alt="Impacto Verde" height="50" style="margin-bottom: 1rem;">
                    <p>Notícias que fazem a diferença. Sustentabilidade e inovação para um planeta melhor.</p>
                    <div class="social-links">
                        <a href="#"><i class="bi bi-facebook"></i></a>
                        <a href="#"><i class="bi bi-instagram"></i></a>
                        <a href="#"><i class="bi bi-twitter"></i></a>
                        <a href="#"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
                <div class="footer-section">
                    <h4>Links</h4>
                    <p>
                        <a href="index.php"><i class="bi bi-chevron-right"></i> Início</a><br>
                        <a href="sobre.php"><i class="bi bi-chevron-right"></i> Quem Somos</a><br>
                        <a href="contato.php"><i class="bi bi-chevron-right"></i> Contato</a>
                    </p>
                </div>
                <div class="footer-section">
                    <h4>Contato</h4>
                    <p>
                        <i class="bi bi-geo-alt"></i> São Paulo/SP<br>
                        <i class="bi bi-envelope"></i> contato@impactoverde.com.br<br>
                        <i class="bi bi-telephone"></i> (11) 3456-7890
                    </p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Impacto Verde. Todos os direitos reservados. 🌱</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>