<?php

namespace TrustPay;

class TrustPayPayment
{
    public const DEFAULT_CURRENCY = 'EUR';

    private ?string $clientPaymentId;
    private int $trustPayPaymentId;
    private ?int $trustPayOrderId;

    private string $type;
    private float $amount;
    private string $currency = self::DEFAULT_CURRENCY;

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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
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
}