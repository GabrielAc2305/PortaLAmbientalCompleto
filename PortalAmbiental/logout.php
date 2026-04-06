<?php
require_once 'funcoes.php';
session_destroy();
mensagem('sucesso', 'Você saiu com sucesso. Até logo! 🌿');
redirecionar('index.php');
?>