<?php
/*
 * A simple SQL query API to crud db.
 *
 * @author Zemian Deng <zemiandeng@gmail.com>
 */
function get_data() {
    $sql = $_GET['sql'] ?? $_POST['sql'] ?? '';
    if (!$sql) {
        return ["error" => "Missing sql parameter."];
    }
    $pdo = new PDO('sqlite:crud.db');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    try {
        $stmt = $pdo->query($sql);
        if ($stmt) {
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        } else {
            $data = ["error" => $pdo->errorInfo()];
        }
    } catch (Exception | Error $e) {
        $data = ["error" => $e];
    }
    return $data;
}

$allow_methods = "OPTIONS, GET, POST";
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: ' . $allow_methods);

// Main script request processing starts there
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 204 NO CONTENT");
    header('Allow: ' . $allow_methods);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' || $_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = get_data();
    echo json_encode($data);
} else {
    header("HTTP/1.1 405 Method Not Allowed");
    header('Allow: ' . $allow_methods);
    echo json_encode(["error" => "Method Not Allowed."]);
}
