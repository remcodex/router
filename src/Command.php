<?php


namespace Remcodex\Router;


class Command
{
    /**
     * @var array<callable>
     */
    private static array $listeners = [];
    private bool $isLoaded = false;
    private string $commandsDefinitionFile;

    public function __construct(string $commandsDefinitionFile)
    {
        $this->commandsDefinitionFile = $commandsDefinitionFile;
    }

    /**
     * @param string $command
     * @param callable|array $listener
     */
    public static function listen(string $command, $listener): void
    {
        self::$listeners[$command] = $listener;
    }

    public function getListeners(): array
    {
        $this->loadCommands();
        return self::$listeners;
    }

    private function loadCommands(): void
    {
        if (!$this->isLoaded) {
            require $this->commandsDefinitionFile;
        }
    }
}