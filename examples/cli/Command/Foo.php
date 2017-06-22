<?php

/**
 * User: coderd
 * Date: 2017/3/23
 * Time: 16:55
 */

namespace Command;

use Rapkg\Cli\CommandInterface;

class Foo implements CommandInterface
{
    public function help()
    {
        return 'Command foo help';
    }

    public function run(array $args)
    {
        echo "Command foo is running\n";
        echo "Args:\n";
        var_dump($args);

        return 0;
    }

    public function synopsis()
    {
        return 'Foo command';
    }
}