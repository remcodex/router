<?php


namespace Remcodex\Router;


use Exception;

class Helper
{
    /**
     * @var resource $stream
     */
    protected static $stream;

    public static function stdout(string $message): void
    {
        $message = "STDOUT: {$message} \n";
        fwrite(self::getStream(), $message);
    }

    /**
     * @return resource
     * @throws Exception
     */
    protected static function getStream()
    {
        if (!isset(self::$stream)) {
            self::$stream = fopen('php://stdout', 'w');
        }

        if (!is_resource(self::$stream)) {
            throw new Exception("Failed to open stream to stdout");
        }

        return self::$stream;
    }
}