<?php declare(strict_types=1);

use Lightcore\Framework\Database\DB;
use Lightcore\Framework\Http\Kernel;
use Lightcore\Framework\Http\Request;

define('BASE_PATH', dirname(__DIR__));

require_once BASE_PATH . '/vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Launch all the resources needed
|--------------------------------------------------------------------------
| 
| app.php will load all the resources the application need.
|
*/
require_once BASE_PATH . '/bootstrap/app.php';

$request = Request::createFromGlobals();

$kernel = new Kernel($request);

$response = $kernel->handle($request);

$response->send();