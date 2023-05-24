<?php
require_once 'conexion.php';
require_once 'jwt.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["user"]) && isset($_GET["pass"])) {
        $c = conexion();
        $s = $c->prepare("SELECT * FROM users WHERE user = :user AND pass = :pass");
        $s->bindValue(":user", $_GET["user"]);
        $s->bindValue(":pass", $_GET["pass"]);
        $s->execute();
        $r = $s->fetch(PDO::FETCH_ASSOC);
        if ($r) {
            $t = JWT::create(["user" => $_GET["user"]], Config::SECRET);
            $result = ["login" => "y", "token" => $t];
        } else {
            $result = ["login" => "n", "token" => "Error"];
        }
        header("HTTP/1.1 200 OK");
        echo json_encode($result);
    } else {
        header("HTTP/1.1 400 Bad Request");
        echo "Faltan datos";
    }
}

