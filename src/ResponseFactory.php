<?php


namespace Remcodex\Router;


use Nette\Utils\JsonException;
use Psr\Http\Message\ResponseInterface;

class ResponseFactory
{
    /**
     * Generate command not found response
     * @param Payload $payload
     * @return ResponseInterface
     * @throws JsonException
     */
    public static function commandNotFound(Payload $payload): ResponseInterface
    {
        return Response::error([
            'code' => 'cmd.404',
            'message' => "Command \"{$payload->getCommand()}\" does not exists",
        ], 404);
    }
}