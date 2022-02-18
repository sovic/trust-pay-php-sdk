# PHP SDK for TrustPay payment gateway

[![packagist](https://img.shields.io/github/v/release/sovic/trust-pay-php-sdk?style=flat-square&maxAge=2592000)]() [![license](https://img.shields.io/github/license/sovic/trust-pay-php-sdk?style=flat-square)]()

## Requirements

- PHP >= 7.4

## Installation

Using [Composer](https://getcomposer.org/doc/00-intro.md)

```bash
composer require sovic/trust-pay-php-sdk
```

## Usage

### Init

```php
$trustPay = new TrustPay('{account-id}', '{secret}');
```

### Create payment

```php
// get TrustPay gateway bank|card payment url for payment button
$url = $trustPay->buildBankPaymentUrl($trustPayPayment, '{notification-url}');
$url = $trustPay->buildCardPaymentUrl($trustPayPayment, '{notification-url}');
```

### Validate TrustPay status request

```php 
$query = [ … ]; // query params array from HTTP request

try {
    $trustPayPayment = $trustPay->validatePaymentRequestQuery($query);
    if ($trustPayPayment->isPaid()) {
        // handle successful payment
        $clientPaymentId = $trustPayPayment->getClientPaymentId(); // reference
    } else {
        // handle failed|cancelled payment
    }
} catch(Exception $e) {
    // invalid request, some parameter missing or invalid signature hash, output 400 Bad Request
}

// all OK, output 202 Accepted
```