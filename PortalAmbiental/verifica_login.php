<?php
require_once 'conexao.php';
require_once 'funcoes.php';

if (!estaLogado()) {
    mensagem('erro', 'Faça login para acessar esta página.');
    redirecionar('login.php');
}
?>