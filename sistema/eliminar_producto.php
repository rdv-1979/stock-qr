<?php
include '../bd/conectar.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Obtener QR para eliminar imagen
    $query = $conn->prepare("SELECT codigo_qr FROM productos WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $res = $query->get_result();

    if ($res->num_rows > 0) {
        $row = $res->fetch_assoc();
        $qr_path = "../qrcodes/" . $row['codigo_qr'] . ".png";
        if (file_exists($qr_path)) unlink($qr_path);
    }

    // Eliminar producto
    $stmt = $conn->prepare("DELETE FROM productos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

header("Location: listar_productos.php");
exit;
