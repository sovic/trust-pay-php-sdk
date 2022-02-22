<?php

namespace TrustPay;

use InvalidArgumentException;

class TrustPay
{
    private const TYPE_CREDIT_CARD = 'CRDT';
    private const TYPE_DEBIT_CARD = 'DBIT';

    private const RESULT_CODES = [
        0 => 'Success',
        1 => 'Pending',
        2 => 'Announced',
        3 => 'Authorized',
        4 => 'Processing',
        5 => 'AuthorizedOnly',
        1001 => 'Invalid request',
        1002 => 'Unknown account',
        1003 => 'Merchant account disabled',
        1004 => 'Invalid signature',
        1005 => 'User cancel',
        1006 => 'Invalid authentication',
        1007 => 'Insufficient balance',
        1008 => 'Service not allowed',
        1009 => 'Processing ID used',
        1010 => 'Transaction not found',
        1011 => 'Unsupported transaction',
        1014 => 'Rejected transaction',
        1100 => 'General Error',
        1101 => 'Unsupported currency conversion',
    ];

    private const BANK_PAYMENT_URL = 'https://amapi.trustpay.eu/mapi5/wire/paypopup';
    private const CARD_PAYMENT_URL = 'https://amapi.trustpay.eu/mapi5/Card/PayPopup';

    private int $accountId;
    private string $secret;

    public function __construct(int $accountId, string $secret)
    {
        $this->accountId = $accountId;
        $this->secret = $secret;
    }

    public function buildBankPaymentUrl(TrustPayPayment $payment, string $notificationUrl): string
    {
        $signatureData = sprintf(
            "%d/%s/%s/%s/%d",
            $this->accountId,
            TrustPayHelper::formatAmount($payment->getAmount()),
            $payment->getCurrency(),
            $payment->getClientPaymentId(),
            $payment->getType(),
        );
        $signature = TrustPayHelper::signMessage($signatureData, $this->secret);

        $query = sprintf(
            'AccountId=%d&Amount=%s&Currency=%s&Reference=%s&PaymentType=%d&Signature=%s&NotificationUrl=%s',
            $this->accountId,
            TrustPayHelper::formatAmount($payment->getAmount()),
            $payment->getCurrency(),
            urlencode($payment->getClientPaymentId()),
            $payment->getType(),
            $signature,
            urlencode($notificationUrl),
        );

        return self::BANK_PAYMENT_URL . '?' . $query;
    }

    public function buildCardPaymentUrl(TrustPayPayment $payment, string $notificationUrl): string
    {
        if (null === $payment->getBillingCity()) {
            throw new InvalidArgumentException('Invalid BillingCity [TrustPayPayment::setBillingCity]');
        }
        if (null === $payment->getBillingCountry()) {
            throw new InvalidArgumentException('Invalid BillingCountry [TrustPayPayment::setBillingCountry]');
        }
        if (null === $payment->getBillingPostCode()) {
            throw new InvalidArgumentException('Invalid BillingPostCode [TrustPayPayment::setBillingPostCode]');
        }
        if (null === $payment->getBillingStreet()) {
            throw new InvalidArgumentException('Invalid BillingStreet [TrustPayPayment::setBillingStreet]');
        }
        if (null === $payment->getCardHolder()) {
            throw new InvalidArgumentException('Invalid CardHolder [TrustPayPayment::setCardHolder]');
        }
        if (null === $payment->getEmail()) {
            throw new InvalidArgumentException('Invalid Email [TrustPayPayment::setEmail]');
        }

        $signatureData = sprintf(
            "%d/%s/%s/%s/%d/%s/%s/%s/%s/%s/%s",
            $this->accountId,
            TrustPayHelper::formatAmount($payment->getAmount()),
            $payment->getCurrency(),
            $payment->getClientPaymentId(), // reference
            $payment->getType(),
            $payment->getBillingCity(),
            $payment->getBillingCountry(),
            $payment->getBillingPostCode(),
            $payment->getBillingStreet(),
            $payment->getCardHolder(),
            $payment->getEmail(),
        );
        $signature = TrustPayHelper::signMessage($signatureData, $this->secret);

        $query = sprintf(
            'AccountId=%d&Amount=%s&Currency=%s&Reference=%s&PaymentType=%d&Signature=%s&BillingCity=%s&BillingCountry=%s&BillingPostcode=%s&BillingStreet=%s&CardHolder=%s&Email=%s&NotificationUrl=%s',
            $this->accountId,
            TrustPayHelper::formatAmount($payment->getAmount()),
            $payment->getCurrency(),
            urlencode($payment->getClientPaymentId()), // reference
            $payment->getType(),
            $signature,
            $payment->getBillingCity(),
            $payment->getBillingCountry(),
            $payment->getBillingPostCode(),
            $payment->getBillingStreet(),
            $payment->getCardHolder(),
            $payment->getEmail(),
            urlencode($notificationUrl),
        );

        return self::CARD_PAYMENT_URL . '?' . $query;
    }

