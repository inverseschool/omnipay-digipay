<?php

/**
 * @package Omnipay\Digipay
 * @author Amirreza Salari <amirrezasalari1997@gmail.com>
 */

namespace Omnipay\Digipay;

use Omnipay\Common\AbstractGateway;
use Omnipay\Common\Message\RequestInterface;
use Omnipay\Digipay\Message\DeliverOrderRequest;
use Omnipay\Digipay\Message\InquiryOrderRequest;
use Omnipay\Digipay\Message\RefundOrderRequest;
use Omnipay\Digipay\Message\VerifyOrderRequest;
use Omnipay\Digipay\Message\CreateOrderRequest;

/**
 * Class Gateway
 */
class Gateway extends AbstractGateway
{
    /**
     * Get gateway display name
     *
     * This can be used by carts to get the display name for each gateway.
     * @return string
     */
    public function getName(): string
    {
        return 'Digipay';
    }

    /**
     * @return array
     */
    public function getDefaultParameters(): array
    {
        return [
            'testMode' => false,
            'clientSecret' => '',
        ];
    }

    /**
     * @inheritDoc
     */
    public function initialize(array $parameters = [])
    {
        parent::initialize($parameters);

        $this->setParameter('cache', new Cache($this->getName()));

        return $this;
    }


    /**
     * @return string
     */
    public function getApiKey(): ?string
    {
        return $this->getParameter('apiKey');
    }

    /**
     * @return string
     */
    public function getSecretKey(): ?string
    {
        return $this->getParameter('secretKey');
    }

    /**
     * @return string
     */
    public function getReturnUrl(): ?string
    {
        return $this->getParameter('returnUrl');
    }

    /**
     * @param string $value
     * @return self
     */
    public function setApiKey(string $value): self
    {
        return $this->setParameter('apiKey', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setSecretKey(string $value): self
    {
        return $this->setParameter('secretKey', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setReturnUrl(string $value): self
    {
        return $this->setParameter('returnUrl', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setUsername(string $value): self
    {
        return $this->setParameter('username', $value);
    }
    /**
     * @param string $value
     * @return self
     */
    public function setPassword(string $value): self
    {
        return $this->setParameter('password', $value);
    }
    /**
     * @param string $value
     * @return self
     */
    public function setClientId(string $value): self
    {
        return $this->setParameter('clientId', $value);
    }

    /**
     * @param string $value
     * @return self
     */
    public function setClientSecret(string $value): self
    {
        return $this->setParameter('clientSecret', $value);
    }


    /**
     * @param $options Extra options of the create order request
     * @return CreateOrderRequest
     */
    public function purchase(array $options = []): RequestInterface
    {
        return $this->createRequest(CreateOrderRequest::class, $options);
    }

    /**
     * @param array $options Extra options of the verify order request
     * @return VerifyOrderRequest
     */
    public function completePurchase(array $options = []): RequestInterface
    {
        return $this->createRequest(VerifyOrderRequest::class, $options);
    }

    /**
     * @param array $option
     * @return RequestInterface
     */
    public function deliver(array $option): RequestInterface
    {
        return $this->createRequest(DeliverOrderRequest::class, $option);
    }

    /**
     * @param array $options Extra options of the verify order request
     * @return VerifyOrderRequest
     */
    public function refund(array $options = []): RequestInterface
    {
        return $this->createRequest(RefundOrderRequest::class, $options);
    }

    /**
     * @param array $options Extra options of the inquiry order request
     * @return InquiryOrderRequest
     */
    public function fetchTransaction(array $options = []): RequestInterface
    {
        return $this->createRequest(InquiryOrderRequest::class, $options);
    }
}
