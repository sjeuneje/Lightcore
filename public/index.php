<?php declare(strict_types=1);

use Lightcore\Framework\Http\Kernel;
use Lightcore\Framework\Http\Request;
use Lightcore\Framework\Http\Response;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$request = Request::createFromGlobals();

$kernel = new Kernel($request);

$response = $kernel->handle($request);

$response->send();