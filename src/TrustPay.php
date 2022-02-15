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

    private int $accountId;

    public function __construct(int $accountId)
    {
        $this->accountId = $accountId;
    }

    public function validatePaymentRequestQuery(array $query): TrustPayPayment
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
        $type = (int) $query['Type'];

        if (empty($query['Amount']) || (float) $query['Amount'] <= 0) {
            throw new InvalidArgumentException('Missing Amount');
        }
        $amount = (float) $query['Amount'];

        if (empty($query['Currency'])) {
            throw new InvalidArgumentException('Missing Currency');
        }
        $currency = $query['Currency'];

        $clientPaymentId = null;
        if (!empty($query['Reference'])) {
            $clientPaymentId = $query['Reference'];
        }

        if (empty($query['PaymentId'])) {
            throw new InvalidArgumentException('Missing PaymentId');
        }
        $trustPayPaymentId = (int) $query['PaymentId'];

        if (empty($query['ResultCode']) || !array_key_exists((int) $query['ResultCode'], self::RESULT_CODES)) {
            throw new InvalidArgumentException('Missing ResultCode');
        }
        $resultCode = $query['ResultCode'];

        $trustPayOrderId = null;
        if (!empty($query['OrderId'])) {
            $trustPayOrderId = (int) $query['OrderId'];
        }

        if (empty($query['Signature'])) {
            throw new InvalidArgumentException('Missing Signature');
        }
        $signature = $query['Signature'];

        $trustPayPayment = new TrustPayPayment();
        $trustPayPayment->setClientPaymentId($clientPaymentId);
        $trustPayPayment->setTrustPayPaymentId($trustPayPaymentId);
        $trustPayPayment->setType($type);
        $trustPayPayment->setAmount($amount);
        $trustPayPayment->setCurrency($currency);
        $trustPayPayment->setResultCode($resultCode);
        $trustPayPayment->setTrustPayOrderId($trustPayOrderId);

        return $trustPayPayment;
    }
}
