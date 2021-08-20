<?php

namespace Cmd;

class Executor
{
    private $commands = [
        'make:migration' => Migration::class,
        'migrate' => Migrate::class,
    ];

    public function __construct(string $command, array $arguments = [])
    {
        if (!isset($this->commands[$command]))
            throw new \Exception('Command not found');

        $handler = new $this->commands[$command]();

        return $this->execute($handler, $arguments);
    }

    private function execute(Command $handler, array $arguments = [])
    {
        return $handler::run($arguments);
    }
}