<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class TrustPayRequestTest extends TestCase
{
    public function testTrustPayResponse(): void
    {
        $accountId = 4107111111;
        $query = [
            'AccountId' => 4107111111,
            'Type' => 'CRDT',
            'Amount' => 6.00,
            'Currency' => 'EUR',
            'Reference' => '1234567890',
            'ResultCode' => 0,
            'PaymentId' => '177561',
            'Signature' => 'DA287DEA7F3898EF861F289553B7626EC829CC9BA401D45F275BDF86DFBF3A43',
            'CounterAccount' => 'SK3399520000002107425307',
            'CounterAccountName' => 'TestAccount',
        ];
        $trustPay = new TrustPay\TrustPay($accountId, 'secret');
        $trustPayPayment = $trustPay->validatePaymentRequestQuery($query);

        $this->assertEquals($query['Type'], $trustPayPayment->getType());
        $this->assertEquals($query['Amount'], $trustPayPayment->getAmount());
        $this->assertEquals($query['Currency'], $trustPayPayment->getCurrency());
        $this->assertEquals($query['PaymentId'], $trustPayPayment->getTrustPayPaymentId());
        $this->assertEquals($query['ResultCode'], $trustPayPayment->getResultCode());
    }
}
