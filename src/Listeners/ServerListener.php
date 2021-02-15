<?php


namespace Remcodex\Router\Listeners;


use React\Promise\PromiseInterface;
use Remcodex\Router\AvailableServers;
use Remcodex\Router\Payload;
use Remcodex\Router\Response;
use function React\Promise\resolve;

class ServerListener
{
    public function list(Payload $payload): PromiseInterface
    {
        return resolve(Response::success(AvailableServers::getServers()));
    }
}