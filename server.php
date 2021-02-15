<?php

use Remcodex\Router\RemoteServer;
use Remcodex\Router\Server;

require 'vendor/autoload.php';

$server = Server::listen('0.0.0.0:9000');

//Add remote server
$server->addRemoteServer(
    //1
    RemoteServer::create('localhost:9110')
        ->protocol(RemoteServer::UNSECURE)
        ->path('api/http/request')
        ->geoLocation('africa.nigeria'),
    //2
    RemoteServer::create('localhost:9111')
        ->protocol(RemoteServer::UNSECURE)
        ->path('api/http/request')
        ->geoLocation('asia.indonesia'),
    //3
    RemoteServer::create('localhost:9112')
        ->protocol(RemoteServer::UNSECURE)
        ->path('api/http/request')
        ->geoLocation('europe.england'),
);

$server->command(__DIR__ . '/commands.php');

//Add error handler
$server->onError(function () {
    echo "Error occurred";
});

echo "Server starting at: http://0.0.0.0:9000\n";
$server->start();