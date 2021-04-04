<?php declare(strict_types=1);
namespace TurboLabIt\TLIBaseBundle\tests\Traits;

use PHPUnit\Framework\TestCase;


class ViewableEntityTest extends TestCase
{
    protected $sampleViewNumber;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->sampleViewNumber = time();
    }


    public function testSet()
    {
        $arr = [];
        for($i = 1; $i < 5; $i++) {

            $views = $this->sampleViewNumber * $i;
            $views = (string)$views;

            $arr[] =
                (new DummyEntity())
                    ->setViews($views);
        }

        $this->assertNotTrue( empty($arr) );

        return $arr;
    }


    public function testGet()
    {
        $arr = $this->testSet();

        $i = 1;
        foreach($arr as $oneItem) {

            $actualViews    = $oneItem->getViews();
            $expectedViews  = $this->sampleViewNumber * $i;
            $this->assertEquals($actualViews, $expectedViews);
            $i++;
        }
    }


    public function testSetIntZero()
    {
        $expectedViews = 7;

        $item =
            (new DummyEntity())
                ->setViews($expectedViews);

        $actualViews    = $item->getViews();

        $this->assertEquals($actualViews, $expectedViews);

        $item->setViews(0);

        $this->assertEquals($item->getViews(), 0);
    }


    public function testSetStringZero()
    {
        $expectedViews = 7;

        $item =
            (new DummyEntity())
                ->setViews((string)$expectedViews);

        $actualViews    = $item->getViews();

        $this->assertEquals($actualViews, $expectedViews);

        $item->setViews((string)0);

        $this->assertEquals($item->getViews(), 0);
    }


    public function testSetNegative()
    {
        $this->expectException(\ValueError::class);
        (new DummyEntity())
            ->setViews(-1);
    }


    public function testSetInvalid()
    {
        $this->expectException(\InvalidArgumentException::class);
        (new DummyEntity())
            ->setViews("pippo");
    }
}
