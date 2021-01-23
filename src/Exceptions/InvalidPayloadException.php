<?php


namespace Remcodex\Router\Exceptions;


use Exception;

class InvalidPayloadException extends Exception
{
    /**
     * @param string|null $property A property that is absent in the request
     * @param string|null $message A preferred exception message
     * @throws InvalidPayloadException
     */
    public static function create(?string $property, ?string $message = null): void
    {
        $message ??= "Request {$property} property must be provided";
        throw new InvalidPayloadException($message);
    }
}