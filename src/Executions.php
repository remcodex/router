<?php


namespace Remcodex\Router;


use Exception;
use Nette\Utils\Json;
use Nette\Utils\JsonException;
use React\Http\Browser;
use React\Promise\PromiseInterface;

class Executions
{
    /**
     * Add execution
     * @param Payload $payload
     * @return PromiseInterface
     * @throws JsonException
     * @throws Exception
     */
    public static function add(Payload $payload): PromiseInterface
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

        $requestData = Json::encode($payload->parsedBody());
        return $browser->post($serverUri, [], $requestData);
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