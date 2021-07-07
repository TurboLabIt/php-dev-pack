<?php declare(strict_types=1);
namespace TurboLabIt\TLIBaseBundle\tests\Traits;

use PHPUnit\Framework\TestCase;
use TurboLabIt\TLIBaseBundle\Twig\TliTwigRuntime;


class TliTwigRuntimeTest extends TestCase
{
    /**
     * @dataProvider dateToTestProvider
     */
    public function testFriendlyDateSmallFuture()
    {
        $date           = (new \DateTime())->modify('+1 hour');
        $expectedValue  = $date->format('d/m/Y') . ' alle ' . $date->format('H:i');
        $actualValue    = (new TliTwigRuntime())->friendlyDate($date);

        $this->assertEquals($expectedValue, $actualValue);
    }
}
