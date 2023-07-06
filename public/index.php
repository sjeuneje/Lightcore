<?php declare(strict_types=1);

require_once dirname(__DIR__) . '/vendor/autoload.php';

$request = \Lightcore\Framework\Http\Request::createFromGlobals();

$content = "Hello world";

$response = new Reponse(content: $content, status: 200, headers: []);

$response->send();