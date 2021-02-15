<?php


namespace Remcodex\Router\Listeners;


use Psr\Http\Message\ResponseInterface;
use React\Promise\PromiseInterface;
use Remcodex\Router\Client;
use Remcodex\Router\Executions;
use Remcodex\Router\Helper;
use Remcodex\Router\Payload;
use Remcodex\Router\Response;
use Throwable;

class HttpListener
{
    public function request(Payload $payload): PromiseInterface
    {
        $request = $payload->getRequest();
        $client = Client::create($request);
        Helper::stdout("New request({$client->getId()}): {$request->getUri()} {$request->getServerParams()['REMOTE_ADDR']}\n");

        return Executions::add($payload)->then(
            function (ResponseInterface $response) {
                $result = $response->getBody()->getContents();
                return Response::with($result, false);
            },
            function (Throwable $exception) {
                return Response::error((string)$exception->getMessage());
            }
        );
    }
}