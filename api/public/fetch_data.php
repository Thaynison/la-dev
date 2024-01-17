<?php
include 'conexao.php';

$sql = "SELECT * FROM pontos";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  $rows = array();
  while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
  }
  echo json_encode($rows);
} else {
  echo "Nenhum dado encontrado";
}

$conn->close();
?>
