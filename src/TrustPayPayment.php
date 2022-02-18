<?php

namespace TrustPay;

class TrustPayPayment
{
    public const DEFAULT_CURRENCY = 'EUR';

    private ?string $clientPaymentId;
    private int $trustPayPaymentId;
    private ?int $trustPayOrderId;

    private float $amount;
    private string $currency = self::DEFAULT_CURRENCY;
    private int $type = 0; // For purchase must be set to 0

    private int $resultCode;

    private ?string $counterAccount;
    private ?string $counterAccountName;

    public function getClientPaymentId(): ?string
    {
        return $this->clientPaymentId;
    }

    public function setClientPaymentId(?string $clientPaymentId): void
    {
        $this->clientPaymentId = $clientPaymentId;
    }

    public function getTrustPayPaymentId(): int
    {
        return $this->trustPayPaymentId;
    }

    public function setTrustPayPaymentId(int $trustPayPaymentId): void
    {
        $this->trustPayPaymentId = $trustPayPaymentId;
    }

    public function getTrustPayOrderId(): ?int
    {
        return $this->trustPayOrderId;
    }

    public function setTrustPayOrderId(?int $trustPayOrderId): void
    {
        $this->trustPayOrderId = $trustPayOrderId;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): void
    {
        $this->type = $type;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): void
    {
        $this->amount = $amount;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getResultCode(): int
    {
        return $this->resultCode;
    }

    public function setResultCode(int $resultCode): void
    {
        $this->resultCode = $resultCode;
    }

    public function getCounterAccount(): ?string
    {
        return $this->counterAccount;
    }

    public function setCounterAccount(?string $counterAccount): void
    {
        $this->counterAccount = $counterAccount;
    }

    public function getCounterAccountName(): ?string
    {
        return $this->counterAccountName;
    }

    public function setCounterAccountName(?string $counterAccountName): void
    {
        $this->counterAccountName = $counterAccountName;
    }

    /**
     * Please note that only result codes 0, 3 and 4 received in a notification can be treated
     * as a successfully executed payment which has been or is guaranteed
     * to be credited to merchant’s account in TrustPay.
     *
     * @see https://doc.trustpay.eu/?aspxerrorpath=/v02#codes-result
     *
     * @return bool
     */
    public function isPaid(): bool
    {
        return isset($this->resultCode) && in_array($this->resultCode, [0, 3, 4]);
    }
}
