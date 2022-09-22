<?php
/**
 * A simple API that will echo request parameters back to you.
 *
 * @author Zemian Deng <zemiandeng@gmail.com>
 */

if (isset($_GET['testError'])) {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('HTTP/1.1 500 Internal Server Error');
} else {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    $data = $_GET;
    if (count($data) === 0) {
        $data = ['status' => "API is working!", 'timestamp' => date('c')];
    }
    echo json_encode($data);
}
