<?php declare(strict_types=1);
namespace TurboLabIt\TLIBaseBundle\tests\Traits;

use PHPUnit\Framework\TestCase;


class PublicableEntityTest extends TestCase
{
    public function testSetPrivate()
    {
        $arr = [];
        for($i = 1; $i < 5; $i++) {

            $arr[] =
                (new DummyEntity())
                    ->setPrivate();
        }

        $this->assertNotTrue( empty($arr) );

        return $arr;
    }

    public function testSetPrivateAsValidString()
    {
        $actualValue =
            (new DummyEntity())
                ->setPrivate("0")
                ->isPrivate();

        $this->assertFalse($actualValue);
    }


    public function testSetPrivateAsInvalidString()
    {
        $this->expectException(\ValueError::class);

        (new DummyEntity())
            ->setPrivate("2")
            ->isPrivate();
    }


    public function testSetPublic()
    {
        $arr = [];
        for($i = 1; $i < 5; $i++) {

            $arr[] =
                (new DummyEntity())
                    ->setPublic();
        }

        $this->assertNotTrue( empty($arr) );

        return $arr;
    }


    public function testIsPublic()
    {
        $arr = $this->testSetPublic();

        $i = 1;
        foreach($arr as $oneItem) {

            $isPublic = $oneItem->isPublic();
            $this->assertTrue($isPublic);

            $isPrivate = $oneItem->isPrivate();
            $this->assertFalse($isPrivate);

            $i++;
        }
    }


    public function testIsPrivate()
    {
        $arr = $this->testSetPrivate();

        $i = 1;
        foreach($arr as $oneItem) {

            $isPrivate = $oneItem->isPrivate();
            $this->assertTrue($isPrivate);

            $isPublic = $oneItem->isPublic();
            $this->assertFalse($isPublic);

            $i++;
        }
    }



}
