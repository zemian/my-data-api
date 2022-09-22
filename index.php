<?php
/**
 * A simple API that will echo request parameters back to you.
 *
 * @author Zemian Deng <zemiandeng@gmail.com>
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (isset($_GET['testError'])) {
    header('HTTP/1.1 500 Internal Server Error');
} else {
    $data = $_GET;
    if (count($data) === 0) {
        $data = ['status' => "API is working!", 'timestamp' => date('c')];
    }
    echo json_encode($data);
}
