<?php
require_once 'conexion.php';
require_once 'jwt.php';

/********BLOQUE DE ACCESO DE SEGURIDAD */
$headers = apache_request_headers();
$tmp = $headers['Authorization'];
$jwt = str_replace("Bearer ", "", $tmp);
if (JWT::verify($jwt, Config::SECRET) > 0) {
    header("HTTP/1.1 401 Unauthorized");
    exit;
}

$user = JWT::get_data($jwt, Config::SECRET)['user'];

/*** BLOQUE WEB SERVICE REST */
$metodo = $_SERVER["REQUEST_METHOD"];
switch ($metodo) {
    case 'GET':
        $c = conexion();
        if (isset($_GET['id'])) {
            $s = $c->prepare("SELECT * FROM bulb WHERE id = :id");
            $s->bindValue(":id", $_GET['id']);
        } else {
            $s = $c->prepare("SELECT * FROM bulb");
        }
        $s->execute();
        $r = $s->fetchAll(PDO::FETCH_ASSOC);
        header('Content-Type: application/json');
        echo json_encode($r, JSON_PRETTY_PRINT);
        break;
    case 'POST':
        $json = json_decode(file_get_contents('php://input'), true);
        if (isset($json['location']) && isset($json['name']) && isset($json['status'])) {
            $c = conexion();
            $s = $c->prepare("INSERT INTO bulb (location, name, status) VALUES (:location, :name, :status)");
            $s->bindValue(":location", $json['location']);
            $s->bindValue(":name", $json['name']);
            $s->bindValue(":status", $json['status']);
            $s->execute();
            if ($s->rowCount() > 0) {
                header("HTTP/1.1 201 Created");
                echo json_encode(["add" => "y", "id" => $c->lastInsertId()]);
            } else {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["add" => "n"]);
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Faltan datos"]);
        }
        break;

    case 'PUT':
        $json = json_decode(file_get_contents('php://input'), true);
        if (isset($_GET['id'])) {
            $sql = "UPDATE bulb SET ";
            (isset($json['location'])) ? $sql .= "location = :location, " : null;
            (isset($json['name'])) ? $sql .= "name = :name, " : null;
            (isset($json['status'])) ? $sql .= "status = :status, " : null;
            $sql = rtrim($sql, ", ");
            $sql .= " WHERE id = :id";
            $c = conexion();
            $s = $c->prepare($sql);
            (isset($json['location'])) ? $s->bindValue(":location", $json['location']) : null;
            (isset($json['name'])) ? $s->bindValue(":name", $json['name']) : null;
            (isset($json['status'])) ? $s->bindValue(":status", $json['status']) : null;
            $s->bindValue(":id", $_GET['id']);
            $s->execute();
            if ($s->rowCount() > 0) {
                header("HTTP/1.1 200 OK");
                echo json_encode(["update" => "y"]);
            } else {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["update" => "n"]);
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Faltan datos"]);
        }
        break;
    case 'DELETE':
        $json = json_decode(file_get_contents('php://input'), true);
        if (isset($_GET['id'])) {
            $c = conexion();
            $s = $c->prepare("DELETE FROM bulb WHERE id = :id");
            $s->bindValue(":id", $_GET['id']);
            $s->execute();
            if ($s->rowCount() > 0) {
                header("HTTP/1.1 200 OK");
                echo json_encode(["delete" => "y"]);
            } else {
                header("HTTP/1.1 400 Bad Request");
                echo json_encode(["delete" => "n"]);
            }
        } else {
            header("HTTP/1.1 400 Bad Request");
            echo json_encode(["error" => "Faltan datos"]);
        }
        break;

    default:
        header("HTTP/1.1 400 Bad Request");
        echo "Método no permitido";
        break;
}
