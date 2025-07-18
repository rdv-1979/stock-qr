<?php
include '../bd/conectar.php';

$resultado = $conn->query("SELECT * FROM productos ORDER BY creado_en DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Listado de Productos</title>
  <style>
    body { font-family: Arial; padding: 20px; max-width: 1000px; margin: auto; }
    nav { background: #333; padding: 10px; margin-bottom: 20px; }
    nav a { color: white; margin-right: 20px; text-decoration: none; }
    table { width: 100%; border-collapse: collapse; margin-top: 10px; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    th { background-color: #f2f2f2; }
    .acciones a { margin: 0 5px; text-decoration: none; }
    .acciones a:hover { text-decoration: underline; }
    .btn { padding: 4px 10px; border-radius: 4px; border: none; cursor: pointer; }
    .btn-editar { background-color: #4CAF50; color: white; }
    .btn-eliminar { background-color: #f44336; color: white; }
  </style>
  <link rel="stylesheet" href="../css/estilos.css">
</head>
<body>
<div class="container">
<nav style="background: #333; padding: 10px;">
  <a href="crear_producto.php" style="color: white; margin-right: 20px; text-decoration: none;">➕ Crear producto</a>
  <a href="../scanner.html" style="color: white; text-decoration: none;">📷 Escanear producto</a>
  <a href="listar_productos.php" style="color: white; text-decoration: none;">📋 Listar productos</a>
</nav>

<h2>📋 Listado de Productos</h2>

<table>
  <thead>
    <tr>
      <th>ID</th>
      <th>Nombre</th>
      <th>Marca</th>
      <th>Presentación</th>
      <th>Precio</th>
      <th>Stock</th>
      <th>Vencimiento</th>
      <th>QR</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $resultado->fetch_assoc()): ?>
    <tr>
      <td><?= $row['id'] ?></td>
      <td><?= $row['nombre'] ?></td>
      <td><?= $row['marca'] ?></td>
      <td><?= $row['presentacion']; ?></td>
      <td>$<?= number_format($row['precio'], 2) ?></td>
      <td><?= $row['stock_actual'] ?></td>
      <td><?= $row['fecha_vencimiento'] ?></td>
      <td><img src="qrcodes/<?= $row['codigo_qr'] ?>.png" alt="QR" width="50"></td>
      <td class="acciones">
        <a class="btn btn-editar" href="editar_producto.php?id=<?= $row['id'] ?>">✏️</a>
        <a class="btn btn-eliminar" href="eliminar_producto.php?id=<?= $row['id'] ?>" onclick="return confirm('¿Eliminar este producto?')">🗑️</a>
      </td>
    </tr>
    <?php endwhile; ?>
  </tbody>
</table>
</div>
</body>
</html>
