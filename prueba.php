<?php
// Conexión a la base de datos
$servidor = "192.168.170.210";
$usuario = "system";
$contrasena = "987654321";
$base_datos = "isaac";

$conexion = new mysqli($servidor, $usuario, $contrasena, $base_datos);

// Verificar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Subir archivo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo"])) {
    // Obtener los datos del formulario
    $nombre_archivo = $_FILES["archivo"]["name"];
    $archivo_temp = $_FILES["archivo"]["tmp_name"];
    $archivo_binario = file_get_contents($archivo_temp);
    $comentario = $_POST["comentario"] ?? "";
    $id_cargo_des = $_POST["id_cargo_des"];
    $id_nombre_des = $_POST["id_nombre_des"];
    $id_nombre_env = $_POST["id_nombre_env"];
    $cuenta_regre = $_POST["cuenta_regre"];

    // Validar que los campos obligatorios no estén vacíos
    if (empty($nombre_archivo) || empty($archivo_binario) || empty($id_cargo_des) || empty($id_nombre_des) || empty($id_nombre_env) || empty($cuenta_regre)) {
        echo "<script>alert('Todos los campos son obligatorios excepto el comentario.');</script>";
    } else {
        // Insertar datos en la base de datos
        $stmt = $conexion->prepare("INSERT INTO archivos (nombre, archivo, comentario, id_cargo_des, id_nombre_des, id_nombre_env, cuenta_regre) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssiis", $nombre_archivo, $archivo_binario, $comentario, $id_cargo_des, $id_nombre_des, $id_nombre_env, $cuenta_regre);
        $stmt->send_long_data(1, $archivo_binario);

        if ($stmt->execute()) {
            echo "<script>alert('Archivo subido correctamente.');</script>";
        } else {
            echo "<script>alert('Error al subir el archivo.');</script>";
        }

        $stmt->close();
    }
}

// Descargar archivo
if (isset($_GET["descargar"])) {
    $id = $_GET["descargar"];
    $stmt = $conexion->prepare("SELECT nombre, archivo FROM archivos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($nombre, $archivo);
    $stmt->fetch();
    $stmt->close();

    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=\"$nombre\"");
    echo $archivo;
    exit;
}

// Obtener archivos
$resultado = $conexion->query("SELECT id, nombre, comentario, fecha_envio FROM archivos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestor de Archivos</title>
    <style>
        body { font-family: Arial, sans-serif; text-align: center; margin: 20px; }
        form { margin-bottom: 20px; }
        ul { list-style: none; padding: 0; }
        li { margin: 10px 0; }
        a { text-decoration: none; color: blue; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h2>Subir Archivo</h2>
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="archivo" required>
        <input type="text" name="comentario" placeholder="Comentario (opcional)">
        <input type="number" name="id_cargo_des" placeholder="ID Cargo Destino" required>
        <input type="number" name="id_nombre_des" placeholder="ID Nombre Destino" required>
        <input type="number" name="id_nombre_env" placeholder="ID Nombre Envío" required>
        <input type="datetime-local" name="cuenta_regre" placeholder="Fecha Límite" required>
        <button type="submit">Subir Archivo</button>
    </form>

    <h2>Archivos Disponibles</h2>
    <ul>
        <?php while ($fila = $resultado->fetch_assoc()): ?>
            <li>
                <strong><?= htmlspecialchars($fila['nombre']) ?></strong><br>
                <small>Comentario: <?= htmlspecialchars($fila['comentario'] ?? "Sin comentario") ?></small><br>
                <small>Fecha de Envío: <?= $fila['fecha_envio'] ?></small><br>
                <a href="?descargar=<?= $fila['id'] ?>">Descargar</a>
            </li>
        <?php endwhile; ?>
    </ul>
</body>
</html>

<?php $conexion->close(); ?>