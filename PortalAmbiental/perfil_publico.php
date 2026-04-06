<?php
require_once 'conexao.php';
require_once 'funcoes.php';

$pdo = conectar();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    mensagem('erro', 'Usuário não encontrado.');
    redirecionar('index.php');
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$autor = $stmt->fetch();

if (!$autor) {
    mensagem('erro', 'Usuário não encontrado.');
    redirecionar('index.php');
}

$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM noticias WHERE autor = ?");
$stmt->execute([$id]);
$total_noticias = $stmt->fetch()['total'];

$stmt = $pdo->prepare("SELECT SUM(visualizacoes) as total FROM noticias WHERE autor = ?");
$stmt->execute([$id]);
$total_views = $stmt->fetch()['total'] ?? 0;

$stmt = $pdo->prepare("SELECT * FROM noticias WHERE autor = ? ORDER BY data DESC LIMIT 12");
$stmt->execute([$id]);
$noticias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($autor['nome']) ?> - Perfil | Impacto Verde</title>
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
                    <?php if(estaLogado()): ?>
                        <li><a href="dashboard.php"><i class="bi bi-speedometer"></i> Painel</a></li>
                        <li><a href="perfil.php"><i class="bi bi-person"></i> Meu Perfil</a></li>
                        <li><a href="nova_noticia.php" class="btn-destaque"><i class="bi bi-plus-circle"></i> Publicar</a></li>
                        <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    <?php else: ?>
                        <li><a href="login.php"><i class="bi bi-box-arrow-in-right"></i> Entrar</a></li>
                    <?php endif; ?>
                    <li><button class="theme-toggle" id="themeToggle">🌙</button></li>
                </ul>
            </nav>
        </div>
    </header>

    <section class="page-header">
        <div class="container">
            <h1><i class="bi bi-person-circle"></i> Perfil do Autor</h1>
        </div>
    </section>

    <main class="container py-4">
        <?php exibirFlash(); ?>
        
        <div class="perfil-card">
            <div class="perfil-header">
                <div class="perfil-avatar">
                    <?php if(!empty($autor['foto']) && file_exists($autor['foto'])): ?>
                        <img src="<?= htmlspecialchars($autor['foto']) ?>" alt="<?= htmlspecialchars($autor['nome']) ?>">
                    <?php else: ?>
                        <i class="bi bi-person-fill"></i>
                    <?php endif; ?>
                </div>
                <div class="perfil-info">
                    <h2><?= htmlspecialchars($autor['nome']) ?></h2>
                    <p><i class="bi bi-calendar"></i> Membro desde <?= formatarData($autor['criado_em']) ?></p>
                    <?php if(!empty($autor['bio'])): ?>
                        <p class="mt-3" style="font-size: 1.1rem; max-width: 600px;">
                            <i class="bi bi-info-circle"></i> <?= nl2br(htmlspecialchars($autor['bio'])) ?>
                        </p>
                    <?php else: ?>
                        <p class="mt-3 text-muted"><i class="bi bi-info-circle"></i> Biografia não preenchida</p>
                    <?php endif; ?>
                </div>
                <div class="ms-auto text-center">
                    <div class="dashboard-stats" style="margin: 0;">
                        <div class="stat-card">
                            <h3><?= $total_noticias ?></h3>
                            <p>Publicações</p>
                        </div>
                        <div class="stat-card">
                            <h3><?= number_format($total_views) ?></h3>
                            <p>Visualizações</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <section class="mt-5">
            <h3 class="mb-4" style="color: var(--verde-primario);">
                <i class="bi bi-newspaper"></i> Publicações de <?= htmlspecialchars($autor['nome']) ?>
            </h3>
            
            <?php if($noticias): ?>
                <div class="noticias-grid">
                    <?php foreach($noticias as $noticia): ?>
                    <article class="card-noticia">
                        <div class="card-imagem">
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
                                <span><i class="bi bi-calendar3"></i> <?= formatarData($noticia['data']) ?></span>
                                <span><i class="bi bi-eye"></i> <?= (int)$noticia['visualizacoes'] ?></span>
                            </div>
                            <a href="noticia.php?id=<?= $noticia['id'] ?>" class="btn btn-outline mt-3">
                                Ler Mais <i class="bi bi-arrow-right-short"></i>
                            </a>
                        </div>
                    </article>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="bi bi-newspaper" style="font-size: 4rem; color: var(--verde-primario);"></i>
                    <h4 class="mt-3">Nenhuma publicação ainda</h4>
                    <p class="text-muted">Este usuário ainda não publicou nada.</p>
                </div>
            <?php endif; ?>
        </section>

        <div class="text-center mt-5">
            <a href="index.php" class="btn btn-outline btn-lg">
                <i class="bi bi-arrow-left"></i> Voltar para Início
            </a>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Impacto Verde. Todos os direitos reservados. </p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>