<?php
/**
 * User: coderd
 * Date: 2017/6/22
 * Time: 19:26
 */

namespace Command;


use Rapkg\Cli\CommandInterface;

class Defaults implements CommandInterface
{

    /**
     * help should return long-form help text that includes the command-line
     * usage, a brief few sentences explaining the function of the command,
     * and the complete list of flags the command accepts.
     *
     * @return string
     */
    public function help()
    {
        return 'Default command';
    }

    /**
     * run should run the actual command with the given App instance and
     * command-line arguments. It should return the exit status when it is
     * finished.
     * There are a handful of special exit codes this can return documented
     * above that change behavior.
     *
     * @param array $args
     * @return int
     */
    public function run(array $args)
    {
        return 0;
    }

    /**
     * synopsis should return a one-line, short synopsis of the command.
     * This should be less than 50 characters ideally.
     *
     * @return string
     */
    public function synopsis()
    {
        return $this->help();
    }
}