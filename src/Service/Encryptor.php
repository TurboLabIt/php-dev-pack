<?php
namespace TurboLabIt\TLIBaseBundle\Service;

use TurboLabIt\TLIBaseBundle\Exception\EncryptionException;


class Encryptor
{
    const KEY_HASHING_ALGO  = "sha512";
    const ENCRYPT_ALGO      = "AES256";

    protected string $secretKey;
    protected int $iv_num_bytes;

    protected $specialCharMap = [
        "/"     => "__ssym1__",
        "\\"    => "__ssym2__",
    ];


    public function __construct($secretKey)
    {
        $this->secretKey    = openssl_digest($secretKey, static::KEY_HASHING_ALGO, true);
        $this->iv_num_bytes = openssl_cipher_iv_length(static::ENCRYPT_ALGO);
    }


    public function encrypt($data): string
    {
        $this->preventLeaks();

        if(empty($data)) {
            return '';
        }

        // Build an initialisation vector
        $initVector = openssl_random_pseudo_bytes($this->iv_num_bytes, $isStrongCrypto);
        if (!$isStrongCrypto) {
            throw new EncryptionException("encrypt() failure: weak result");
        }

        $txtData = is_array($data) || is_object($data) ? serialize($data) : $data;
        $encryptedString = openssl_encrypt($txtData, static::ENCRYPT_ALGO, $this->secretKey, OPENSSL_RAW_DATA, $initVector);

        if($encryptedString === false) {
            throw new EncryptionException("encrypt() failure: " . openssl_error_string());
        }

        // store the initvector with the encoded string
        $encryptedString = $initVector . $encryptedString;

        //
        $base64EncryptedString = base64_encode($encryptedString);

        // we cannot have / in URLs
        $urlEncodedString = str_ireplace( array_keys($this->specialCharMap), $this->specialCharMap, $base64EncryptedString);

        return $urlEncodedString;
    }


    public function decrypt($encodedEncryptedString, bool $unserialize = true)
    {
        $this->preventLeaks();

        if(empty($encodedEncryptedString)) {
            return $encodedEncryptedString;
        }

        // restore /
        $encodedEncryptedString = str_ireplace( $this->specialCharMap, array_keys($this->specialCharMap), $encodedEncryptedString);

        $encryptedString = base64_decode($encodedEncryptedString);

        // and do an integrity check on the size.
        if (strlen($encryptedString) < $this->iv_num_bytes)  {
            throw new EncryptionException('decrypt() failure: input is too short');
        }

        // Extract the initialisation vector and encrypted data
        $initVector         = substr($encryptedString, 0, $this->iv_num_bytes);
        $encryptedString    = substr($encryptedString, $this->iv_num_bytes);

        $decryptedString = openssl_decrypt($encryptedString, static::ENCRYPT_ALGO, $this->secretKey, OPENSSL_RAW_DATA, $initVector);
        if($decryptedString === false) {
            throw new EncryptionException("decrypt() failure: " . openssl_error_string());
        }

        if(!$unserialize) {
            return $decryptedString;
        }

        $arrData = @unserialize($decryptedString);
        if($arrData === false) {
            throw new \Exception('decrypt() failure: unable to unserialize');
        }

        return $arrData;
    }


    protected function preventLeaks()
    {
        if( empty($this->secretKey) || empty($this->iv_num_bytes) ) {
            throw new EncryptionException();
        }
    }
}
