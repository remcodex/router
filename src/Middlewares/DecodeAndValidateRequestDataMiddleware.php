<?php


namespace Remcodex\Router\Middlewares;


use Nette\Utils\JsonException;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Remcodex\Router\Context;
use Remcodex\Router\Exceptions\InvalidPayloadException;
use Remcodex\Router\Payload;
use Remcodex\Router\Response;
use function React\Promise\resolve;

class DecodeAndValidateRequestDataMiddleware
{
    /**
     * @param ServerRequestInterface $request
     * @param callable $next
     * @return PromiseInterface
     * @throws JsonException
     */
    public function __invoke(ServerRequestInterface $request, callable $next): PromiseInterface
    {
        try {
            $payload = new Payload($request);
            Context::setPayload($request, $payload);

            //Check guzwrap request data
            if (empty($payload->getGuzwrap())) {
                InvalidPayloadException::create('guzwrap request data');
            }

            return $next($request);
        } catch (InvalidPayloadException $e) {
            return resolve(Response::error($e->getMessage()));
        }
    }
}