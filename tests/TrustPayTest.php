<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TrustPay\TrustPay;
use TrustPay\TrustPayPayment;

final class TrustPayTest extends TestCase
{
    private const BANK_PAYMENT_URL = 'https://amapi.trustpay.eu/mapi5/wire/paypopup';
    private const CARD_PAYMENT_URL = 'https://amapi.trustpay.eu/mapi5/Card/PayPopup';

    public function testBuildBankPaymentUrl(): void
    {
        /** @noinspection SpellCheckingInspection */
        $expected = preg_replace('/\s+/', '', self::BANK_PAYMENT_URL . '?
            AccountId=1&
            Amount=6.00&
            Currency=EUR&
            Reference=1&
            PaymentType=0&
            Signature=ABDF4A889E8C0D0DB297DE0A4DD6EF0BA59C405C4B7DD2A132EE5119E9D60DBB&
            NotificationUrl=https%3A%2F%2Fwebsite.com%2Fnotification
        ');

        $trustPay = new TrustPay(1, 'secret');
        $trustPayPayment = new TrustPayPayment();
        $trustPayPayment->setAmount(6.00);
        $trustPayPayment->setCurrency('EUR');
        $trustPayPayment->setClientPaymentId('1');
        $url = $trustPay->buildBankPaymentUrl($trustPayPayment, 'https://website.com/notification');

        $this->assertEquals($expected, $url);
    }

    public function testBuildCardPaymentUrl(): void
    {
        /** @noinspection SpellCheckingInspection */
        $expected = preg_replace('/\s+/', '', self::CARD_PAYMENT_URL . '?
            AccountId=1&
            Amount=6.00&
            Currency=EUR&
            Reference=1&
            PaymentType=0&
            Signature=978157CC5999F143D9BCEF9F6057A5CB1FAD9386EA0D7443704DD4C7DE8F0E5E&
            BillingCity=City&
            BillingCountry=CZ&
            BillingPostcode=12345&
            BillingStreet=Street&
            CardHolder=Name&
            Email=name@user.com&
            NotificationUrl=https%3A%2F%2Fwebsite.com%2Fnotification
        ');

        $trustPay = new TrustPay(1, 'secret');
        $trustPayPayment = new TrustPayPayment();
        $trustPayPayment->setAmount(6.00);
        $trustPayPayment->setCurrency('EUR');
        $trustPayPayment->setClientPaymentId('1');
        $trustPayPayment->setBillingCity('City');
        $trustPayPayment->setBillingCountry('CZ');
        $trustPayPayment->setBillingPostCode('12345');
        $trustPayPayment->setBillingStreet('Street');
        $trustPayPayment->setCardHolder('Name');
        $trustPayPayment->setEmail('name@user.com');
        $url = $trustPay->buildCardPaymentUrl($trustPayPayment, 'https://website.com/notification');

        $this->assertEquals($expected, $url);
    }
}
