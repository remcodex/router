<?php


namespace Remcodex\Router;


use Psr\Http\Message\ServerRequestInterface;
use SplObjectStorage;

class Context
{
    private static SplObjectStorage $payloads;

    public static function setPayload(ServerRequestInterface $request, Payload $payload): void
    {
        self::getPayloads()->attach($request, $payload);
    }

    private static function getPayloads(): SplObjectStorage
    {
        if (!isset(self::$payloads)) {
            self::$payloads = new SplObjectStorage();
        }

        return self::$payloads;
    }

    public static function getPayload(ServerRequestInterface $request): Payload
    {
        return self::getPayloads()->offsetGet($request);
    }
}