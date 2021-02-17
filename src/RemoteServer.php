<?php


namespace Remcodex\Router;


use JsonSerializable;

class RemoteServer implements JsonSerializable
{
    /**
     * Will indicate to force use secure protocol to connect to this server.
     */
    public const SECURE = 'secure';
    /**
     * Will indicate to use unsecure protocol when connecting to this server
     */
    public const UNSECURE = 'unsecure';
    private const SECURE_HTTP_PROTOCOL = 'https://';
    private const UNSECURE_HTTP_PROTOCOL = 'http://';
    private int $id;
    private int $activeConnections = 0;
    private int $totalConnections = 0;
    private string $hostAddress;
    private string $protocol = 'http://';
    private ?string $httpPath = null;
    private string $geoLocation;

    public function __construct(string $hostAddress)
    {
        $this->id = spl_object_id($this);

        if (substr($hostAddress, -1) == '/') {
            $hostAddress = substr($hostAddress, 0, -1);
        }

        $this->hostAddress = $hostAddress;
    }

    public static function create(string $hostAddress): RemoteServer
    {
        return new RemoteServer($hostAddress);
    }

    /**
     * Server api route path
     * @param string $httpPath
     * @return $this
     */
    public function path(string $httpPath): RemoteServer
    {
        $this->httpPath = $httpPath;
        return $this;
    }

    /**
     * Choose how secure you wants your connection to be
     * @param string $protocol use RemoteServer::SECURE or RemoteServer::UNSECURE
     * @return $this
     */
    public function protocol(string $protocol): RemoteServer
    {
        $this->protocol = $protocol;
        return $this;
    }

    /**
     * Set server geographic location
     * @param string $location Server continent.country, example: africa.nigeria
     * @return RemoteServer
     */
    public function geoLocation(string $location): RemoteServer
    {
        $this->geoLocation = $location;
        return $this;
    }

    public function incrementTotalConnections(): void
    {
        $this->totalConnections += 1;
    }

    public function incrementActiveConnection(): void
    {
        $this->activeConnections += 1;
    }

    public function decrementActiveConnection(): void
    {
        $this->activeConnections -= 1;
    }

    /**
     * @return string
     */
    public function getHostAddress(): string
    {
        return $this->hostAddress;
    }

    /**
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->protocol;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getActiveConnections(): int
    {
        return $this->activeConnections;
    }

    /**
     * @return int
     */
    public function getTotalConnections(): int
    {
        return $this->totalConnections;
    }

    public function getConstructServerUri(): string
    {
        if ($this->protocol == self::SECURE) {
            return self::SECURE_HTTP_PROTOCOL . $this->hostAddress . '/' . $this->httpPath;
        }

        return $serverUri = self::UNSECURE_HTTP_PROTOCOL . $this->hostAddress . '/' . $this->httpPath;
    }

    public function getGeoLocation(): string
    {
        return $this->geoLocation;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'activeConnections' => $this->activeConnections,
            'totalConnections' => $this->totalConnections,
            'hostAddress' => $this->hostAddress,
            'httpPath' => $this->httpPath,
            'protocol' => $this->protocol,
            'geoLocation' => $this->geoLocation,
        ];
    }
}