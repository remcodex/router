<?php


namespace Remcodex\Router;


use Throwable;

class ErrorHandler
{
    /**
     * @var callable $additionalErrorHandler
     */
    protected $additionalErrorHandler;

    public function __construct(?callable $additionalErrorHandler)
    {
        $this->additionalErrorHandler = $additionalErrorHandler;
    }

    public static function create(?callable $callback = null): ErrorHandler
    {
        return new ErrorHandler($callback);
    }

    public static function handle(Throwable $exception): void
    {

    }

    public function __invoke(Throwable $exception): void
    {
        $f = fopen('php://stdout', 'w');
        fwrite($f, $exception->__toString());
        echo($exception->getMessage());
    }
}