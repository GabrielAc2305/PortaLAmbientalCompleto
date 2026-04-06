<?php
require_once 'conexao.php';
require_once 'verifica_login.php';

$pdo = conectar();

// Buscar todos os usuários
$stmt = $pdo->prepare("
    SELECT u.*, COUNT(n.id) as total_noticias 
    FROM usuarios u 
    LEFT JOIN noticias n ON u.id = n.autor 
    GROUP BY u.id 
    ORDER BY u.criado_em DESC
");
$stmt->execute();
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciar Usuários - Portal Ambiental</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="oi.css">
</head>
<body>
    <header>
        <div class="container">
            <a href="index.php" class="logo">
                <i class="bi bi-tree-fill"></i> Portal Ambiental
            </a>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="bi bi-house"></i> Início</a></li>
                    <li><a href="dashboard.php"><i class="bi bi-speedometer"></i> Painel</a></li>
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
            <h2><i class="bi bi-people-fill"></i> Gerenciar Usuários</h2>
            <a href="dashboard.php" class="btn btn-outline">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="usuarios-table table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Publicações</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($usuarios as $user): ?>
                    <tr>
                        <td>#<?= $user['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <?php if($user['foto'] && file_exists($user['foto'])): ?>
                                    <img src="<?= sanitizar($user['foto']) ?>" 
                                         alt="<?= sanitizar($user['nome']) ?>"
                                         style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">
                                <?php else: ?>
                                    <div style="width: 40px; height: 40px; border-radius: 50%; background: var(--verde-primario); display: flex; align-items: center; justify-content: center; color: white;">
                                        <?= strtoupper(substr($user['nome'], 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <?= sanitizar($user['nome']) ?>
                            </div>
                        </td>
                        <td><?= sanitizar($user['email']) ?></td>
                        <td><span class="badge bg-success"><?= $user['total_noticias'] ?></span></td>
                        <td><?= formatarData($user['criado_em']) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="editar_usuario.php?id=<?= $user['id'] ?>" 
                                   class="btn btn-sm btn-outline" 
                                   title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <?php if($user['id'] != $_SESSION['usuario_id']): ?>
                                <a href="excluir_usuario.php?id=<?= $user['id'] ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Tem certeza que deseja excluir este usuário?')"
                                   title="Excluir">
                                    <i class="bi bi-trash"></i>
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="text-center mt-3">
            <p class="text-muted">
                <i class="bi bi-check-circle"></i> 
                <?= count($usuarios) ?> usuário(s) cadastrado(s)
            </p>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> Portal Ambiental. 🌱</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="script.js"></script>
</body>
</html>