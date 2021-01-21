# RCE Router
Remote code execution router/load balancer - Checks which [RCE Server](https://github.com/remcodex/server) has the lowest connections and sends the client request to it.


## Installation
```bash
composer require remcodex/router
```

## Usage
```php
use Remcodex\Router\RemoteServer;
use Remcodex\Router\Server;

require 'vendor/autoload.php';

$serverUri = '0.0.0.0:9000';
$server = Server::listen($serverUri);

//Add remote server
$server->addRemoteServer(
    //1
    RemoteServer::create('localhost:9110')
        ->protocol(RemoteServer::UNSECURE)
        ->path('api/http/request'),
    //2
    RemoteServer::create('localhost:9111')
        ->protocol(RemoteServer::UNSECURE)
        ->path('api/http/request'),
    //3
    RemoteServer::create('localhost:9112')
        ->protocol(RemoteServer::UNSECURE)
        ->path('api/http/request'),
);

//Add error handler
$server->onError(function (Throwable $exception) {
    echo "Error occurred";
    echo $exception;
});

echo "Server starting at: http://{$serverUri}\n";
$server->start();
```

Built with [ReactPHP](https://reactphp.org)