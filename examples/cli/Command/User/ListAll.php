<?php

/**
 * User: coderd
 * Date: 2017/3/23
 * Time: 16:48
 */

namespace Command\User;

use Rapkg\Cli\CommandInterface;

class ListAll implements CommandInterface
{
    public function help()
    {
        return 'Command users/list_all help';
    }

    public function run(array $args)
    {
        echo "Command users/list_all is running\n";

        return 0;
    }

    public function synopsis()
    {
        return 'Command users/list_all synopsis';
    }
}