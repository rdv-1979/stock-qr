<?php
header("Content-Type: application/json");
include '../bd/conectar.php';

if (!isset($_GET['codigo_qr'])) {
    echo json_encode(["error" => "Código QR no enviado."]);
    exit;
}

$codigo_qr = $_GET['codigo_qr'];

$stmt = $conn->prepare("SELECT * FROM productos WHERE nombre = ?");
$stmt->bind_param("s", $codigo_qr);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    echo json_encode($resultado->fetch_assoc());
} else {
    echo json_encode(["error" => "Producto no encontrado."]);
}

$stmt->close();
$conn->close();

?>



