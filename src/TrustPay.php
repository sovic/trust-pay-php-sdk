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

    private const BASE_URL = 'https://amapi.trustpay.eu/mapi5/wire/paypopup';

    private int $accountId;
    private string $secret;

    public function __construct(int $accountId, string $secret)
    {
        $this->accountId = $accountId;
        $this->secret = $secret;
    }

    public function buildPaymentUrl(TrustPayPayment $payment, string $notificationUrl): string
    {
        $paymentType = 0;

        $signatureData = sprintf(
            "%d/%s/%s/%s/%d",
            $this->accountId,
            number_format($payment->getAmount(), 2, '.', ''),
            $payment->getCurrency(),
            $payment->getClientPaymentId(),
            $paymentType
        );
        $signature = TrustPayHelper::signMessage($signatureData, $this->secret);

        $query = sprintf(
            'AccountId=%d&Amount=%s&Currency=%s&Reference=%s&NotificationUrl=%s&PaymentType=%d&Signature=%s',
            $this->accountId,
            number_format($payment->getAmount(), 2, '.', ''),
            $payment->getCurrency(),
            urlencode($payment->getClientPaymentId()),
            urlencode($notificationUrl),
            $paymentType,
            $signature
        );

        return self::BASE_URL . '?' . $query;
    }

    public function validatePaymentRequestQuery(array $query): TrustPayPayment
    {
        if (empty($query['AID']) || (int) $query['AID'] <= 0) {
            throw new InvalidArgumentException('Missing AID', 1);
        }
        if ($this->accountId !== (int) $query['AID']) {
            throw new InvalidArgumentException('Invalid AID', 2);
        }
        $accountId = $query['AID'];

        if (!in_array($query['TYP'], [self::TYPE_CREDIT_CARD, self::TYPE_DEBIT_CARD], true)) {
            throw new InvalidArgumentException('Missing TYP', 2);
        }
        $type = $query['TYP'];

        if (empty($query['AMT']) || (float) $query['AMT'] <= 0) {
            throw new InvalidArgumentException('Missing AMT');
        }
        $amount = (float) $query['AMT'];

        if (empty($query['CUR'])) {
            throw new InvalidArgumentException('Missing CUR');
        }
        $currency = $query['CUR'];

        $clientPaymentId = null;
        if (!empty($query['REF'])) {
            $clientPaymentId = $query['REF'];
        }

        if (empty($query['TID'])) {
            throw new InvalidArgumentException('Missing TID');
        }
        $trustPayPaymentId = (int) $query['TID'];

        if (!array_key_exists((int) $query['RES'], self::RESULT_CODES)) {
            throw new InvalidArgumentException('Missing RES');
        }
        $resultCode = $query['RES'];

        $trustPayOrderId = null;
        if (!empty($query['OID'])) {
            $trustPayOrderId = (int) $query['OID'];
        }

        if (empty($query['SIG'])) {
            throw new InvalidArgumentException('Missing SIG');
        }
        $signature = $query['SIG'];

        $counterAccount = null;
        if (!empty($query['CounterAccount'])) {
            $counterAccount = $query['CounterAccount'];
        }

        $counterAccountName = null;
        if (!empty($query['CounterAccountName'])) {
            $counterAccountName = $query['CounterAccountName'];
        }

        if ($clientPaymentId) {
            $signatureData = [
                $accountId,
                $amount,
                $currency,
                $clientPaymentId,
                $type,
            ];
        } else {
            $signatureData = [
                $accountId,
                $amount,
                $currency,
                $type,
            ];
        }
        $signedData = TrustPayHelper::signMessage(implode('|', $signatureData), $this->secret);
        // TODO signature verification check
//        if ($signedData !== $signature) {
//            throw new InvalidArgumentException('Invalid signature');
//        }

        $trustPayPayment = new TrustPayPayment();
        $trustPayPayment->setClientPaymentId($clientPaymentId);
        $trustPayPayment->setTrustPayPaymentId($trustPayPaymentId);
        $trustPayPayment->setTrustPayOrderId($trustPayOrderId);

        $trustPayPayment->setType($type);
        $trustPayPayment->setAmount($amount);
        $trustPayPayment->setCurrency($currency);
        $trustPayPayment->setResultCode($resultCode);

        $trustPayPayment->setCounterAccount($counterAccount);
        $trustPayPayment->setCounterAccountName($counterAccountName);

        return $trustPayPayment;
    }
}
