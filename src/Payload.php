<?php


namespace Remcodex\Router;


use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Http\Message\ServerRequestInterface;
use Remcodex\Router\Exceptions\InvalidPayloadException;

class Payload
{
    private static array $parsedBody;
    private static array $guzwrapRequestData;
    private static string $command;
    private static int $time;


    /**
     * @param ServerRequestInterface $request
     * @throws InvalidPayloadException|JsonException
     */
    public static function init(ServerRequestInterface $request): void
    {
        self::$parsedBody = (array)$request->getParsedBody();
        self::$guzwrapRequestData = Json::decode(self::$parsedBody['guzwrap'], Json::FORCE_ARRAY) ?? [];

        //Check request command
        if (!isset(self::$parsedBody['command'])) {
            InvalidPayloadException::create('command');
        }

        //Check request time
        if (!isset(self::$parsedBody['time'])) {
            InvalidPayloadException::create('time');
        }

        self::$command = self::$parsedBody['command'];
        self::$time = self::$parsedBody['time'];
    }

    public static function guzwrap(): array
    {
        return self::$guzwrapRequestData;
    }

    public static function command(): string
    {
        return self::$command;
    }

    public static function time(): int
    {
        return self::$time;
    }

    public static function parsedBody(): array
    {
        return self::$parsedBody;
    }
}