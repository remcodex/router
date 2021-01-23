<?php


namespace Remcodex\Router;


use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @param mixed $data
     * @return ResponseInterface
     */
    public static function success($data): ResponseInterface
    {

        return self::respond(200, true, $data);
    }

    /**
     * @param int $statusCode
     * @param bool $success
     * @param mixed $data
     * @return ResponseInterface
     * @throws JsonException
     */
    public static function respond(int $statusCode, bool $success, $data): ResponseInterface
    {
        return self::json([
            'responder' => 'rce.router',
            'status' => $statusCode,
            'success' => $success,
            'data' => $data,
        ]);
    }

    /**
     * @param array|object $data
     * @param bool $shouldEncode
     * @return ResponseInterface
     * @throws JsonException
     */
    public static function json($data, bool $shouldEncode = true): ResponseInterface
    {
        if ($shouldEncode) {
            $data = Json::encode($data);
        }

        return new \React\Http\Message\Response(200, [
            'content-type' => 'application/json',
        ], $data);
    }

    /**
     * This method send response with data as it is, no success, status or data attributes will added
     * @param mixed $data
     * @param bool $shouldEncode
     * @return ResponseInterface
     * @throws JsonException
     */
    public static function with($data, bool $shouldEncode = true): ResponseInterface
    {
        return self::json($data, $shouldEncode);
    }

    public static function internalServerError(string $message): ResponseInterface
    {
        return self::error($message);
    }

    /**
     * @param mixed $data
     * @param int $statusCode
     * @return ResponseInterface
     */
    public static function error($data, int $statusCode = 500): ResponseInterface
    {
        return self::respond($statusCode, false, $data);
    }
}