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

    public function __invoke(Throwable $exception): void
    {
        if (isset($this->additionalErrorHandler)) {
            $additionalErrorHandler = $this->additionalErrorHandler;
            $additionalErrorHandler($exception);
        }

        echo PHP_EOL . $exception . PHP_EOL . PHP_EOL;
    }
}