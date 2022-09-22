<?php
/*
 * This script provide a simple REST API service to return a list of locale objects.
 * It also support simple pagination with offset and limit.
 *
 * NOTE: You need to enable "PHP Intl" extension in order to able to get these Locales
 * data.
 *
 * This script has been tested with PH 7.4.
 *
 * Example usage:
 *   php -S localhost:3000
 *
 *   http://localhost:3000/locales.php
 *   http://localhost:3000/locales.php?offset=35&limit=35
 *   http://localhost:3000/locales.php?limit=9999
 *   http://localhost:3000/locales.php?code=en_US
 *   http://localhost:3000/locales.php?language=English
 *   http://localhost:3000/locales.php?script=Latin
 *   http://localhost:3000/locales.php?random
 *   http://localhost:3000/locales.php?random&totalResult=100&limit=25
 *   http://localhost:3000/locales.php?sortBy=language:descending
 *   http://localhost:3000/locales.php?sortBy=language:ascending
 *
 * Source: https://gist.github.com/zemian/8a04fa2d11542654e5262f46f341c1ef
 * Author: Zemian Deng <zemiandneg@gmail.com>
 */

function get_locales() {
    $locale_codes = ResourceBundle::getLocales('');
    $locales = [];
    foreach ($locale_codes as $code) {
        $locales[] = array(
            "code" => $code,
            "language" => Locale::getDisplayLanguage($code),
            "script" => Locale::getDisplayScript($code),
            "region" => Locale::getDisplayRegion($code)
        );
    }

    if (isset($_GET['code'])) {
        $locales = array_filter($locales, function ($e) {
           return strstr($e['code'], $_GET['code']);
        });
    } else if (isset($_GET['language'])) {
        $locales = array_filter($locales, function ($e) {
            return strstr($e['language'], $_GET['language']);
        });
    } else if (isset($_GET['region'])) {
            $locales = array_filter($locales, function ($e) {
             return strstr($e['region'], $_GET['region']);
            });
    } else if (isset($_GET['script'])) {
        $locales = array_filter($locales, function ($e) {
         return strstr($e['script'], $_GET['script']);
        });
    }

    if (isset($_GET['random'])) {
        shuffle($locales);
    } else if (isset($_GET['sortBy'])) {
        [$field, $direction] = explode(':', $_GET['sortBy']);
        usort($locales, function($a, $b) use ($field, $direction) {
            if ($direction === 'ascending') {
                return strcmp($a[$field], $b[$field]);
            }
            return strcmp($b[$field], $a[$field]);
        });
    }

    if (isset($_GET['totalResult'])) {
        // Chop off data to simulate less data than it provide
        $locales = array_slice($locales, 0, $_GET['totalResult']);
    }

    $total_count = count($locales);
    $offset = intval($_GET['offset'] ?? 0);
    $limit = intval($_GET['limit'] ?? 20);
    $items = array_slice($locales, $offset, $limit);
    $has_more = $offset + count($items) < $total_count;
    $data = array(
        "hasMore" => $has_more,
        "offset" => $offset,
        "limit" => $limit,
        "items" => $items,
        "totalResult" => $total_count,
        "timestamp" => date('c'),
        "source" => "PHP " . phpversion()
    );

    return $data;
}

$allow_methods = "OPTIONS, GET";
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: *');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: ' . $allow_methods);

// Main script request processing starts there
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
	header("HTTP/1.1 204 NO CONTENT");
	header('Allow: ' . $allow_methods);
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	$data = get_locales();
	echo json_encode($data);
} else {
	header("HTTP/1.1 405 Method Not Allowed");
	header('Allow: ' . $allow_methods);
	echo json_encode(["error" => "Method Not Allowed."]);
}
