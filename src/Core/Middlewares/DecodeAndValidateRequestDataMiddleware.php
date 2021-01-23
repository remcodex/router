<?php


namespace Remcodex\Router\Core\Middlewares;


use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Remcodex\Router\Exceptions\InvalidPayloadException;
use Remcodex\Router\Payload;
use Remcodex\Router\Response;
use function React\Promise\resolve;

class DecodeAndValidateRequestDataMiddleware
{
    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        try {
            Payload::init($request);

            //Check guzwrap request data
            if (empty(Payload::guzwrap())) {
                InvalidPayloadException::create('guzwrap request data');
            }

            return $next($request);
        } catch (InvalidPayloadException $e) {
            return resolve(Response::error($e->getMessage()));
        }
    }
}