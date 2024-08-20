<?php

/**
 * @package Omnipay\Digipay
 * @author Amirreza Salari <amirrezasalari1997@gmail.com>
 */

namespace Omnipay\Digipay\Message;

use Omnipay\Common\Message\RedirectResponseInterface;

class RefundOrderResponse extends AbstractResponse implements RedirectResponseInterface
{

    /**
     * @inheritDoc
     */
    public function getTransactionReference()
    {
        /** @var VerifyOrderRequest $request */
        $request = $this->data;
        return $request['trackingCode'];
    }


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
}