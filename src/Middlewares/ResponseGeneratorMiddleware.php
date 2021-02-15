<?php


namespace Remcodex\Router\Middlewares;


use Closure;
use Exception;
use Nette\Utils\JsonException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response as ReactResponse;
use React\Promise\PromiseInterface;
use Remcodex\Router\AvailableServers;
use Remcodex\Router\Config;
use Remcodex\Router\ErrorHandler;
use Remcodex\Router\Response;
use Remcodex\Router\Server;
use Throwable;

class ResponseGeneratorMiddleware
{
    public function __construct(array $remoteServers)
    {
        AvailableServers::add(...$remoteServers);
    }

    /**
     * @param ServerRequestInterface $request
     * @param Closure $next
     * @return PromiseInterface
     * @throws JsonException
     */
    public function __invoke(ServerRequestInterface $request, Closure $next): PromiseInterface
    {
        $response = $next($request); // Execute all middlewares and handle final response
        return $this->generateProperResponse($response);
    }

    /**
     * @param mixed $response
     * @return mixed|ResponseInterface|ReactResponse
     * @throws JsonException
     */
    public function generateProperResponse($response)
    {
        if ($response instanceof PromiseInterface) {
            return $response->then(
                function ($returnedResponse) {
                    return $this->generateProperResponse($returnedResponse);
                },
                function ($returnedResponse) {
                    return $this->generateProperResponse($returnedResponse);
                });
        } elseif (!$response instanceof ReactResponse) {
            //Let's see if object is callable
            if (is_callable($response)) {
                return $this->generateProperResponse($response());
            } //Since object is not callable, let's figure out a way to handle it
            else {
                //if object can be used as string
                switch ($response) {
                    case ($response instanceof Throwable):
                        if (Server::ENV_DEVELOPMENT == Config::environment()) {
                            $response = Response::success($response);
                        } else {
                            $message = 'Server returns an unexpected response, please check server logs';
                            $response = Response::internalServerError($message);
                            ErrorHandler::handle(new Exception($message));
                        }
                        break;
                    case (is_scalar($response) || is_array($response)):
                        $response = Response::success($response);
                        break;
                    default:
                        $message = "Server returns an unexpected response.";
                        ErrorHandler::handle(new Exception($message));
                        $response = Response::internalServerError($message);
                        break;
                }
            }
        }

        return $response;
    }
}