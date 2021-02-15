<?php


namespace Remcodex\Router\Middlewares;


use Nette\Utils\JsonException;
use Psr\Http\Message\ServerRequestInterface;
use React\Promise\PromiseInterface;
use Remcodex\Router\Command;
use Remcodex\Router\Context;
use Remcodex\Router\Payload;
use Remcodex\Router\ResponseFactory;
use function React\Promise\resolve;

class CommandRoutingMiddleware
{
    private array $commandListeners;

    public function __construct(string $commandsDefinitionFile)
    {
        $this->commandListeners = (new Command($commandsDefinitionFile))->getListeners();
    }

    /**
     * @param ServerRequestInterface $request
     * @return PromiseInterface
     * @throws JsonException
     */
    public function __invoke(ServerRequestInterface $request): PromiseInterface
    {
        $payload = Context::getPayload($request);
        $matchedCommand = $this->matchCommand($payload);
        if (false === $matchedCommand) {
            return resolve(ResponseFactory::commandNotFound($payload));
        }

        $listenerObject = new $matchedCommand[0];
        $listerMethod = $matchedCommand[1];

        $response = $listenerObject->$listerMethod($payload);
        if (!$response instanceof PromiseInterface) {
            return resolve($response);
        }

        return $response;
    }


    /**
     * @param Payload $payload
     * @return callable|false
     */
    public function matchCommand(Payload $payload)
    {
        $clientCommand = $payload->getCommand();

        foreach ($this->commandListeners as $command => $listener) {
            if ($command == $clientCommand) {
                return $listener;
            }
        }

        return false;
    }
}