<?php
/**
 * User: coderd
 * Date: 2017/3/23
 * Time: 15:38
 */

require "../../vendor/autoload.php";
require "./Command/User/ListAll.php";
require "./Command/Foo.php";

use Rapkg\Cli;

$config = [
    'command_namespace_prefix' => 'Command\\',
    'commands' => [
        'user/list_all',
        'foo',
    ],
];

$app = new Cli\App($config);
exit($app->run($argv));


