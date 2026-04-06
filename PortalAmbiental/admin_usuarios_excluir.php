<?php
require_once 'conexao.php';
require_once 'verifica_login.php';
requireAdmin();

$pdo = conectar();
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$id || $id == $_SESSION['usuario_id']) {
    mensagem('erro', 'Não é possível excluir.');
    redirecionar('admin_usuarios.php');
}

$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$id]);
$usuario = $stmt->fetch();

if ($usuario) {
    if (!empty($usuario['foto']) && file_exists($usuario['foto'])) {
        unlink($usuario['foto']);
    }
    $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    mensagem('sucesso', 'Usuário excluído! 🗑️');
}

redirecionar('admin_usuarios.php');
?>