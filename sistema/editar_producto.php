<?php
include '../bd/conectar.php';

$mensaje = "";
$producto = null;

if (!isset($_GET['id'])) {
    die("❌ ID de producto no especificado.");
}

$id = intval($_GET['id']);

// Obtener el producto actual
$stmt = $conn->prepare("SELECT * FROM productos WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    die("❌ Producto no encontrado.");
}

$producto = $res->fetch_assoc();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $conn->real_escape_string($_POST["nombre"]);
    $marca = $conn->real_escape_string($_POST["marca"]);
    $presentacion = $conn->real_escape_string($_POST["presentacion"]);
    $precio = floatval($_POST["precio"]);
    $stock_actual = intval($_POST["stock_actual"]);
    $fecha_vencimiento = trim($_POST["fecha_vencimiento"]);

    // Si la fecha está vacía, usamos NULL
    if ($fecha_vencimiento === "") {
        $fecha_vencimiento_sql = "NULL";
    } else {
        $fecha_vencimiento_sql = "'" . $conn->real_escape_string($fecha_vencimiento) . "'";
    }

    // Armamos la consulta manualmente
    $sql = "UPDATE productos SET 
                nombre='$nombre',
                marca='$marca',
                presentacion='$presentacion',
                precio=$precio,
                fecha_vencimiento=$fecha_vencimiento_sql,
                stock_actual=$stock_actual
            WHERE id=$id";

    if ($conn->query($sql)) {
        $mensaje = "✅ Producto actualizado correctamente.";
        // Recargamos los datos actualizados
        $res = $conn->query("SELECT * FROM productos WHERE id = $id");
        $producto = $res->fetch_assoc();
    } else {
        $mensaje = "❌ Error al actualizar: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Editar Producto</title>
  <style>
    body { font-family: Arial; max-width: 600px; margin: auto; padding: 20px; }
    nav { background: #333; padding: 10px; margin-bottom: 20px; }
    nav a { color: white; margin-right: 20px; text-decoration: none; }
    label { display: block; margin-top: 10px; }
    input, button { padding: 8px; width: 100%; margin-top: 5px; }
    .success { color: green; }
    .error { color: red; }
  </style>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<div class="container">
<nav>
  <a href="crear_producto.php">➕ Crear producto</a>
  <a href="../scanner.html">📷 Escanear producto</a>
  <a href="listar_productos.php">📋 Listar productos</a>
</nav>

<h2>✏️ Editar Producto</h2>

<?php if ($mensaje): ?>
  <p class="<?= str_starts_with($mensaje, '✅') ? 'success' : 'error' ?>"><?= $mensaje ?></p>
<?php endif; ?>

<?php
$fecha_valida = ($producto['fecha_vencimiento'] !== "0000-00-00" && !empty($producto['fecha_vencimiento'])) ? $producto['fecha_vencimiento'] : "";
?>

<form method="POST">
  <label>Nombre del producto</label>
  <input type="text" name="nombre" value="<?= htmlspecialchars($producto['nombre']) ?>" readonly>

  <label>Marca</label>
  <input type="text" name="marca" value="<?= htmlspecialchars($producto['marca']) ?>">

  <label>Presentación</label>
  <input type="text" name="presentacion" value="<?= htmlspecialchars($producto['presentacion']) ?>">

  <label>Precio</label>
  <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($producto['precio']) ?>" required>

  <label>Fecha de vencimiento</label>
  <input type="date" name="fecha_vencimiento" value="<?= $fecha_valida ?>">

  <label>Stock actual</label>
  <input type="number" name="stock_actual" value="<?= htmlspecialchars($producto['stock_actual']) ?>">

  <br><br>
  <button type="submit">💾 Guardar Cambios</button>
</form>
</div>
</body>
</html>
