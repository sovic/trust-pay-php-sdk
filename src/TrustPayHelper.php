<?php

namespace TrustPay;

class TrustPayHelper
{
    public static function formatAmount(float $amount): string
    {
        return number_format($amount, 2, '.', '');
    }

    public static function signMessage(string $message, string $key): string
    {
        return strtoupper(hash_hmac('sha256', pack('A*', $message), pack('A*', $key)));
    }
}