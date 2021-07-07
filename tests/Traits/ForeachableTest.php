<?php declare(strict_types=1);
namespace TurboLabIt\TLIBaseBundle\tests\Traits;

use PHPUnit\Framework\TestCase;


final class ForeachableTest extends TestCase
{
    // dummy data to test against
    use Data;

    public function testForeach()
    {
        $collDummy = new CollectionDummy();

        $i = 0;
        foreach($collDummy as $oneArticle) {

            $dummyTitle     = $this->arrDummyData[$i]["title"];
            $dummyAbstract  = $this->arrDummyData[$i]["abstract"];

            $this->assertEquals($oneArticle->getTitle(), $dummyTitle);
            $this->assertEquals($oneArticle->getAbstract(), $dummyAbstract);

            $i++;
        }
    }


    public function testForeachDifferent()
    {
        $collDummy = new CollectionDummy();

        $i = 0;
        foreach($collDummy as $oneArticle) {

            $j =
                $i == ($collDummy->count() - 1) ? 0 : $i + 1;

            $dummyTitle     = $this->arrDummyData[$j]["title"];
            $dummyAbstract  = $this->arrDummyData[$j]["abstract"];

            $this->assertNotEquals($oneArticle->getTitle(), $dummyTitle);
            $this->assertNotEquals($oneArticle->getAbstract(), $dummyAbstract);

            $i++;
        }
    }


    public function testRewind()
    {
        $collDummy = new CollectionDummy();

        $firstActual    = reset($this->arrDummyData);
        $dummyTitle     = $firstActual["title"];
        $dummyAbstract  = $firstActual["abstract"];

        $actualCount    = count($this->arrDummyData);

        $i = 0;
        foreach($collDummy as $none) {

            $collDummy->rewind();
            $oneArticle = $collDummy->current();

            $this->assertEquals($oneArticle->getTitle(), $dummyTitle);
            $this->assertEquals($oneArticle->getAbstract(), $dummyAbstract);

            if( $i == ($actualCount -1) ) {

                break;
            }

            $i++;
        }
    }


    public function testCurrent()
    {
        $collDummy = new CollectionDummy();
        $collDummy->next();

        $dummyTitle     = $this->arrDummyData[1]["title"];
        $dummyAbstract  = $this->arrDummyData[1]["abstract"];

        $oneArticle = $collDummy->current();
        $this->assertEquals($oneArticle->getTitle(), $dummyTitle);
        $this->assertEquals($oneArticle->getAbstract(), $dummyAbstract);
    }


    public function testKey()
    {
        $collDummy = new CollectionDummy();

        $i = 0;
        foreach($collDummy as $none) {

            $key = $collDummy->key();
            $this->assertEquals($key, $i);

            $i++;
        }
    }


    public function testNext()
    {
        $collDummy = new CollectionDummy();

        for($i = 0; $i < 3; $i++) {

            $dummyTitle     = $this->arrDummyData[$i]["title"];
            $dummyAbstract  = $this->arrDummyData[$i]["abstract"];

            $oneArticle = $collDummy->current();
            $this->assertEquals($oneArticle->getTitle(), $dummyTitle);
            $this->assertEquals($oneArticle->getAbstract(), $dummyAbstract);

            $collDummy->next();
        }

        for($i = 1; $i < 5; $i++) {

            $collDummy->next();
            $oneArticle = $collDummy->current();
            $this->assertNull($oneArticle);
        }
    }


    public function testValid()
    {
        $collDummy = new CollectionDummy();
        $count = count($this->arrDummyData);

        for($i = 0; $i < 10; $i++) {

            if( $i < $count ) {

                $this->assertTrue($collDummy->valid());

            } else {

                $this->assertFalse($collDummy->valid());
            }

            $collDummy->next();
        }
    }


    public function testCount()
    {
        $collDummy      = new CollectionDummy();
        $collCount      = $collDummy->count();
        $actualCount    = count($this->arrDummyData);
        $this->assertEquals($collCount, $actualCount);
    }


    public function testFirst()
    {
        $collDummy      = new CollectionDummy();

        $firstActual    = reset($this->arrDummyData);
        $dummyTitle     = $firstActual["title"];
        $dummyAbstract  = $firstActual["abstract"];

        foreach($collDummy as $none) {

            $firstArticle   = $collDummy->first();

            $this->assertEquals($firstArticle->getTitle(), $dummyTitle);
            $this->assertEquals($firstArticle->getAbstract(), $dummyAbstract);
        }
    }


    public function testLast()
    {
        $collDummy      = new CollectionDummy();

        $lastActual     = end($this->arrDummyData);
        $dummyTitle     = $lastActual["title"];
        $dummyAbstract  = $lastActual["abstract"];

        foreach($collDummy as $none) {

            $lastArticle   = $collDummy->last();

            $this->assertEquals($lastArticle->getTitle(), $dummyTitle);
            $this->assertEquals($lastArticle->getAbstract(), $dummyAbstract);
        }
    }


    public function testGet()
    {
        $collDummy = new CollectionDummy();

        $i = 0;
        foreach($collDummy as $none) {

            $oneArticle     = $collDummy->get($i);

            $dummyTitle     = $this->arrDummyData[$i]["title"];
            $dummyAbstract  = $this->arrDummyData[$i]["abstract"];

            $this->assertEquals($oneArticle->getTitle(), $dummyTitle);
            $this->assertEquals($oneArticle->getAbstract(), $dummyAbstract);

            $i++;
        }
    }


    public function testClear()
    {
        $collDummy = new CollectionDummy();
        $collDummy->next();
        $collDummy->next();
        $collDummy->clear();

        foreach($collDummy as $none) {

            $this->assertFalse(true);
        }

        $collCount = $collDummy->count();
        $this->assertEquals($collCount, 0);
    }


    public function testGetAll()
    {
        $collDummy          = new CollectionDummy();
        $arrDummyObjects    = $collDummy->getAll();
        $countActual        = count($this->arrDummyData);

        $this->assertTrue( count($arrDummyObjects) === $countActual );

        for( $i = 0; $i < $countActual; $i++ ) {

            $dummyTitle             = $arrDummyObjects[$i]->getTitle();
            $dummyAbstract          = $arrDummyObjects[$i]->getAbstract();

            $dummyTitleActual       = $this->arrDummyData[$i]["title"];
            $dummyAbstractActual    = $this->arrDummyData[$i]["abstract"];

            $this->assertEquals($dummyTitle, $dummyTitleActual);
            $this->assertEquals($dummyAbstract, $dummyAbstractActual);
        }
    }
}
