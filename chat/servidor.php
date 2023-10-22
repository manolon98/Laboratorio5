<?php
// Definir la configuración de la base de datos
$host = "127.0.0.1";
$database = "chat";
$username = "root";
$password = "";

// Función para establecer la conexión PDO
function conectar(){
    global $host, $database, $username, $password;
    try {
        $dsn = "mysql:host=$host;dbname=$database";
        $conexion = new PDO($dsn, $username, $password);
        $conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conexion;
    } catch (PDOException $e) {
        echo "Error de conexión: " . $e->getMessage();
        return null;
    }
}

// Función para ejecutar consultas preparadas
function ejecutar($sql, $params = array()){
    $conexion = conectar();
    if ($conexion) {
        try {
            $stmt = $conexion->prepare($sql);
            $stmt->execute($params);
        } catch (PDOException $e) {
            echo "Error de ejecución: " . $e->getMessage();
        }
    }
}

// Función para consultar con consultas preparadas
function consultar($sql, $params = array()){
    $conexion = conectar();
    if ($conexion) {
        try {
            $stmt = $conexion->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error de consulta: " . $e->getMessage();
        }
    }
}

// Función de retorno de datos AJAX
function AJAX($nombre, $mensaje){
    if (($nombre != "") && ($mensaje != "")) {
        ejecutar("INSERT INTO mensajeria(usuarios, mensajes) VALUES(?, ?)", array($nombre, $mensaje));
    }
    $chat = consultar("CALL mostrar();");
    header("Content-Type: text/plain");
    foreach ($chat as $dato) {
        echo($dato['usuarios'] . ": " . $dato['mensajes'] . "\n\n");
    }
}

// Solo si recibe variables nombre y mensaje sabemos que es una solicitud AJAX
if (isset($_REQUEST["nombre"]) && isset($_REQUEST["mensaje"])) {
    $nombre = $_REQUEST["nombre"];
    $mensaje = $_REQUEST["mensaje"];
    AJAX($nombre, $mensaje);
} else {
    echo("Solo Personal Autorizado");
}
?>
