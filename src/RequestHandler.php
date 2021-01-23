<?php


namespace Remcodex\Router;


use Exception;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use React\Http\Message\Response;
use React\Promise\PromiseInterface;
use Remcodex\Router\Core\Middlewares\Router;
use Throwable;

class RequestHandler
{
    private array $remoteServers;

    public function __construct(array $remoteServers)
    {
        $this->remoteServers = $remoteServers;
    }

    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $response = (new Router(...$this->remoteServers))->__invoke($request);
        return $this->generateProperResponse($response);
    }

    /**
     * @param mixed $response
     * @return mixed|ResponseInterface|Response
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
        } elseif (!$response instanceof Response) {
            //Let's see if object is callable
            if (is_callable($response)) {
                return $this->generateProperResponse($response());
            } //Since object is not callable, let's figure out a way to handle it
            else {
                //if object can be used as string
                switch ($response) {
                    case ($response instanceof Throwable):
                        if (Server::ENV_DEVELOPMENT == Config::environment()) {
                            $response = \Remcodex\Router\Response::success($response);
                        } else {
                            $response = \Remcodex\Router\Response::internalServerError('Server returns an unexpected response, please check server logs');
                            //handleApplicationException($response);
                        }
                        break;
                    case (is_scalar($response) || is_array($response)):
                        $response = \Remcodex\Router\Response::success($response);
                        break;
                    default:
                        $message = "Server returns an unexpected response.";
                        ErrorHandler::handle(new Exception($message));
                        $response = \Remcodex\Router\Response::internalServerError($message);
                        break;
                }
            }
        }

        return $response;
    }
}