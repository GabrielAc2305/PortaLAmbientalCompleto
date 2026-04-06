<?php
// funcoes.php - Funções auxiliares do sistema

session_start();

function estaLogado() {
    return isset($_SESSION['usuario_id']);
}

function ehAdmin() {
    return isset($_SESSION['usuario_admin']) && $_SESSION['usuario_admin'] == 1;
}

function requireLogin() {
    if (!estaLogado()) {
        mensagem('erro', 'Faça login para acessar esta página.');
        redirecionar('login.php');
    }
}

function requireAdmin() {
    if (!estaLogado()) {
        mensagem('erro', 'Faça login para acessar esta página.');
        redirecionar('login.php');
    }
    if (!ehAdmin()) {
        mensagem('erro', 'Acesso negado. Apenas administradores podem acessar esta página.');
        redirecionar('index.php');
    }
}

function redirecionar($url) {
    header("Location: $url");
    exit;
}

function hashSenha($senha) {
    return password_hash($senha, PASSWORD_DEFAULT);
}

function verificarSenha($senha, $hash) {
    return password_verify($senha, $hash);
}

function sanitizar($texto) {
    return htmlspecialchars(trim($texto), ENT_QUOTES, 'UTF-8');
}

function resumirTexto($texto, $limite = 150) {
    $texto = strip_tags($texto);
    if (mb_strlen($texto) <= $limite) return $texto;
    return mb_substr($texto, 0, $limite) . '...';
}

function formatarData($data) {
    setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'portuguese');
    return strftime('%d de %B de %Y', strtotime($data));
}

function uploadImagem($arquivo, $pasta = 'imagens/') {
    if (!isset($arquivo['tmp_name']) || $arquivo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }
    
    $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));
    $permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    
    if (!in_array($extensao, $permitidas)) {
        return null;
    }
    
    $novoNome = uniqid('news_') . '.' . $extensao;
    $caminho = $pasta . $novoNome;
    
    if (!is_dir($pasta)) {
        mkdir($pasta, 0755, true);
    }
    
    if (move_uploaded_file($arquivo['tmp_name'], $caminho)) {
        return $caminho;
    }
    return null;
}

function mensagem($tipo, $texto) {
    $_SESSION['flash'] = ['tipo' => $tipo, 'texto' => $texto];
}

function exibirFlash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        $classe = $flash['tipo'] === 'sucesso' ? 'alert-success' : 'alert-danger';
        $icone = $flash['tipo'] === 'sucesso' ? 'check-circle' : 'exclamation-triangle';
        echo "<div class='alert {$classe} alert-dismissible fade show' role='alert'>
                <i class='bi bi-{$icone}-fill'></i> {$flash['texto']}
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
              </div>";
    }
}
?>