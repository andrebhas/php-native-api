<?php

namespace Src\System\Encryption;

class Cryptor
{
    protected $method = 'aes-128-ctr';
    private $key;

    protected function ivBytes()
    {
        return openssl_cipher_iv_length($this->method);
    }

    public function __construct($key = false, $method = false)
    {
        if (!$key) {
            $key = php_uname(); // default encryption key if none supplied
        }
        if (ctype_print($key)) {

            $this->key = openssl_digest($key, 'SHA256', true);
        } else {
            $this->key = $key;
        }
        if ($method) {
            if (in_array(strtolower($method), openssl_get_cipher_methods())) {
                $this->method = $method;
            } else {
                die(__METHOD__ . ": unrecognised cipher method: {$method}");
            }
        }
    }

    public function encrypt($data)
    {
        $iv = openssl_random_pseudo_bytes($this->ivBytes());
        return bin2hex($iv) . openssl_encrypt($data, $this->method, $this->key, 0, $iv);
    }

    public function decrypt($data)
    {
        $iv_strlen = 2  * $this->ivBytes();
        if (preg_match("/^(.{" . $iv_strlen . "})(.+)$/", $data, $regs)) {
            list(, $iv, $crypted_string) = $regs;
            if (ctype_xdigit($iv) && (strlen($iv) % 2 == 0)) {
                return openssl_decrypt($crypted_string, $this->method, $this->key, 0, hex2bin($iv));
            }
        }
        return false; // failed to decrypt
    }
}
