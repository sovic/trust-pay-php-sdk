<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TrustPayRequestTest extends TestCase
{
    public function testTrustPayResponse(): void
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
