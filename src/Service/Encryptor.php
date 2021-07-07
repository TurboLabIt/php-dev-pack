<?php
namespace TurboLabIt\TLIBaseBundle\Service;

use App\Exception\EncryptionException;
use App\Exception\UnserializationException;

class Encryptor
{
    protected $secretKey;
    protected $cryptInitVector;

    protected $specialCharMap = [

        "/"     => "__ssym1__",
        "\\"    => "__ssym2__",
    ];

    public function __construct($secretKey, $cryptInitVector)
    {
        $this->secretKey        = $secretKey;
        $this->cryptInitVector  = $cryptInitVector;
    }

    public function encrypt($data): string
    {
        $this->preventLeaks();

        if(empty($data)) {

            return '';
        }

        $txtData    = is_array($data) || is_object($data) ? serialize($data) : $data;
        $txtData    = openssl_encrypt($txtData, "AES256", $this->secretKey, 0, $this->cryptInitVector);
        $txtData    = str_ireplace( array_keys($this->specialCharMap), $this->specialCharMap, $txtData);
        return $txtData;
    }


    public function decrypt($data, $unserialize = true)
    {
        $this->preventLeaks();

        if(empty($data)) {

            return $data;
        }

        $data       = str_ireplace( $this->specialCharMap, array_keys($this->specialCharMap), $data);
        $txtData    = @openssl_decrypt($data, "AES256", $this->secretKey, 0, $this->cryptInitVector);
        if($txtData === false) {

            throw new EncryptionException();
        }

        if(!$unserialize) {

            return $data;
        }

        $txtData = @unserialize($txtData);
        if($txtData === false) {

            throw new UnserializationException();
        }

        return $txtData;
    }


    protected function preventLeaks()
    {
        if( empty($this->secretKey) || empty($this->cryptInitVector) ) {

            throw new EncryptionException();
        }
    }
}
