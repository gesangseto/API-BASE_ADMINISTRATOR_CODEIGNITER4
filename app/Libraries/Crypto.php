<?php

namespace App\Libraries;

class Crypto
{
    const METHOD = 'aes-256-ctr';

    public function encrypt($plaintext, $password, $salt = '', $encode = false)
    {
        $keyAndIV = self::evpKDF($password, $salt);

        $ciphertext = openssl_encrypt(
            $plaintext,
            self::METHOD,
            $keyAndIV["key"],
            OPENSSL_RAW_DATA,
            $keyAndIV["iv"]
        );
        $ciphertext = bin2hex($ciphertext);
        if ($encode) {
            $ciphertext = base64_encode($ciphertext);
        }
        return $ciphertext;
    }

    public function decrypt($ciphertext, $password, $salt = '', $encoded = false)
    {
        if ($encoded) {
            $ciphertext = base64_decode($ciphertext, true);
            if ($ciphertext === false) {
                return false;
            }
        }
        $ciphertext = hex2bin($ciphertext);
        $keyAndIV   = self::evpKDF($password, $salt);
        $plaintext = openssl_decrypt(
            $ciphertext,
            self::METHOD,
            $keyAndIV["key"],
            OPENSSL_RAW_DATA,
            $keyAndIV["iv"]
        );
        return $plaintext;
    }

    public function evpKDF($password, $salt, $keySize = 8, $ivSize = 4, $iterations = 1, $hashAlgorithm = "md5")
    {
        $targetKeySize = $keySize + $ivSize;
        $derivedBytes  = "";
        $numberOfDerivedWords = 0;
        $block         = NULL;
        $hasher        = hash_init($hashAlgorithm);
        while ($numberOfDerivedWords < $targetKeySize) {
            if ($block != NULL) {
                hash_update($hasher, $block);
            }
            hash_update($hasher, $password);
            hash_update($hasher, $salt);
            $block   = hash_final($hasher, TRUE);
            $hasher  = hash_init($hashAlgorithm);
            // Iterations
            for ($i = 1; $i < $iterations; $i++) {
                hash_update($hasher, $block);
                $block   = hash_final($hasher, TRUE);
                $hasher  = hash_init($hashAlgorithm);
            }
            $derivedBytes .= substr($block, 0, min(strlen($block), ($targetKeySize - $numberOfDerivedWords) * 4));
            $numberOfDerivedWords += strlen($block) / 4;
        }
        return array(
            "key" => substr($derivedBytes, 0, $keySize * 4),
            "iv"  => substr($derivedBytes, $keySize * 4, $ivSize * 4)
        );
    }
}
