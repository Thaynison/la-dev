<?php

$DB_HOST=aws.connect.psdb.cloud
$DB_USERNAME=bihutgynq8a24pp2sy0g
$DB_PASSWORD=pscale_pw_FSITurGdDweD9pFyaTLWQ6PAllTHypLsn3SFtPz64t4
$DB_NAME=la-dev

// Conectar ao banco de dados
$conn = new mysqli($DB_HOST, $DB_USERNAME, $DB_PASSWORD, $DB_NAME);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conn->connect_error);
}
?>
