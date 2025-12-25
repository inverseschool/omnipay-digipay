<?php

/**
 * @package Omnipay\Digipay
 * @author Amirreza Salari <amirrezasalari1997@gmail.com>
 */

namespace Omnipay\Digipay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class VerifyOrderRequest
 */
class VerifyOrderRequest extends AbstractRequest
{
    /**
     * @return string
     */
    public function getState()
    {
        return $this->getParameter('state');
    }

    /**
     * @param string $state
     * @return self
     */
    public function setState(string $state)
    {
        return $this->setParameter('state', $state);
    }


    /**
     * @return string
     */
    public function getType()
    {
        return $this->getParameter('type');
    }

    /**
     * @param string $type
     * @return self
     */
    public function setType(string $type)
    {
        return $this->setParameter('type', $type);
    }
    /**
     * @inheritDoc
     */
    protected function getHttpMethod()
    {
        return 'POST';
    }

    /**
     * Get the raw data array for this message. The format of this varies from gateway to
     * gateway, but will usually be either an associative array, or a SimpleXMLElement.
     *
     * @return mixed
     * @throws InvalidRequestException
     */
    public function getData()
    {
        return [
            'providerId' => $this->getTransactionId(),
            'trackingCode' => $this->getTransactionReference()
        ];
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function createUri(string $endpoint)
    {
        return $endpoint . '/purchases/verify' . '?type=' . $this->getType();
    }

    /**
     * @param array $data
     * @return VerifyOrderResponse
     */
    protected function createResponse(array $data)
    {
        return new VerifyOrderResponse($this, $data);
    }

    /**
     * @param array|string ...$args a variable length list of required parameters
     * @return void
     * @throws InvalidRequestException
     */
    public function validate(...$args)
    {
        parent::validate(...$args);

        // verify callback params returned from payment gateway
        if ($this->getState() !== 'SUCCESS') {
            throw new InvalidRequestException("Payment was not successful");
        }
    }
}
