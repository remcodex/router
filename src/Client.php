<?php


namespace Remcodex\Router;


use Psr\Http\Message\ServerRequestInterface;

class Client
{
    private int $id;
    private array $requestData;
    private string $clientIP;
    private string $clientPort;

    public function __construct(ServerRequestInterface $request)
    {
        $this->id = spl_object_id($this);
        $this->requestData = Context::getPayload($request)->getGuzwrap();
        $this->clientIP = $request->getServerParams()['REMOTE_ADDR'];
        $this->clientPort = $request->getServerParams()['REMOTE_PORT'];
    }

    public static function create(ServerRequestInterface $request): Client
    {
        return new Client($request);
    }

    /**
     * @return string
     */
    public function getClientIP(): string
    {
        return $this->clientIP;
    }

    /**
     * @return string
     */
    public function getClientPort(): string
    {
        return $this->clientPort;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function getRequestData(): array
    {
        return $this->requestData;
    }
}