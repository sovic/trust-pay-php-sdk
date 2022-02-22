<?php

namespace TrustPay;

use InvalidArgumentException;

class TrustPayPayment
{
    public const DEFAULT_CURRENCY = 'EUR';

    private string $clientPaymentId;
    private int $trustPayPaymentId;
    private ?int $trustPayOrderId;

    private float $amount;
    private string $currency = self::DEFAULT_CURRENCY;
    private int $type = 0; // For purchase must be set to 0

    private int $resultCode;

    // bank payment context
    private ?string $counterAccount = null;
    private ?string $counterAccountName = null;

    // card payment context
    private ?string $billingCity = null;
    private ?string $billingCountry = null;
    private ?string $billingPostCode = null;
    private ?string $billingStreet = null;
    private ?string $cardHolder = null;
    private ?string $email = null;

    public function getClientPaymentId(): string
    {
        return $this->clientPaymentId;
    }

    public function setClientPaymentId(string $clientPaymentId): void
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

    public function getBillingCity(): ?string
    {
        return $this->billingCity;
    }

    public function setBillingCity(?string $billingCity): void
    {
        if (null !== $billingCity && ($billingCity === '' || strlen($billingCity) > 80)) {
            throw new InvalidArgumentException('invalid billingCity [string{1-80}]', 200);
        }
        $this->billingCity = $billingCity;
    }

    public function getBillingCountry(): ?string
    {
        return $this->billingCountry;
    }

    public function setBillingCountry(?string $billingCountry): void
    {
        if (null !== $billingCountry && (strlen($billingCountry) !== 2)) {
            throw new InvalidArgumentException('invalid billingCountry [string{2}]', 201);
        }
        $this->billingCountry = $billingCountry;
    }

    public function getBillingPostCode(): ?string
    {
        return $this->billingPostCode;
    }

    public function setBillingPostCode(?string $billingPostCode): void
    {
        if (null !== $billingPostCode && ($billingPostCode === '' || strlen($billingPostCode) > 30)) {
            throw new InvalidArgumentException('invalid billingPostCode [string{1-30}]', 202);
        }
        $this->billingPostCode = $billingPostCode;
    }

    public function getBillingStreet(): ?string
    {
        return $this->billingStreet;
    }

    public function setBillingStreet(?string $billingStreet): void
    {
        if (null !== $billingStreet && ($billingStreet === '' || strlen($billingStreet) > 100)) {
            throw new InvalidArgumentException('invalid billingStreet [string{1-100}]', 203);
        }
        $this->billingStreet = $billingStreet;
    }

    public function getCardHolder(): ?string
    {
        return $this->cardHolder;
    }

    public function setCardHolder(?string $cardHolder): void
    {
        if (null !== $cardHolder && (strlen($cardHolder) < 3 || strlen($cardHolder) > 140)) {
            throw new InvalidArgumentException('invalid cardHolder [string{3-140}]', 204);
        }
        $this->cardHolder = $cardHolder;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        if (null !== $email && ($email === '' || strlen($email) > 254)) {
            throw new InvalidArgumentException('invalid email [string{1-254}]', 205);
        }
        $this->email = $email;
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
