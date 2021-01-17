<?php


namespace Remcodex\Router\Core\Middlewares;


use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Remcodex\Router\Response;
use function React\Promise\resolve;

class ValidateRequestData
{
    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        if (empty($request->getParsedBody()['data'])) {
            return resolve(Response::error('Invalid request data'));
        }

        return $next($request);
    }
}