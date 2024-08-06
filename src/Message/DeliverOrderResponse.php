<?php

namespace Omnipay\Digipay\Message;

class DeliverOrderResponse extends AbstractResponse
{



    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return (int)$this->getCode() === 200 && $this->data['result']['status'] === 0;
    }

    /**
     * @inheritDoc
     */
    public function isCancelled()
    {
        return (int)$this->getCode() === 200 && $this->data['result']['status'] === 1;
    }

    /**
     * In case of pending, you must inquiry the order later
     * @return bool
     */
    public function isPending()
    {
        return (int)$this->getCode() === 200 && $this->data['result']['status'] === 2;
    }
}