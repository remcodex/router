<?php


namespace Remcodex\Router\Core\Middlewares;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Remcodex\Router\Core\AvailableServers;
use Remcodex\Router\Core\Client;
use Remcodex\Router\Core\Executions;
use Remcodex\Router\RemoteServer;
use Remcodex\Router\Response;
use Throwable;

class Router
{
    public function __construct(RemoteServer ...$remoteServers)
    {
        AvailableServers::add(...$remoteServers);
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $client = Client::create($request);
        echo "New request({$client->getId()}): {$request->getUri()} {$request->getServerParams()['REMOTE_ADDR']}\n";

        return Executions::add($client)->then(
            function (ResponseInterface $response) {
                return Response::success($response->getBody()->getContents());
            },
            function (Throwable $exception) {
                return Response::error((string)$exception);
            }
        );
    }
}