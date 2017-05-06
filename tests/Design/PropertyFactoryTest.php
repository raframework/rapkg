<?php
/**
 * User: coderd
 * Date: 2017/5/6
 * Time: 下午2:29
 */

namespace tests\Design;


use Rapkg\Design\AbstractPropertyFactory;

class User
{
    public function __construct()
    {
        // do some initializations here.
    }

    public function get($id)
    {
        return $id;
    }
}

/**
 * Class ApiFactory
 * @package tests\Design
 *
 * @property User $user
 */
class ApiFactory extends AbstractPropertyFactory
{
    protected function namespacePrefix()
    {
        return 'tests\\Design\\';
    }
}

class PropertyFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var ApiFactory
     */
    private $apiFactory;

    public function testPropertyFactory()
    {
        $this->apiFactory = new ApiFactory();
        $id = 1;
        $this->assertEquals($id, $this->apiFactory->user->get($id));
    }
}