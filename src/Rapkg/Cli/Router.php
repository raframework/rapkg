<?php
/**
 * User: coderd
 * Date: 2017/3/23
 * Time: 11:17
 */

namespace Rapkg\Cli;


class Router
{
    const DEFAULT_COMMAND = 'defaults';
    const DEFAULT_COMMAND_NAMESPACE_PREFIX = "Command\\";

    /**
     * @var string
     */
    private $commandNamespacePrefix;

    /**
     * @var array
     */
    private $commands;

    /**
     * @var string
     */
    private $commandClassName;

    /**
     * @var CommandInterface
     */
    private $commandClassObj;

    private $defaultCommand = self::DEFAULT_COMMAND;

    public function __construct($commandNamespacePrefix, array $commands)
    {
        $this->commandNamespacePrefix = $commandNamespacePrefix;
        $this->commands = $commands;
    }

    public function match($command)
    {
        if ($command === '') {
            $command = $this->getDefaultCommand();
            $commandClassName = $this->resolveCommandClassName($command);
            if (!class_exists($commandClassName)) {
                return;
            }
        } else {
            if (!in_array($command, $this->commands, true)) {
                return;
            }
            $commandClassName = $this->resolveCommandClassName($command);
            if (!class_exists($commandClassName)) {
                throw new \RuntimeException("Class '{$commandClassName}' is not found");
            }
        }

        $this->withCommandClassObj($commandClassName);
    }

    private function getDefaultCommand()
    {
        return $this->defaultCommand;
    }

    private function resolveCommandClassName($command)
    {
        return $this->commandNamespacePrefix
            . str_replace(
                '/',
                '\\',
                str_replace('_', '', ucwords($command, '_/'))
            );
    }

    /**
     * @param $commandClassName
     * @throws \RuntimeException
     * @return CommandInterface
     */
    private function createCommandClassObj($commandClassName)
    {
        $obj = new $commandClassName();
        if (!$obj instanceof CommandInterface) {
            throw new \RuntimeException(
                "Class '{$commandClassName}' must implement '" . CommandInterface::class . "'"
            );
        }

        return $obj;
    }

    private function withCommandClassObj($commandClassName)
    {
        $obj = $this->createCommandClassObj($commandClassName);
        $this->commandClassName = $commandClassName;
        $this->commandClassObj = $obj;
    }

    public function executeCommand(array $args)
    {
        if ($this->commandClassObj) {
            return $this->commandClassObj->run($args);
        }

        return $this->defaultHandling($args);
    }

    public function defaultHandling($args)
    {
        $this->help();

        return 0;
    }

    private function help()
    {
        echo "Usage: cli [--version] [--help] <command> [<args>]\n";
        echo "\n";
        echo "Available commands are:\n";

        if (empty($this->commands)) {
            return;
        }

        $commandSynopsises = [];
        $commandMaxLength = 0;
        foreach ($this->commands as $command) {
            $commandClassName = $this->resolveCommandClassName($command);
            $obj = $this->createCommandClassObj($commandClassName);
            $commandSynopsises[$command] = $obj->synopsis();
            $len = strlen($command);
            if ($len > $commandMaxLength) {
                $commandMaxLength = $len;
            }
        }

        foreach ($commandSynopsises as $command => $synopsis) {
            echo '    ' . $command
                . str_repeat(' ', $commandMaxLength + 4 - strlen($command))
                . $synopsis . "\n";
        }
    }
}