<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Leitor de Cracha</title>
  <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
  <header>
    <h1>Leitor de Cracha</h1>
  </header>
  <main>
    <video id="preview"></video>
    <div class="info-container" id="infoContainer">
      <p>Carregando dados do banco de dados...</p>
    </div>
    <button onclick="exportToExcel()">Exportar para Excel</button>
  </main>
  <script src="https://rawgit.com/schmich/instascan-builds/master/instascan.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.4/xlsx.full.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

  <script>
    let scannedData = [];

    document.addEventListener('DOMContentLoaded', function () {
      let scanner = new Instascan.Scanner({ video: document.getElementById('preview') });
      let infoContainer = document.getElementById('infoContainer');

      scanner.addListener('scan', function (content) {
        let [nome, codigo] = content.split('|').map(item => item.trim());
        let datas = getdatas();
        let horas = gethoras();

        if (!isCodeAlreadyReadRecently(codigo)) {
          let formData = new FormData();
          formData.append('nome', nome);
          formData.append('codigo', codigo);
          formData.append('horas', horas);
          formData.append('datas', datas);

          fetch('ponto.php', {
            method: 'POST',
            body: formData
          })
            .then(response => response.text())
            .then(data => {
            })
            .catch(error => {
              console.error('Erro ao enviar dados para o servidor:', error);
            });

          scannedData.push({ nome, codigo, horas, datas });
          showPopup(`QR Code lido!\nNome: ${nome}\nCódigo: ${codigo}\nHora: ${horas}\nData: ${datas}`);
          updateInfoContainer();
        } else {
          showPopup('Esse código já foi lido recentemente. Aguarde 10 minutos para ler novamente.');
        }
      });

      Instascan.Camera.getCameras().then(function (cameras) {
        if (cameras.length > 0) {
          scanner.start(cameras[0]);
        } else {
          console.error('No cameras found.');
        }
      }).catch(function (e) {
        console.error(e);
      });

      function fetchDataAndUpdateContainer() {
        fetch('fetch_data.php')
          .then(response => response.json())
          .then(data => {
            scannedData = data;
            updateInfoContainer();
          })
          .catch(error => {
            console.error('Erro ao buscar dados do servidor:', error);
          });
      }

      fetchDataAndUpdateContainer();

      setInterval(fetchDataAndUpdateContainer, 60000); 

      function getdatas() {
        let now = new Date();
        let day = now.getDate();
        let month = now.getMonth() + 1;
        let year = now.getFullYear();
        return `${year}-${month < 10 ? '0' : ''}${month}-${day < 10 ? '0' : ''}${day}`;
      }

      function gethoras() {
        let now = new Date();
        let hours = now.getHours();
        let minutes = now.getMinutes();
        return `${hours < 10 ? '0' : ''}${hours}:${minutes < 10 ? '0' : ''}${minutes}`;
      }

      function showPopup(message) {
        const popup = document.createElement('div');
        popup.classList.add('popup');

        const overlay = document.createElement('div');
        overlay.classList.add('overlay');

        const closeBtn = document.createElement('span');
        closeBtn.innerHTML = '&times;';
        closeBtn.classList.add('close-btn');
        closeBtn.addEventListener('click', () => {
          document.body.removeChild(popup);
          document.body.removeChild(overlay);
        });

        const messageElement = document.createElement('h2');
        messageElement.textContent = message;

        popup.appendChild(closeBtn);
        popup.appendChild(messageElement);

        document.body.appendChild(overlay);
        document.body.appendChild(popup);
        popup.style.display = 'block';
        overlay.style.display = 'block';
      }



      function updateInfoContainer() {
        let infoHtml = '<p><strong>Últimos pontos batidos:</strong></p>';
        scannedData.forEach(data => {
          infoHtml += `
            <p>Nome: ${data.nome}</p>
            <p>Código: ${data.codigo}</p>
            <p>Hora: ${data.horas}</p>
            <p>Data: ${data.datas}</p>
            <hr>
          `;
        });
        infoContainer.innerHTML = infoHtml;
      }

      window.exportToExcel = function () {
        if (scannedData.length === 0) {
          alert('Nenhum dado para exportar.');
          return;
        }

        const worksheet = XLSX.utils.json_to_sheet(scannedData);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Dados');
        XLSX.writeFile(workbook, 'dados_qr_code.xlsx');
      }

      function isCodeAlreadyReadRecently(newCode) {
        let now = new Date();
        let tenMinutesAgo = new Date(now.getTime() - 10 * 60 * 1000);

        for (let i = 0; i < scannedData.length; i++) {
          if (scannedData[i].codigo === newCode) {
            let codeTime = new Date(`${scannedData[i].datas} ${scannedData[i].horas}`);
            if (codeTime > tenMinutesAgo) {
              return true;
            }
          }
        }

        return false;
      }
    });
  </script>
</body>
</html>

<?php
include 'conexao.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = clean_input($_POST['nome']);
    $codigo = clean_input($_POST['codigo']);
    $horas = clean_input($_POST['horas']);
    $datas = clean_input($_POST['datas']);

    $sql = "INSERT INTO pontos (nome, codigo, horas, datas) VALUES (?, ?, ?, ?)";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("ssss", $nome, $codigo, $horas, $datas);
        if ($stmt->execute()) {
            echo "Dados inseridos com sucesso!";
        } else {
            echo "Erro ao inserir dados: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Erro ao preparar a declaração: " . $conn->error;
    }

    $conn->close();
}

function clean_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>

<style>
    /* Adicione este bloco de estilo */
    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      padding: 20px;
      background-color: #fff;
      border: 1px solid #ccc;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      max-width: 300px;
      text-align: center;
      z-index: 1000;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 999;
    }

    .popup h2 {
      margin-bottom: 10px;
      color: #333;
    }

    .close-btn {
      cursor: pointer;
      color: #666;
      font-weight: bold;
      position: absolute;
      top: 10px;
      right: 10px;
      font-size: 18px;
    }
  </style>