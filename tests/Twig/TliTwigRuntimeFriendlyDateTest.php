<?php declare(strict_types=1);
namespace TurboLabIt\TLIBaseBundle\tests\Twig;

use PHPUnit\Framework\TestCase;
use TurboLabIt\TLIBaseBundle\Twig\TliTwigRuntime;


class TliTwigRuntimeFriendlyDateTest extends TestCase
{
    public function testSmallFuture()
    {
        $date           = (new \DateTime())->modify('+1 hour');
        $expectedValue  = $date->format('d/m/Y') . ' alle ' . $date->format('H:i');
        $actualValue    = (new TliTwigRuntime())->friendlyDate($date);
        $this->assertEquals($expectedValue, $actualValue);
    }

    public function testSmallPast()
    {
        $date           = (new \DateTime())->modify('-2 days -1 second');
        $expectedValue  = $date->format('d/m/Y') . ' alle ' . $date->format('H:i');
        $actualValue    = (new TliTwigRuntime())->friendlyDate($date);
        $this->assertEquals($expectedValue, $actualValue);
    }

    public function testEdgePast()
    {
        $date           = (new \DateTime())->modify('-2 days');
        $expectedValue  = 'ieri alle ' . $date->format('H:i');
        $actualValue    = (new TliTwigRuntime())->friendlyDate($date);
        $this->assertEquals($expectedValue, $actualValue);
    }


    public function testYesterday()
    {
        for($i=24; $i < 48; $i++) {

            $date           = (new \DateTime())->modify('-' . $i . ' hours');
            $expectedValue  = 'ieri alle ' . $date->format('H:i');
            $actualValue    = (new TliTwigRuntime())->friendlyDate($date);
            $this->assertEquals($expectedValue, $actualValue);
        }
    }


    public function testHoursAgo()
    {
        for($i=1; $i < 24; $i++) {

            $date           = (new \DateTime())->modify('-' . $i . ' hours');
            $word           = $i == 1 ? 'ora' : 'ore';
            $expectedValue  = $i . ' ' . $word . ' fa';
            $actualValue    = (new TliTwigRuntime())->friendlyDate($date);
            $this->assertEquals($expectedValue, $actualValue);
        }
    }


    public function testMinutesAgo()
    {
        for($i=30; $i < 60; $i++) {

            $date           = (new \DateTime())->modify('-' . $i . ' minutes');
            $expectedValue  = $i . ' minuti fa';
            $actualValue    = (new TliTwigRuntime())->friendlyDate($date);
            $this->assertEquals($expectedValue, $actualValue);
        }
    }


    public function testNow()
    {
        for($i=0; $i < 30; $i++) {

            $date           = (new \DateTime())->modify('-' . $i . ' minutes');
            $expectedValue  = 'adesso';
            $actualValue    = (new TliTwigRuntime())->friendlyDate($date);
            $this->assertEquals($expectedValue, $actualValue);
        }
    }
}
