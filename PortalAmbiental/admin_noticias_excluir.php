<?php
require_once 'conexao.php';
require_once 'verifica_login.php';
requireAdmin();

$pdo = conectar();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    mensagem('erro', 'Notícia não encontrada.');
    redirecionar('admin_noticias.php');
}

$stmt = $pdo->prepare("SELECT * FROM noticias WHERE id = ?");
$stmt->execute([$id]);
$noticia = $stmt->fetch();

if ($noticia) {
    // Remover imagem se existir
    if (!empty($noticia['imagem']) && file_exists($noticia['imagem'])) {
        unlink($noticia['imagem']);
    }
    
    // Excluir notícia
    $stmt = $pdo->prepare("DELETE FROM noticias WHERE id = ?");
    if ($stmt->execute([$id])) {
        mensagem('sucesso', 'Notícia excluída com sucesso. 🗑️');
    } else {
        mensagem('erro', 'Erro ao excluir notícia.');
    }
} else {
    mensagem('erro', 'Notícia não encontrada.');
}

redirecionar('admin_noticias.php');
?>