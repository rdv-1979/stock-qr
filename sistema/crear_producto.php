<?php
include '../bd/conectar.php';

$mensaje = "";
$qr_guardado = false;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["qr_base64"])) {
    $nombre = $conn->real_escape_string($_POST["nombre"]);
    $marca = $conn->real_escape_string($_POST["marca"]);
    $presentacion = $conn->real_escape_string($_POST["presentacion"]);
    $precio = floatval($_POST["precio"]);
    $fecha_vencimiento = trim($_POST["fecha_vencimiento"]);
    $stock_actual = intval($_POST["stock_actual"]);

    $stmt = $conn->prepare("INSERT INTO productos (nombre, marca, presentacion, precio, fecha_vencimiento, stock_actual) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $nombre, $marca, $presentacion, $precio, $fecha_vencimiento, $stock_actual);

    if ($stmt->execute()) {
       $id = $conn->insert_id;

        // Guardar imagen base64 en carpeta qrcodes
        $base64 = $_POST["qr_base64"];
        $base64 = str_replace('data:image/png;base64,', '', $base64);
        $base64 = str_replace(' ', '+', $base64);
        $data = base64_decode($base64);

        if (!is_dir("qrcodes")) mkdir("qrcodes");
        $filename = "qrcodes/QR_" . $id . ".png";
        file_put_contents($filename, $data);

        $qr_guardado = true;
        $filename = "QR_".$id;
        $conn->query("UPDATE productos SET codigo_qr = '$filename' WHERE id = $id");
        $mensaje = "✅ Producto guardado correctamente. QR generado.";
    } else {
        $mensaje = "❌ Error al guardar el producto: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Crear producto</title>
  <link rel="stylesheet" href="css/estilos.css">
  <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
  <div class="container">

    <nav>
      <a href="crear_producto.php">➕ Crear producto</a>
      <a href="../scanner.html">📷 Escanear producto</a>
      <a href="listar_productos.php">📋 Listar productos</a>
    </nav>

    <h2>➕ Crear nuevo producto</h2>

    <?php if ($mensaje): ?>
      <p class="<?= str_starts_with($mensaje, '✅') ? 'success' : 'error' ?>"><?= $mensaje ?></p>
    <?php endif; ?>

    <form method="POST" onsubmit="prepararQR()" id="formulario">
      <label>Nombre del producto</label>
      <input type="text" name="nombre" id="nombre" required>

      <label>Marca</label>
      <input type="text" name="marca" id="marca">

      <label>Presentación</label>
      <input type="text" name="presentacion" id="presentacion">

      <label>Precio</label>
      <input type="number" step="0.01" name="precio" id="precio" required>

      <label>Fecha de vencimiento</label>
      <input type="date" name="fecha_vencimiento" id="fecha_vencimiento">

      <label>Stock actual</label>
      <input type="number" name="stock_actual" id="stock_actual">

      <!-- Campo oculto para imagen base64 -->
      <input type="hidden" name="qr_base64" id="qr_base64">

      <br><br>
      <button type="submit">💾 Guardar producto</button>
    </form>

    <div style="text-align:center;margin-top:20px;">
      <div id="previewQR"></div>
    </div>

  </div>

  <script>
    function prepararQR() {
      event.preventDefault(); // Evita envío inmediato

      const nombre = document.getElementById("nombre").value;
      const marca = document.getElementById("marca").value;
      const presentacion = document.getElementById("presentacion").value;

      //const contenidoQR = `${nombre} - ${marca} - ${presentacion}`;
      const contenidoQR = `${nombre}`;

      const tempDiv = document.createElement("div");
      new QRCode(tempDiv, {
        text: contenidoQR,
        width: 200,
        height: 200
      });

      setTimeout(() => {
        const img = tempDiv.querySelector("img");
        if (img) {
          const base64 = img.src;
          document.getElementById("qr_base64").value = base64;
          document.getElementById("previewQR").innerHTML = "<p>Previsualización del QR:</p>" + img.outerHTML;
          document.getElementById("formulario").submit(); // Enviar el formulario
        }
      }, 500);
    }
  </script>
</body>
</html>
