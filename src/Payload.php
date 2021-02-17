<?php


namespace Remcodex\Router;


use Nette\Utils\Json;
use Nette\Utils\JsonException;
use Psr\Http\Message\ServerRequestInterface;
use Remcodex\Router\Exceptions\InvalidPayloadException;

class Payload
{
    private ServerRequestInterface $request;
    private array $parsedBody;
    private array $guzwrapRequestData;


    /**
     * @param ServerRequestInterface $request
     * @throws InvalidPayloadException|JsonException
     */
    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
        $this->parsedBody = (array)$request->getParsedBody();

        //Check request command
        if (!isset($this->parsedBody['command'])) {
            InvalidPayloadException::create('command');
        }

        if (isset($this->parsedBody['guzwrap'])) {
            $this->guzwrapRequestData = Json::decode($this->parsedBody['guzwrap'], Json::FORCE_ARRAY) ?? [];
        }

        //Check request time
        if (!isset($this->parsedBody['time'])) {
            InvalidPayloadException::create('time');
        }
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getGuzwrap(): array
    {
        return $this->guzwrapRequestData;
    }

    public function getCommand(): string
    {
        return $this->parsedBody['command'];
    }

    public function getRouter(): array
    {
        return $this->parsedBody['router'] ?? [];
    }

    public function getTime(): int
    {
        return $this->parsedBody['time'];
    }

    public function parsedBody(): array
    {
        return $this->parsedBody;
    }
}