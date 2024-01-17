<?php
// Incluir o arquivo de conexão
include './public/conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Preparar e executar a consulta SQL
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->bind_param("s", $username);

    $stmt->execute();

    $result = $stmt->get_result();

    // Verificar o resultado da consulta
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            header("Location: ./public/ponto.php");
            exit();
        }
    }

    // Mostrar alerta no HTML
    echo '<script>alert("Usuário ou senha inválidos. Tente novamente.");</script>';
}

// Fechar a conexão com o banco de dados (você também pode remover esta linha se desejar manter a conexão aberta)
$conn->close();
?>
