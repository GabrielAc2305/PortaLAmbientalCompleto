<?php
require_once 'funcoes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $tipo = $_POST['tipo'] ?? '';
    $mensagem = trim($_POST['mensagem'] ?? '');
    
    if ($nome && $email && $tipo && $mensagem) {
        mensagem('sucesso', 'Mensagem enviada com sucesso! Entraremos em contato em breve. 📧');
        redirecionar('contato.php');
    } else {
        mensagem('erro', 'Preencha todos os campos obrigatórios.');
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contato - Impacto Verde</title>
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
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1.5rem;
        }
        .contato-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 3rem;
            margin: 2rem 0;
        }
        .contato-info {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: var(--sombra);
            border: 1px solid var(--borda);
        }
        .contato-info h3 {
            color: var(--verde-medio);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }
        body.dark-mode .contato-info h3 {
            color: var(--verde-neon);
        }
        .contato-item {
            display: flex;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: var(--verde-suave);
            border-radius: 12px;
            transition: transform 0.3s;
        }
        .contato-item:hover {
            transform: translateX(5px);
        }
        .contato-item i {
            font-size: 1.5rem;
            color: var(--verde-medio);
            margin-top: 0.2rem;
        }
        body.dark-mode .contato-item i {
            color: var(--verde-neon);
        }
        .contato-item div strong {
            display: block;
            color: var(--texto);
            margin-bottom: 0.25rem;
        }
        .contato-item div span {
            color: var(--texto-secundario);
            font-size: 0.95rem;
        }
        .form-card {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: var(--sombra);
            border: 1px solid var(--borda);
        }
        .form-card h3 {
            color: var(--verde-medio);
            margin-bottom: 1.5rem;
        }
        body.dark-mode .form-card h3 {
            color: var(--verde-neon);
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
        .form-group textarea,
        .form-group select {
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
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--verde-medio);
            box-shadow: 0 0 0 4px rgba(46, 125, 50, 0.1);
        }
        body.dark-mode .form-group input:focus,
        body.dark-mode .form-group textarea:focus,
        body.dark-mode .form-group select:focus {
            border-color: var(--verde-neon);
            box-shadow: 0 0 0 4px rgba(102, 187, 106, 0.1);
        }
        .form-group textarea {
            min-height: 150px;
            resize: vertical;
            font-family: inherit;
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
        .mapa-container {
            background: var(--card-bg);
            padding: 2rem;
            border-radius: 16px;
            box-shadow: var(--sombra);
            border: 1px solid var(--borda);
            text-align: center;
            margin-top: 2rem;
        }
        .mapa-placeholder {
            background: linear-gradient(135deg, var(--verde-suave), var(--fundo));
            border: 2px dashed var(--verde-medio);
            border-radius: 12px;
            padding: 3rem;
            margin-top: 1rem;
        }
        body.dark-mode .mapa-placeholder {
            border-color: var(--verde-neon);
        }
        .mapa-placeholder i {
            font-size: 4rem;
            color: var(--verde-medio);
            margin-bottom: 1rem;
        }
        body.dark-mode .mapa-placeholder i {
            color: var(--verde-neon);
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
            .contato-grid {
                grid-template-columns: 1fr;
                gap: 2rem;
            }
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
                    <li><a href="sobre.php"><i class="bi bi-info-circle"></i> Quem Somos</a></li>
                    <li><a href="contato.php" class="ativo"><i class="bi bi-envelope"></i> Contato</a></li>
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

    <section class="page-header">
        <div class="container">
            <h1><i class="bi bi-envelope-fill"></i> Fale Conosco</h1>
            <p>Quer ser um repórter? Tem uma ideia ou informação? Entre em contato!</p>
        </div>
    </section>

    <main class="container">
        <?php exibirFlash(); ?>
        
        <div class="contato-grid">
            <div class="contato-info">
                <h3><i class="bi bi-chat-dots"></i> Como Participar</h3>
                
                <div class="contato-item">
                    <i class="bi bi-mic-fill"></i>
                    <div>
                        <strong>Seja um Repórter</strong>
                        <span>Quer fazer parte da nossa equipe? Preencha o formulário e conte-nos sobre sua experiência!</span>
                    </div>
                </div>
                
                <div class="contato-item">
                    <i class="bi bi-lightbulb-fill"></i>
                    <div>
                        <strong>Envie uma Ideia</strong>
                        <span>Tem uma sugestão de pauta ou tema? Adoraríamos ouvir você!</span>
                    </div>
                </div>
                
                <div class="contato-item">
                    <i class="bi bi-newspaper"></i>
                    <div>
                        <strong>Informações e Denúncias</strong>
                        <span>Viu algo importante? Envie informações sobre questões ambientais.</span>
                    </div>
                </div>
                
                <div class="contato-item">
                    <i class="bi bi-envelope-fill"></i>
                    <div>
                        <strong>E-mail</strong>
                        <span>contato@impactoverde.com.br<br>reporter@impactoverde.com.br</span>
                    </div>
                </div>
                
                <div class="contato-item">
                    <i class="bi bi-whatsapp"></i>
                    <div>
                        <strong>WhatsApp</strong>
                        <span>(11) 98765-4321</span>
                    </div>
                </div>
                
                <img src="https://images.unsplash.com/photo-1532996122724-e3c354a0b15b?w=600" 
                     alt="Contato" class="img-fluid rounded mt-3">
            </div>
            
            <div class="form-card">
                <h3><i class="bi bi-send"></i> Envie sua Mensagem</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="nome"><i class="bi bi-person-fill"></i> Nome Completo *</label>
                        <input type="text" id="nome" name="nome" required 
                               placeholder="Seu nome completo"
                               value="<?= isset($_POST['nome']) ? htmlspecialchars($_POST['nome']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="email"><i class="bi bi-envelope-fill"></i> E-mail *</label>
                        <input type="email" id="email" name="email" required 
                               placeholder="seu@email.com"
                               value="<?= isset($_POST['email']) ? htmlspecialchars($_POST['email']) : '' ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo"><i class="bi bi-tag-fill"></i> Tipo de Mensagem *</label>
                        <select id="tipo" name="tipo" required>
                            <option value="">Selecione uma opção...</option>
                            <option value="reporter" <?= (isset($_POST['tipo']) && $_POST['tipo'] === 'reporter') ? 'selected' : '' ?>>🎤 Quero ser Repórter</option>
                            <option value="ideia" <?= (isset($_POST['tipo']) && $_POST['tipo'] === 'ideia') ? 'selected' : '' ?>>💡 Enviar Ideia/Sugestão</option>
                            <option value="informacao" <?= (isset($_POST['tipo']) && $_POST['tipo'] === 'informacao') ? 'selected' : '' ?>>📰 Enviar Informação/Denúncia</option>
                            <option value="outro" <?= (isset($_POST['tipo']) && $_POST['tipo'] === 'outro') ? 'selected' : '' ?>>📧 Outro Assunto</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="mensagem"><i class="bi bi-chat-square-text-fill"></i> Mensagem *</label>
                        <textarea id="mensagem" name="mensagem" required 
                                  placeholder="Conte-nos mais sobre seu interesse, ideia ou informação..."
                                  style="min-height: 150px;"><?= isset($_POST['mensagem']) ? htmlspecialchars($_POST['mensagem']) : '' ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-lg w-100">
                        <i class="bi bi-send"></i> Enviar Mensagem
                    </button>
                </form>
            </div>
        </div>
        
        <div class="mapa-container">
            <h3><i class="bi bi-geo-alt"></i> Onde Estamos</h3>
            <div class="mapa-placeholder">
                <i class="bi bi-geo-alt"></i>
                <h4>Redação Impacto Verde</h4>
                <p>Av. Sustentável, 1000 - São Paulo/SP</p>
                <p class="text-muted">
                    <small><em>Visitas apenas com agendamento prévio</em></small>
                </p>
            </div>
        </div>
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