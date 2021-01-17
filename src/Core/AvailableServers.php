<?php


namespace Remcodex\Router\Core;


use Exception;
use Remcodex\Router\RemoteServer;

class AvailableServers
{
    /**
     * @var array<RemoteServer>
     */
    private static array $servers = [];

    public static function add(RemoteServer ...$remoteServers): void
    {
        self::$servers = array_merge(self::$servers, $remoteServers);
    }

    /**
     * @return RemoteServer[]
     */
    public static function getServers(): array
    {
        return self::$servers;
    }

    public static function getById(int $id): RemoteServer
    {
        foreach (self::$servers as $server) {
            if ($server->getId() == $id) {
                return $server;
            }
        }

        throw new Exception("No server with id({$id}) found");
    }
}