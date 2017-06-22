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
    const DEFAULT_COMMAND = 'defaults';

    private $commandNamespacePrefix;

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
        if ($command === '') {
            $command = self::DEFAULT_COMMAND;
        }
        if (!in_array($command, $this->commands, true)) {
            throw new \InvalidArgumentException("Command '{$command}' not implemented");
        }

        $commandClassName = $this->resolveCommandClassName($command);

        $this->withCommandClassObj($commandClassName);
    }

    private function resolveCommandClassName($command)
    {
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

        return $this->commandNamespacePrefix . implode('\\', $handledCmdSegments);
    }

    private function withCommandClassObj($commandClassName)
    {
        if (!class_exists($commandClassName)) {
            throw new \RuntimeException("Class '{$commandClassName}' is not found");
        }

        $obj = new $commandClassName();
        if (!$obj instanceof CommandInterface) {
            throw new \RuntimeException(
                "Class '{$commandClassName}' must implement '" . CommandInterface::class . "'"
            );
        }

        $this->commandClassName = $commandClassName;
        $this->commandClassObj = $obj;
    }

    public function executeCommand(array $args)
    {
        if ($this->commandClassObj) {
            return $this->commandClassObj->run($args);
        }

        return $this->executeDefaultCommand($args);
    }

    public function executeDefaultCommand($args)
    {
        return 0;
    }
}