<?php
/*
 * A API to provide CRUD operations to a database named 'crud.db'.
 *
 * Example of Usages:
 *   http://localhost:3000/crud.php/columns
 *   http://localhost:3000/crud.php/records/options
 *   http://localhost:3000/crud.php/records/locales
 *   http://localhost:3000/crud.php/records/locales?page=2,7
 *   http://localhost:3000/crud.php/records/locales?filter=language,eq,English
 *   http://localhost:3000/crud.php/records/locales?filter=region,cs,Island
 *
 * See more on https://github.com/mevdschee/php-crud-api
 *
 * @author Zemian Deng <zemiandeng@gmail.com>
 */

require_once 'api.include.php';

use Tqdev\PhpCrudApi\Api;
use Tqdev\PhpCrudApi\Config;
use Tqdev\PhpCrudApi\RequestFactory;
use Tqdev\PhpCrudApi\ResponseUtils;

$config = new Config([
    'debug' => true,
    'driver' => 'sqlite',
    'address' => 'crud.db',
    'database' => 'crud.db',
    'username' => '',
    'password' => '',
    'controllers' => 'records,geojson,openapi,status,columns',
]);
$request = RequestFactory::fromGlobals();
$api = new Api($config);
$response = $api->handle($request);
ResponseUtils::output($response);
