<?php
// Incluir o arquivo de conexão
include './public/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Preparar e executar a consulta SQL
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $username, $password);

    $stmt->execute();

    $result = $stmt->get_result();

    // Verificar o resultado da consulta
    if ($result->num_rows > 0) {
        header("Location: ./public/ponto.php");
        exit();
    } else {
        header("Location: registrar.php");
        exit();
    }

    $stmt->close();
}

// Fechar a conexão com o banco de dados (você também pode remover esta linha se desejar manter a conexão aberta)
$conn->close();
?>
