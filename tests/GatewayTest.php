<?php

namespace Omnipay\Digipay\Tests;

use Omnipay\Digipay\Gateway;
use Omnipay\Digipay\Message\RefundOrderResponse;
use Omnipay\Digipay\Message\VerifyOrderResponse;
use Omnipay\Tests\GatewayTestCase;
use Omnipay\Digipay\Message\CreateOrderRequest;
use Omnipay\Digipay\Message\CreateOrderResponse;
use Omnipay\Tests\TestCase;

class GatewayTest extends TestCase
{
    /**
     * @var Gateway
     */
    protected $gateway;

    /**
     * @var array<string, integer|string|boolean>
     */
    protected $params;

    public function setUp(): void
    {
        parent::setUp();

        $this->gateway = new Gateway($this->getHttpClient(), $this->getHttpRequest());
        $this->gateway->setUsername('36528a8');
        $this->gateway->setPassword('Ppu');
        $this->gateway->setClientId('company-client-id');
        $this->gateway->setClientSecret('6545');
    }

    public function testPurchaseSuccess(): void
    {
        $this->setMockHttpResponse(['GetToken.txt','PurchaseSuccess.txt']);
        $amount = 60;
        $customerPhone = '09056619083';

        /** @var CreateOrderResponse $response */
        $response = $this->gateway->purchase([
            'amount' => $amount,
            'phoneNumber' => $customerPhone,
            'transactionId' => rand(1111111, 99999999),
            'returnUrl' => 'http://localhost/return',
            'products' => [192, 193, 194],
            'cartId' => 180
        ])->send();

        self::assertTrue($response->isSuccessful());
        self::assertTrue($response->isRedirect());
        self::assertEquals('v2:ab17ec383d654be3b009f9fc45202f80',$response->getTransactionReference());
        self::assertEquals('https://uatweb.mydigipay.info/web-pay/tgs/v2:ab17ec383d654be3b009f9fc45202f80', $response->getRedirectUrl());
    }


    public function testMessageResponseInCreatePurchase(): void
    {
        $this->setMockHttpResponse(['GetToken.txt','CreatePurchaseWith422Response.txt']);
        $amount = 60;
        $customerPhone = '09056619083';

        /** @var CreateOrderResponse $response */
        $response = $this->gateway->purchase([
            'amount' => $amount,
            'phoneNumber' => $customerPhone,
            'transactionId' => rand(1111111, 99999999),
            'returnUrl' => 'http://localhost/return',
            'products' => [192, 193, 194],
            'cartId' => 180
        ])->send();


        self::assertEquals(15805, $response->getCode());
        self::assertFalse($response->isSuccessful());
    }

    public function testPurchaseFailure(): void
    {
        $this->setMockHttpResponse(['GetToken.txt','PurchaseFailure.txt']);
        $amount = 60;
        $customerPhone = '09056619083';

        /** @var CreateOrderResponse $response */
        $response = $this->gateway->purchase([
            'amount' => $amount,
            'phoneNumber' => $customerPhone,
            'transactionId' => rand(1111111, 99999999),
            'returnUrl' => 'http://localhost/return',
            'products' => [192, 193, 194],
            'cartId' => 180
        ])->send();
        self::assertFalse($response->isSuccessful());
        self::assertTrue($response->isCancelled());
    }

    public function testCompletePurchaseSuccess(): void
    {
        $this->setMockHttpResponse(['GetToken.txt', 'PurchaseCompleteSuccess.txt']);
        $param= [
            'transactionReference' => '19259313601650191846745',
            'type' => 4,
        ];


        /** @var VerifyOrderResponse $response */
        $response = $this->gateway->completePurchase($param)->send();

        self::assertTrue($response->isSuccessful());
        self::assertEquals('CPG',$response->getPaymentGatewayTypes());
        self::assertEquals('132713002000200010',$response->getTransactionId());
        self::assertEquals('19259313601650191846745',$response->getTransactionReference());
    }

    public function testRefundPurchaseSuccess(): void
    {
        $this->setMockHttpResponse(['GetToken.txt', 'PurchaseRefundSuccess.txt']);
        $param= [
            'type' => 4,
            'amount' => 60,
            'providerId' => rand(1111111, 999999999),
            'saleTrackingCode' => 19259313601650191846745,
        ];


        /** @var RefundOrderResponse $response */
        $response = $this->gateway->refund($param)->send();

        self::assertTrue($response->isSuccessful());
        self::assertEquals('19259313601650191846745',$response->getTransactionReference());
    }




}