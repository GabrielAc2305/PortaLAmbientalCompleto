<?php
require_once 'conexao.php';
require_once 'verifica_login.php';
requireAdmin();

$pdo = conectar();

// Estatísticas gerais
$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios");
$total_usuarios = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM noticias");
$total_noticias = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT COUNT(*) as total FROM usuarios WHERE admin = 1");
$total_admins = $stmt->fetch()['total'];

$stmt = $pdo->query("SELECT SUM(visualizacoes) as total FROM noticias");
$total_views = $stmt->fetch()['total'] ?? 0;

// Últimos usuários cadastrados
$stmt = $pdo->prepare("SELECT * FROM usuarios ORDER BY criado_em DESC LIMIT 5");
$stmt->execute();
$ultimos_usuarios = $stmt->fetchAll();

// Últimas notícias
$stmt = $pdo->prepare("
    SELECT n.*, u.nome as autor_nome 
    FROM noticias n 
    JOIN usuarios u ON n.autor = u.id 
    ORDER BY n.data DESC 
    LIMIT 5
");
$stmt->execute();
$ultimas_noticias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Impacto Verde</title>
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
                    <li><a href="admin.php" class="ativo"><i class="bi bi-shield-lock"></i> Admin</a></li>
                    <li><a href="admin_usuarios.php"><i class="bi bi-people"></i> Usuários</a></li>
                    <li><a href="admin_noticias.php"><i class="bi bi-newspaper"></i> Notícias</a></li>
                    <li><a href="perfil.php"><i class="bi bi-person"></i> Perfil</a></li>
                    <li><a href="logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a></li>
                    <li><button class="theme-toggle" id="themeToggle">🌙</button></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container py-4">
        <?php exibirFlash(); ?>
        
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-shield-lock-fill"></i> Painel Administrativo</h2>
            <span class="badge bg-success">
                <i class="bi bi-check-circle"></i> Admin: <?= htmlspecialchars($_SESSION['usuario_nome']) ?>
            </span>
        </div>

        <div class="dashboard-stats">
            <div class="stat-card">
                <i class="bi bi-people-fill" style="color: var(--verde-medio);"></i>
                <h3><?= $total_usuarios ?></h3>
                <p>Usuários Cadastrados</p>
            </div>
            <div class="stat-card">
                <i class="bi bi-newspaper" style="color: var(--verde-medio);"></i>
                <h3><?= $total_noticias ?></h3>
                <p>Notícias Publicadas</p>
            </div>
            <div class="stat-card">
                <i class="bi bi-eye" style="color: var(--verde-medio);"></i>
                <h3><?= number_format($total_views) ?></h3>
                <p>Visualizações Totais</p>
            </div>
            <div class="stat-card">
                <i class="bi bi-person-badge" style="color: var(--verde-medio);"></i>
                <h3><?= $total_admins ?></h3>
                <p>Administradores</p>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="perfil-card">
                    <h4 class="mb-3"><i class="bi bi-person-plus"></i> Últimos Usuários</h4>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Nome</th>
                                    <th>Email</th>
                                    <th>Data</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ultimos_usuarios as $user): ?>
                                <tr>
                                    <td><?= sanitizar($user['nome']) ?></td>
                                    <td><?= sanitizar($user['email']) ?></td>
                                    <td><?= formatarData($user['criado_em']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="admin_usuarios.php" class="btn btn-outline mt-2">
                        Gerenciar Usuários <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="perfil-card">
                    <h4 class="mb-3"><i class="bi bi-newspaper"></i> Últimas Notícias</h4>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Autor</th>
                                    <th>Views</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($ultimas_noticias as $n): ?>
                                <tr>
                                    <td><?= sanitizar($n['titulo']) ?></td>
                                    <td><?= sanitizar($n['autor_nome']) ?></td>
                                    <td><?= (int)$n['visualizacoes'] ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <a href="admin_noticias.php" class="btn btn-outline mt-2">
                        Gerenciar Notícias <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        <div class="text-center mt-4">
            <a href="admin_usuarios.php" class="btn btn-lg">
                <i class="bi bi-person-plus-fill"></i> Cadastrar Novo Usuário
            </a>
            <a href="nova_noticia.php" class="btn btn-lg">
                <i class="bi bi-plus-circle-fill"></i> Publicar Notícia
            </a>
        </div>
    </main>

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