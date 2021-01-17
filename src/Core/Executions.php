<?php


namespace Remcodex\Router\Core;


use React\Http\Browser;
use React\Promise\PromiseInterface;

class Executions
{
    private static array $clients;

    public static function add(Client $client): PromiseInterface
    {
        $connections = [];
        foreach (AvailableServers::getServers() as $remoteServer) {
            $connections[$remoteServer->getId()] = $remoteServer->getActiveConnections();
        }

        $lowestConnServer = self::findServerWithLowestConnections($connections);

        $availableServer = AvailableServers::getById($lowestConnServer['key']);
        $serverUri = $availableServer->getConstructServerUri();

        //Update stats
        $availableServer->incrementActiveConnection();
        $availableServer->incrementTotalConnections();

        $browser = new Browser(getLoop());
        return $browser->post($serverUri, [], $client->getRequestData());
    }

    public static function findServerWithLowestConnections(array $arrays): array
    {
        $firstArrayKey = array_key_first($arrays);
        $currentMin = [
            'value' => $arrays[$firstArrayKey],
            'key' => $firstArrayKey
        ];

        foreach ($arrays as $arrayKey => $arrayValue) {
            if ($arrayValue <= $currentMin['value']) {
                $currentMin = [
                    'value' => $arrayValue,
                    'key' => $arrayKey,
                ];
            }
        }

        return $currentMin;
    }
}