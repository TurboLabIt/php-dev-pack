<?php
namespace TurboLabIt\TLIBaseBundle\tests\Service;

use PHPUnit\Framework\TestCase;
use TurboLabIt\TLIBaseBundle\Service\Encryptor;


class EncryptorTest extends TestCase
{
    const FAKE_APP_SECRET = 'secret-from-symfony-env';
    const TEXT_TO_ENCODE = 'T%is is /-\ SECRùT';


    protected function testCreateInstance()
    {
        $encryptor = new Encryptor(static::FAKE_APP_SECRET);
        $this->assertNotEmpty($encryptor);

        return $encryptor;
    }


    public function testWithString()
    {
        $encryptedText =
            (new Encryptor(static::FAKE_APP_SECRET))->encrypt(static::TEXT_TO_ENCODE);

        $this->assertNotEquals(static::TEXT_TO_ENCODE, $encryptedText);

        $decryptedText =
            (new Encryptor(static::FAKE_APP_SECRET))->decrypt($encryptedText, false);

        $this->assertEquals(static::TEXT_TO_ENCODE, $decryptedText);
    }


    public function testWithArray()
    {
        $arrData = [
            "one"   => substr(str_shuffle(MD5(microtime())), 0, 10),
            "two"   => substr(str_shuffle(MD5(microtime())), 0, 10),
            "three" => [
                "one"   => substr(str_shuffle(MD5(microtime())), 0, 10),
                "six"   => substr(str_shuffle(MD5(microtime())), 0, 10),
            ]
        ];

        $encryptedText =
            (new Encryptor(static::FAKE_APP_SECRET))->encrypt($arrData);

        $this->assertNotEquals($arrData, $encryptedText);

        $decryptedText =
            (new Encryptor(static::FAKE_APP_SECRET))->decrypt($encryptedText, true);

        $this->assertEquals($arrData, $decryptedText);
    }
}