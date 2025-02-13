<?php
// Datos de conexión
$hostname = 'phi2b@mysql.service1.divers-about.com';
$port = 3200;
$username = 'uDcd@sysno.io.11';
$password = 'tu_contraseña'; // Reemplaza con tu contraseña
$database = 'nombre_de_tu_base_de_datos'; // Reemplaza con el nombre de tu base de datos

// Crear conexión
$conn = new mysqli($hostname, $username, $password, $database, $port);

// Verificar conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}
echo "Conexión exitosa!";

// Insertar un registro en la tabla CARGO
$cargo = "Gerente"; // Valor para la columna "cargo"
$sql = "INSERT INTO CARGO (cargo) VALUES ('$cargo')";

if ($conn->query($sql) === TRUE) {
    echo "Nuevo registro insertado correctamente.";
} else {
    echo "Error al insertar registro: " . $conn->error;
}

// Consultar todos los registros de la tabla CARGO
$sql = "SELECT id, cargo FROM CARGO";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Mostrar los datos de cada fila
    while($row = $result->fetch_assoc()) {
        echo "ID: " . $row["id"]. " - Cargo: " . $row["cargo"]. "<br>";
    }
} else {
    echo "0 resultados";
}

// Cerrar conexión
$conn->close();
?>