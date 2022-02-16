<?php

namespace TrustPay;

class TrustPayHelper
{
    public static function signMessage(string $message, string $key): string
    {
        return strtoupper(hash_hmac('sha256', pack('A*', $message), pack('A*', $key)));
    }
}