<?php declare(strict_types=1);
namespace TurboLabIt\TLIBaseBundle\tests\Traits;

use PHPUnit\Framework\TestCase;


class IdableEntityTest extends TestCase
{
    public function testSetId()
    {
        $arr = [];
        for($i = 1; $i < 5; $i++) {

            $arr[] =
                (new DummyEntity())
                    ->setId($i * 10000);
        }

        $this->assertNotTrue( empty($arr) );

        return $arr;
    }


    public function testGetId()
    {
        $arr = $this->testSetId();

        $i = 1;
        foreach($arr as $oneItem) {

            $id = $oneItem->getId();
            $this->assertEquals($id, $i * 10000);
            $i++;
        }
    }


    public function testSetWrong()
    {
        $this->expectException(\InvalidArgumentException::class);

        (new DummyEntity())
            ->setId("bad");
    }


    public function testSetZero()
    {
        $this->expectException(\ValueError::class);

        (new DummyEntity())
            ->setId(0);
    }


    public function testSetAcceptZero()
    {
        $oEntity =
            (new DummyEntity())
                ->acceptZero()
                ->setId(0);

        $this->assertEquals($oEntity->getId(), 0);
    }


    public function testSetStringZero()
    {
        $item =
            (new DummyEntity())
                ->acceptZero()
                ->setId("0");

        $actualViews = $item->getViews();

        $this->assertEquals($actualViews, 0);
    }


    public function testSetNegative()
    {
        $this->expectException(\ValueError::class);
        (new DummyEntity())
            ->setId(-1);
    }


    public function testSetInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new DummyEntity())
            ->setId("pippo");
    }
}
