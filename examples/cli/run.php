<?php
/**
 * User: coderd
 * Date: 2017/3/23
 * Time: 15:38
 */

ini_set('display_errors', 'on');

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/Command/User/ListAll.php';
require __DIR__ . '/Command/Foo.php';

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


