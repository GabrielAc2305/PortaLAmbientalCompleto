<?php
require_once 'funcoes.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quem Somos - Impacto Verde</title>
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
                    <li><a href="index.php"><i class="bi bi-house"></i> Início</a></li>
                    <li><a href="sobre.php" class="ativo"><i class="bi bi-info-circle"></i> Quem Somos</a></li>
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

    <section class="sobre-hero">
        <div class="container">
            <h1><i class="bi bi-people-fill"></i> Quem Somos</h1>
            <p>Conheça nossa missão e valores</p>
        </div>
    </section>

    <main class="container">
        <div class="sobre-content">
            <div class="sobre-section">
                <h2><i class="bi bi-bullseye"></i> Nossa Missão</h2>
                <p>O Impacto Verde nasceu da necessidade de democratizar o acesso à informação sobre sustentabilidade e preservação do meio ambiente.</p>
                <img src="https://images.unsplash.com/photo-1542601906990-b4d3fb778b09?w=800" alt="Missão" class="img-fluid rounded mt-3">
            </div>

            <div class="sobre-section">
                <h2><i class="bi bi-eye"></i> Nossa Visão</h2>
                <p>Ser referência nacional em divulgação de informações ambientais.</p>
                <img src="https://images.unsplash.com/photo-1473341304170-971dccb5ac1e?w=800" alt="Visão" class="img-fluid rounded mt-3">
            </div>

            <div class="sobre-section">
                <h2><i class="bi bi-heart"></i> Nossos Valores</h2>
                <div class="valores-grid">
                    <div class="valor-card">
                        <i class="bi bi-recycle"></i>
                        <h4>Sustentabilidade</h4>
                        <p>Práticas eco-friendly</p>
                    </div>
                    <div class="valor-card">
                        <i class="bi bi-shield-check"></i>
                        <h4>Transparência</h4>
                        <p>Informações confiáveis</p>
                    </div>
                    <div class="valor-card">
                        <i class="bi bi-people"></i>
                        <h4>Comunidade</h4>
                        <p>Juntos somos mais fortes</p>
                    </div>
                    <div class="valor-card">
                        <i class="bi bi-lightbulb"></i>
                        <h4>Inovação</h4>
                        <p>Soluções criativas</p>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Impacto Verde. 🌱</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>