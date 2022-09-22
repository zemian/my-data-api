<?php
/**
 * A simple API that will echo request parameters back to you.
 *
 * Example usage:
 *   index.php                    # Returns a status and timestamp message
 *   index.php?foo=123&bar=test   # Echo out what's giving query params as JSON response
 *   index.php?test_error=true    # Sends a 500 error for testing
 *
 * @author Zemian Deng <zemiandeng@gmail.com>
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

if (boolval($_GET['test_error'] ?? false)) {
    header('HTTP/1.1 500 Internal Server Error');
} else {
    $data = $_GET;
    if (count($data) === 0) {
        $data = ['status' => "API is working!", 'timestamp' => date('c')];
    }
    echo json_encode($data);
}
