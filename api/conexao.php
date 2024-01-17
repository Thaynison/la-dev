<?php
include 'database.php';

// Conectar ao banco de dados
$conn = new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}

// Configurar SSL
$cert_path = "/etc/ssl/certs/ca-certificates.crt";
$conn->ssl_set(NULL, NULL, $cert_path, NULL, NULL);
?>
