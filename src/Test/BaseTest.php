<?php
namespace TurboLabIt\TLIBaseBundle\Test;

use PHPUnit\Framework\TestCase;
use TurboLabIt\TLIBaseBundle\tests\TLIBaseTestingKernel;


class BaseTest extends TestCase
{
    protected static $serviceName;
    protected static TLIBaseTestingKernel $theFineKernel;


    protected function getService($serviceName)
    {
        return static::getTheFineKernel()->getContainer()->get($serviceName);
    }


    public function testInstance()
    {
        $instance = $this->getService(static::$serviceName);
        $this->assertInstanceOf(static::$serviceName, $instance);
        return $instance;
    }



    protected static function getTheFineKernel() : TLIBaseTestingKernel
    {
        if( empty(static::$theFineKernel) ) {

            static::$theFineKernel = static::createKernel();
            static::$theFineKernel->boot();
        }

        return static::$theFineKernel;
    }


    protected static function createKernel(array $options = [])
    {
        return new TLIBaseTestingKernel('test', true);
    }
}
