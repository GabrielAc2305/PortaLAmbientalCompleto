<?php
require_once 'conexao.php';
require_once 'verifica_login.php';
requireAdmin();

$pdo = conectar();
$stmt = $pdo->query("SELECT n.*, u.nome as autor_nome FROM noticias n JOIN usuarios u ON n.autor = u.id ORDER BY n.data DESC");
$noticias = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Notícias - Impacto Verde</title>
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
        .perfil-card {
            background: var(--card-bg);
            border-radius: 16px;
            padding: 2.5rem;
            box-shadow: var(--sombra);
            margin: 2rem 0;
            border: 1px solid var(--borda);
        }
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }
        .dashboard-header h2 {
            color: var(--verde-medio);
        }
        body.dark-mode .dashboard-header h2 {
            color: var(--verde-neon);
        }
        .table {
            background: var(--card-bg);
            border-radius: 12px;
            overflow: hidden;
            box-shadow: var(--sombra);
        }
        .table thead {
            background: var(--verde-suave);
        }
        body.dark-mode .table thead {
            background: var(--verde-escuro);
        }
        .table th {
            color: var(--texto);
            font-weight: 600;
            border: none;
            padding: 1rem;
        }
        .table td {
            color: var(--texto-secundario);
            padding: 1rem;
            border-color: var(--borda);
            vertical-align: middle;
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
        .btn-lg {
            padding: 1rem 2.5rem;
            font-size: 1.1rem;
        }
        .btn-sm {
            padding: 0.4rem 0.8rem;
            font-size: 0.85rem;
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
        body.dark-mode .alert-success {
            color: var(--verde-neon);
        }
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
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }
            .table-responsive {
                overflow-x: auto;
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

    <main class="container py-4">
        <?php exibirFlash(); ?>
        
        <div class="dashboard-header">
            <h2><i class="bi bi-newspaper"></i> Gerenciar Notícias</h2>
            <div>
                <a href="nova_noticia.php" class="btn btn-lg">
                    <i class="bi bi-plus-circle-fill"></i> Nova Notícia
                </a>
                <a href="admin.php" class="btn btn-outline btn-lg">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
            </div>
        </div>

        <div class="perfil-card">
            <?php if(count($noticias) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título</th>
                            <th>Autor</th>
                            <th>Data</th>
                            <th>Views</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($noticias as $n): ?>
                        <tr>
                            <td>#<?= $n['id'] ?></td>
                            <td><?= htmlspecialchars($n['titulo']) ?></td>
                            <td><?= htmlspecialchars($n['autor_nome']) ?></td>
                            <td><?= formatarData($n['data']) ?></td>
                            <td><span class="badge bg-success"><?= (int)$n['visualizacoes'] ?></span></td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="noticia.php?id=<?= $n['id'] ?>" 
                                       class="btn btn-sm btn-outline" 
                                       target="_blank" 
                                       title="Ver">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="admin_noticias_editar.php?id=<?= $n['id'] ?>" 
                                       class="btn btn-sm btn-outline" 
                                       title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <a href="admin_noticias_excluir.php?id=<?= $n['id'] ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja excluir esta notícia?')"
                                       title="Excluir">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="bi bi-newspaper" style="font-size: 4rem; color: var(--verde-medio);"></i>
                <h4 class="mt-3">Nenhuma notícia cadastrada</h4>
                <p class="text-muted">Seja o primeiro a publicar uma notícia!</p>
                <a href="nova_noticia.php" class="btn btn-lg mt-3">
                    <i class="bi bi-plus-circle"></i> Publicar Primeira Notícia
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <?php if(count($noticias) > 0): ?>
        <div class="text-center mt-3">
            <p class="text-muted">
                <i class="bi bi-check-circle"></i> 
                <?= count($noticias) ?> notícia(s) cadastrada(s)
            </p>
        </div>
        <?php endif; ?>
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