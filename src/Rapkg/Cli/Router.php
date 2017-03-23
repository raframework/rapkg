<?php
/**
 * User: coderd
 * Date: 2017/3/23
 * Time: 11:17
 */

namespace Rapkg\Cli;


class Router
{
    const DEFAULT_COMMAND_NAMESPACE_PREFIX = "Command\\";

    private $commandNamespacePrefix = '';

    /**
     * @var array
     */
    private $commands = [];

    /**
     * @var string
     */
    private $commandClassName;

    /**
     * @var CommandInterface
     */
    private $commandClassObj;

    public function __construct($commandNamespacePrefix, array $commands)
    {
        $this->commandNamespacePrefix = $commandNamespacePrefix;
        $this->commands = $commands;
    }

    public function match($command)
    {
        if (!in_array($command, $this->commands)) {
            throw new \InvalidArgumentException("Command '{$command}' not implemented");
        }

        $cmdSegments = explode('/', $command);

        $handledCmdSegments = [];
        foreach ($cmdSegments as $cmdSegment) {
            $words = explode('_', $cmdSegment);
            $handledWords = [];
            foreach ($words as $word) {
                $handledWords[] = ucfirst($word);
            }
            $handledCmdSegments[] = implode('', $handledWords);
        }
        $commandClassName = $this->commandNamespacePrefix . implode('\\', $handledCmdSegments);

        $this->withCommandClassObj($commandClassName);
    }

    private function withCommandClassObj($commandClassName)
    {
        if (!class_exists($commandClassName)) {
            throw new \RuntimeException("Class '{$commandClassName}' is not found");
        }

        $obj = new $commandClassName();
        if (!$obj instanceof CommandInterface) {
            throw new \RuntimeException("Class '{$commandClassName}' must implement '" . CommandInterface::class . "'");
        }

        $this->commandClassName = $commandClassName;
        $this->commandClassObj = $obj;
    }

    public function executeCommand(array $args)
    {
        return $this->commandClassObj->run($args);
    }
}