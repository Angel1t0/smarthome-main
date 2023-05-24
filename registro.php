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
        header("HTTP/1.1 200 OK");
        echo json_encode($r);
        break;
    case 'POST':
        if (isset($_POST['location']) && isset($_POST['name']) && isset($_POST['status'])) {
            $c = conexion();
            $s = $c->prepare("INSERT INTO bulb (location, name, status) VALUES (:location, :name, :status)");
            $s->bindValue(":location", $_POST['location']);
            $s->bindValue(":name", $_POST['name']);
            $s->bindValue(":status", $_POST['status']);
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
            echo "Faltan datos";
        }
        break;
    case 'PUT':
        if (isset($_GET['id'])) {
            $sql = "UPDATE bulb SET ";
            (isset($_GET['location'])) ? $sql .= "location = :location, " : null;
            (isset($_GET['name'])) ? $sql .= "name = :name, " : null;
            (isset($_GET['status'])) ? $sql .= "status = :status, " : null;
            $sql = rtrim($sql, ", ");
            $sql .= " WHERE id = :id";
            $c = conexion();
            $s = $c->prepare($sql);
            (isset($_GET['location'])) ? $s->bindValue(":location", $_GET['location']) : null;
            (isset($_GET['name'])) ? $s->bindValue(":name", $_GET['name']) : null;
            (isset($_GET['status'])) ? $s->bindValue(":status", $_GET['status']) : null;
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
            echo "Faltan datos";
        }
        break;
    case 'DELETE':
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
            echo "Faltan datos";
        }
        break;
    default:
        header("HTTP/1.1 400 Bad Request");
        echo "Método no permitido";
        break;
}
