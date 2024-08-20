<?php

/**
 * @package Omnipay\Digipay
 * @author Amirreza Salari <amirrezasalari1997@gmail.com>
 */

namespace Omnipay\Digipay\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

/**
 * Class CreateOrderResponse
 */
class CreateOrderResponse extends AbstractResponse implements RedirectResponseInterface
{
    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return (int)$this->getHttpStatus() === 200 && $this->getCode() === 0;
    }

    /**
     * @inheritDoc
     */
    public function isCancelled()
    {
        return (int)$this->getHttpStatus() === 200 && $this->getCode() === 1;
    }

    /**
     * @inheritDoc
     */
    public function isRedirect()
    {
        return isset($this->data['redirectUrl']) && !empty($this->data['redirectUrl']);
    }

    /**
     * @inheritDoc
     */
    public function getRedirectUrl()
    {
        return $this->data['redirectUrl'];
    }

    /**
     * @inheritDoc
     */
    public function getTransactionReference()
    {
        return $this->data['ticket'];
    }
}
