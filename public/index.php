<?php declare(strict_types=1);

use Lightcore\Framework\Http\Request;
use Lightcore\Framework\Http\Response;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$request = Request::createFromGlobals();

$content = '<h1>Hello world</h1>';

$response = new Response(content: $content, status: 200, headers: []);

$response->send();