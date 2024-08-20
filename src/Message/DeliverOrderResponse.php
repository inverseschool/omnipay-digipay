<?php

namespace Omnipay\Digipay\Message;

class DeliverOrderResponse extends AbstractResponse
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
     * In case of pending, you must inquiry the order later
     * @return bool
     */
    public function isPending()
    {
        return (int)$this->getHttpStatus() === 200 && $this->getCode() === 2;
    }
}