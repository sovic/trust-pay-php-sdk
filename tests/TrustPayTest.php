<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TrustPay\TrustPay;
use TrustPay\TrustPayPayment;

final class TrustPayTest extends TestCase
{
    public function testBuildPaymentUrl(): void
    {
        $expected = 'https://amapi.trustpay.eu/mapi5/wire/paypopup?AccountId=1&Amount=6.00&Currency=EUR&Reference=1&NotificationUrl=https%3A%2F%2Fwebsite.com%2Fnotification&PaymentType=0&Signature=ABDF4A889E8C0D0DB297DE0A4DD6EF0BA59C405C4B7DD2A132EE5119E9D60DBB';

        $trustPay = new TrustPay(1, 'secret');
        $trustPayPayment = new TrustPayPayment();
        $trustPayPayment->setAmount(6.00);
        $trustPayPayment->setCurrency('EUR');
        $trustPayPayment->setClientPaymentId('1');
        $url = $trustPay->buildPaymentUrl($trustPayPayment, 'https://website.com/notification');

        $this->assertEquals($expected, $url);
    }
}
