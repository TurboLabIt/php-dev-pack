<?php declare(strict_types=1);
namespace TurboLabIt\TLIBaseBundle\tests\Traits;

use PHPUnit\Framework\TestCase;


class TitleableEntityTest extends TestCase
{
    public function testSet()
    {
        $arr = [];
        for($i = 1; $i < 5; $i++) {

            $txt = $this->generateRandomText($i);

            $arr[] =
                (new DummyEntity())
                    ->setTitle($txt);
        }

        $this->assertNotTrue( empty($arr) );

        return $arr;
    }


    public function testGet()
    {
        $arr = $this->testSet();

        $i = 1;
        foreach($arr as $oneItem) {

            $name = $oneItem->getTitle();
            $expectedName = $this->generateRandomText($i);
            $this->assertEquals($name, $expectedName);
            $i++;
        }
    }

    protected function generateRandomText($seed)
    {
        $base = $seed + ($seed * 175);
        $hash = md5((string)$base);

        $arrHash = str_split($hash, 4);

        $final = implode('', $arrHash);
        return $final;
    }
}
