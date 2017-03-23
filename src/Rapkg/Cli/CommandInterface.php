<?php
/**
 * User: coderd
 * Date: 2017/3/23
 * Time: 11:14
 */

namespace Rapkg\Cli;


interface CommandInterface
{
    /**
     * help should return long-form help text that includes the command-line
     * usage, a brief few sentences explaining the function of the command,
     * and the complete list of flags the command accepts.
     *
     * @return string
     */
    public function help();

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
    public function run(array $args);

    /**
     * synopsis should return a one-line, short synopsis of the command.
     * This should be less than 50 characters ideally.
     *
     * @return string
     */
    public function synopsis();
}