<?php
require_once 'conexao.php';
require_once 'verifica_login.php';

$pdo = conectar();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id) {
    mensagem('erro', 'Usuário não encontrado.');
    redirecionar('usuarios.php');
}

// Não permitir excluir a si mesmo
if ($id == $_SESSION['usuario_id']) {
    mensagem('erro', 'Você não pode excluir sua própria conta.');
    redirecionar('usuarios.php');
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if ($usuario) {
    // Remover foto se existir
    if ($usuario['foto'] && file_exists($usuario['foto'])) {
        unlink($usuario['foto']);
    }
    
    // Excluir usuário (notícias serão excluídas em cascata)
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    if ($stmt->execute([$id])) {
        mensagem('sucesso', 'Usuário excluído com sucesso. 🗑️');
    } else {
        mensagem('erro', 'Erro ao excluir usuário.');
    }
} else {
    mensagem('erro', 'Usuário não encontrado.');
}

redirecionar('usuarios.php');
?>