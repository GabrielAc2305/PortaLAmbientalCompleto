<?php
require_once 'conexao.php';
require_once 'verifica_login.php';
// NÃO usar requireAdmin() - autor ou admin pode excluir!

$pdo = conectar();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    mensagem('erro', 'Notícia não encontrada.');
    redirecionar('index.php');
}

// Busca a notícia
$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if (!$noticia) {
    mensagem('erro', 'Notícia não encontrada.');
    redirecionar('index.php');
}

// Verifica permissão: é o autor OU é admin?
if ($_SESSION['usuario_id'] != $noticia['autor'] && !ehAdmin()) {
    mensagem('erro', 'Você só pode excluir suas próprias notícias.');
    redirecionar('index.php');
}

// Remove imagem se existir
if (!empty($noticia['imagem']) && file_exists($noticia['imagem'])) {
    unlink($noticia['imagem']);
}

// Exclui a notícia
$stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
if ($stmt->execute([$id])) {
    mensagem('sucesso', 'Notícia excluída com sucesso! 🗑️');
} else {
    mensagem('erro', 'Erro ao excluir notícia.');
}

redirecionar('index.php');
?>