<?php
$DB_HOST=aws.connect.psdb.cloud
$DB_USERNAME=dfxmgyibyya09510v9rn
$DB_PASSWORD=pscale_pw_bHymMmRWVCZcGLtoKb1SPY3SFMoDwdy98vEn2sbHaTx
$DB_NAME=la-dev

// Conectar ao banco de dados
$conn = new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}
?>
