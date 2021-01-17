<?php


namespace Remcodex\Router;


use Psr\Http\Message\ResponseInterface;

class Response
{
    /**
     * @param mixed $data
     * @return ResponseInterface
     */
    public static function success($data): ResponseInterface
    {
        return self::json([
            'status' => 200,
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * @param array|object $data
     * @return ResponseInterface
     */
    public static function json($data): ResponseInterface
    {
        return new \React\Http\Message\Response(200, [
            'content-type' => 'application/json',
        ], json_encode($data));
    }

    /**
     * @param mixed $data
     * @param int $statusCode
     * @return ResponseInterface
     */
    public static function error($data, int $statusCode = 500): ResponseInterface
    {
        return self::json([
            'status' => $statusCode,
            'success' => false,
            'data' => $data,
        ]);
    }
}