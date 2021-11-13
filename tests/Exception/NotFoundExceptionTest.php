<?php
namespace TurboLabIt\TLIBaseBundle\tests\Exception;

use PHPUnit\Framework\TestCase;
use TurboLabIt\TLIBaseBundle\Exception\NotFoundException;


class NotFoundExceptionTest extends TestCase
{
    const MESSAGE   = "This exception is a test";
    const PAGE_URL  = 'http://example.com/page-with-error';
    const REF_URL   = 'http://example.com/referring-page';
    const ID        = '91875';
    const TITLE     = 'My test title';


    public function testCreateInstance()
    {
        $ex = new MockNotFoundException(
            (new MockLogger()), [], static::MESSAGE, null,static::PAGE_URL, static::REF_URL
        );
        $this->assertInstanceOf('TurboLabIt\TLIBaseBundle\Exception\NotFoundException', $ex);
        return $ex;
    }


    public function testMessage()
    {
        $ex = $this->testCreateInstance();

        $arrSeverities = [
            NotFoundException::SEVERITY_CRITICAL, NotFoundException::SEVERITY_HIGH,
            NotFoundException::SEVERITY_MEDIUM, NotFoundException::SEVERITY_LOW
        ];

        foreach($arrSeverities as $severity) {

            $ex->setSeverity($severity);
            $actualText = $ex->getMessage();
            $this->assertStringContainsString($severity, $actualText);
            $this->assertStringContainsString(static::PAGE_URL, $actualText);
            $this->assertStringContainsString(static::REF_URL, $actualText);
        }
    }


    public function testMessageWithData()
    {
        $actualText =
            $this->testCreateInstance()
                ->setData([
                    "id"    => static::ID,
                    "title" => static::TITLE
                ])
                ->getMessage();

        $this->assertStringContainsString(static::ID, $actualText);
        $this->assertStringContainsString(static::TITLE, $actualText);
    }


    public function testThrowability()
    {
        $this->expectException(NotFoundException::class);
        throw $this->testCreateInstance();
    }
}