    /**
     * Validates request for API v2
     *
     * @param array $query
     * @return TrustPayPayment
     */
    public function validateCardPaymentRequestQuery(array $query): TrustPayPayment
    {
        if (empty($query['AccountId']) || (int) $query['AccountId'] <= 0) {
            throw new InvalidArgumentException('Missing AccountId', 1);
        }
        if ($this->accountId !== (int) $query['AccountId']) {
            throw new InvalidArgumentException('Invalid AccountId', 2);
        }
        $accountId = $query['AccountId'];

        if (!in_array($query['Type'], [self::TYPE_CREDIT_CARD, self::TYPE_DEBIT_CARD], true)) {
            throw new InvalidArgumentException('Missing Type', 2);
        }
        $type = $query['Type'];

        if (empty($query['Amount']) || (float) $query['Amount'] <= 0) {
            throw new InvalidArgumentException('Missing AMT');
        }
        $amount = (float) $query['Amount'];

        if (empty($query['Currency'])) {
            throw new InvalidArgumentException('Missing Currency');
        }
        $currency = $query['Currency'];

        if (empty($query['Reference'])) {
            throw new InvalidArgumentException('Missing Reference');
        }
        $clientPaymentId = $query['Reference'];

        if (!array_key_exists((int) $query['ResultCode'], self::RESULT_CODES)) {
            throw new InvalidArgumentException('Missing ResultCode');
        }
        $resultCode = $query['ResultCode'];

        if (empty($query['PaymentRequestId'])) {
            throw new InvalidArgumentException('Missing PaymentRequestId');
        }
        $trustPayPaymentId = (int) $query['PaymentRequestId'];

        if (empty($query['Signature'])) {
            throw new InvalidArgumentException('Missing Signature');
        }
        $signature = $query['Signature'];

        $signatureData = [
            $accountId,
            TrustPayHelper::formatAmount($amount),
            $currency,
            $clientPaymentId,
            $type,
            $resultCode,
            $trustPayPaymentId,
        ];
        $optional = ['CardId', 'CardMask', 'CardExpiration', 'AuthNumber'];
        foreach ($optional as $key) {
            if (!empty($query[$key])) {
                $signatureData[] = $query[$key];
            }
        }

        $signedData = TrustPayHelper::signMessage(implode('/', $signatureData), $this->secret);
        if ($signedData !== $signature) {
            throw new InvalidArgumentException('Invalid signature');
        }

        $trustPayPayment = new TrustPayPayment();
        $trustPayPayment->setClientPaymentId($clientPaymentId);
        $trustPayPayment->setTrustPayPaymentId($trustPayPaymentId);
        $trustPayPayment->setAmount($amount);
        $trustPayPayment->setCurrency($currency);
        $trustPayPayment->setResultCode($resultCode);

        return $trustPayPayment;
    }
}
