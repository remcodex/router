<?php


namespace Remcodex\Router;

use Exception;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Http\Server as ReactHttpServer;
use React\Socket\Server as ReactSocketServer;
use Remcodex\Router\Core\Middlewares\DecodeAndValidateRequestDataMiddleware;

class Server
{
    public const ENV_PRODUCTION = 'production';
    public const ENV_DEVELOPMENT = 'development';

    protected string $uri;
    protected LoopInterface $eventLoop;
    protected ReactHttpServer $httpServer;
    protected ReactSocketServer $socketServer;
    /**
     * @var array<RemoteServer>
     */
    protected array $remoteServers = [];
    /**
     * @var array<callable>
     */
    protected array $middlewares = [];
    /**
     * @var callable $errorHandler
     */
    protected $errorHandler = null;


    public function __construct(string $uri)
    {
        set_exception_handler(ErrorHandler::create());
        $this->uri = $uri;
    }

    /**
     * Provide a uri that this http server will listen to
     * @param string $uri ip:port uri, example 0.0.0.0:91002
     * @return Server
     */
    public static function listen(string $uri): Server
    {
        return new Server($uri);
    }

    /**
     * Add callback to be called when an error occurred
     * @param callable $callback
     * @return $this
     */
    public function onError(callable $callback): Server
    {
        $this->errorHandler = $callback;
        return $this;
    }

    /**
     * Use custom reactphp's event loop
     * @link https://reactphp.org/event-loop/
     * @param LoopInterface $loop
     * @return Server
     */
    public function useEventLoop(LoopInterface $loop): Server
    {
        $this->eventLoop = $loop;
        return $this;
    }

    /**
     * Use custom reactphp http server
     * @link https://reactphp.org/http
     * @param ReactHttpServer $server
     * @return Server
     */
    public function useHttpServer(ReactHttpServer $server): Server
    {
        $this->httpServer = $server;
        return $this;
    }

    /**
     * Use custom reactphp socket server
     * @link https://reactphp.org/socket
     * @param ReactSocketServer $server
     * @return Server
     */
    public function useSocketServer(ReactSocketServer $server): Server
    {
        $this->socketServer = $server;
        return $this;
    }

    /**
     * Add remote server - a server that will be connected to
     * @param RemoteServer ...$remoteServers A server or array of servers
     * @return $this
     */
    public function addRemoteServer(RemoteServer ...$remoteServers): Server
    {
        $this->remoteServers = array_merge($this->remoteServers, $remoteServers);
        return $this;
    }

    /**
     * Starts routing servers
     * @throws Exception
     */
    public function start(): void
    {
        //Check if remote server is added
        if (empty($this->remoteServers)) {
            throw new Exception('No remote server added.');
        }

        //Add router to middlewares
        $this->addMiddleware(new DecodeAndValidateRequestDataMiddleware());
        $this->addMiddleware(new RequestHandler($this->remoteServers));

        $server = $this->getHttpServer();
        $server->on('error', ErrorHandler::create($this->errorHandler));
        $server->listen($this->getSocketServer());

        //Init react helper
        setLoop($this->eventLoop);

        //Run event loop
        $this->getEventLoop()->run();
    }

    /**
     * Add middleware to http server
     * @param callable ...$middlewares
     * @return $this
     * @link https://reactphp.org/http/#middleware
     */
    public function addMiddleware(callable ...$middlewares): Server
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }

    protected function getHttpServer(): ReactHttpServer
    {
        if (!isset($this->httpServer)) {
            $this->httpServer = new ReactHttpServer($this->getEventLoop(), ...$this->middlewares);
        }

        return $this->httpServer;
    }

    protected function getEventLoop(): LoopInterface
    {
        if (!isset($this->eventLoop)) {
            $this->eventLoop = Factory::create();
        }

        return $this->eventLoop;
    }

    protected function getSocketServer(): ReactSocketServer
    {
        if (!isset($this->socketServer)) {
            $this->socketServer = new ReactSocketServer($this->uri, $this->eventLoop);
        }

        return $this->socketServer;
    }

}