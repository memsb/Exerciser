<?php

// load Tonic library
require_once '/usr/share/php5/tonic/lib/tonic.php';
require_once 'includes.php';
require_once 'config.php';


// handle request
$config = array('baseUri' => URI);
$request = new Request($config);

try {
    $resource = $request->loadResource();
    $response = $resource->exec($request);
} catch (ResponseException $e) {
	
    switch ($e->getCode()) {
    case Response::UNAUTHORIZED:
        $response = $e->response($request);
        $response->addHeader('WWW-Authenticate', 'Basic realm="Exerciser"');
        break;
    default:
        $response = $e->response($request);
    }
}
$response->output();

