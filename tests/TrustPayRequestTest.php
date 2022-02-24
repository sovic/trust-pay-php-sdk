<?php

/** @noinspection SpellCheckingInspection */

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TrustPayRequestTest extends TestCase
{
    public function testTrustPayBankResponse(): void
    {
        $accountId = 4107408721;
        $secret = 'secret';
        $query = [
            'AccountId' => '4107408721',
            'Amount' => '5.90',
            'Currency' => 'EUR',
            'PaymentId' => '17466360',
            'Reference' => '204',
            'ResultCode' => '0',
            'Signature' => '155FC1D4599ECCEE22C5608476A2DA096BE91D994A4C9032FC37528BEA535CE1',
            'Type' => 'CRDT',
        ];

        $trustPay = new TrustPay\TrustPay($accountId, $secret);
        $trustPayPayment = $trustPay->validateBankPaymentRequestQuery($query);

        $this->assertEquals($query['Amount'], $trustPayPayment->getAmount());
        $this->assertEquals($query['Currency'], $trustPayPayment->getCurrency());
        $this->assertEquals($query['PaymentId'], $trustPayPayment->getTrustPayPaymentId());
        $this->assertEquals($query['ResultCode'], $trustPayPayment->getResultCode());
    }

    public function testTrustPayCardResponse(): void
    {
        $accountId = 4107408721;
        $secret = 'secret';

        $query = [
            'AccountId' => '4107408721',
            'Amount' => '5.90',
            'CardExpiration' => '0223',
            'CardMask' => '420000******1234',
            'Currency' => 'EUR',
            'PaymentRequestId' => '846487',
            'Reference' => '172',
            'ResultCode' => '3',
            'Signature' => '348DA492F42D7E88144DC5DF585DA42568965EB0EB578EE3115D7633020DDD7F',
            'Type' => 'CRDT',
        ];

        $trustPay = new TrustPay\TrustPay($accountId, $secret);
        $trustPayPayment = $trustPay->validateCardPaymentRequestQuery($query);

        $this->assertEquals($query['Amount'], $trustPayPayment->getAmount());
        $this->assertEquals($query['Currency'], $trustPayPayment->getCurrency());
        $this->assertEquals($query['PaymentRequestId'], $trustPayPayment->getTrustPayPaymentId());
        $this->assertEquals($query['ResultCode'], $trustPayPayment->getResultCode());
    }
}
