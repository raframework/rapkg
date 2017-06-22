<?php
/**
 * User: coderd
 * Date: 2017/3/23
 * Time: 11:17
 */

namespace Rapkg\Cli;


class App
{
    /**
     * @var array
     */
    private $config = [];

    /**
     * @var Router
     */
    private $router;

    /**
     * App constructor.
     *
     * @param array $config Must be an associative in the format:
     *                      [
     *                          'command_namespace_prefix' => 'string',
     *                          'commands' => [
     *                              'string',
     *                              ...
     *                          ],
     *                      ]
     */
    public function __construct(array $config)
    {
        $this->parseConfig($config);

        $this->router = new Router($this->config['command_namespace_prefix'], $this->config['commands']);
    }

    private function parseConfig(array $config)
    {
        if (!isset($config['command_namespace_prefix']) || !is_string($config['command_namespace_prefix'])) {
            throw new \InvalidArgumentException('Invalid config field "command_namespace_prefix", a not-empty string is required.');
        }
        if (!isset($config['commands']) || !is_array($config['commands']) || empty($config['commands'])) {
            throw new \InvalidArgumentException('Invalid config field "commands", a not-empty array is required.');
        }

        $this->config = $config;
    }

    /**
     * run run the command and return exit code.
     *
     * @param array $args
     * @return int
     */
    public function run(array $args)
    {
        $command = $args[1];
        $this->router->match($command);
        $args = array_slice($args, 2);

        return $this->router->executeCommand($args);
    }
}