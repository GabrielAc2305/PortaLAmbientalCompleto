<?php
// conexao.php - Conexão com o banco de dados

define('DB_HOST', 'localhost');
define('DB_NAME', 'bancoaula');
define('DB_USER', 'root');
define('DB_PASS', '');

function conectar() {
    try {
        $pdo = new PDO(
            "mysql:host=".DB_HOST.";dbname=".DB_NAME.";charset=utf8mb4",
            DB_USER, 
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
        return $pdo;
    } catch(PDOException $e) {
        die("Erro ao conectar: " . $e->getMessage());
    }
}
?>