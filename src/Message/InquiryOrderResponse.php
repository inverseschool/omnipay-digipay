<?php

/**
 * @package Omnipay\Digipay
 * @author Amirreza Salari <amirrezasalari1997@gmail.com>
 */

namespace Omnipay\Digipay\Message;

/**
 * Class InquiryOrderResponse
 */
class InquiryOrderResponse extends AbstractResponse
{
    /**
     * Return order status; possible values: SUCCESS, FAILED, UNKNOWN, EXPIRED, IN_PROGRESS
     * @return string
     */
    public function getStatus(): string
    {
        return $this->data['status'];
    }

    /**
     * @inheritDoc
     */
    public function isSuccessful()
    {
        return (int)$this->getHttpStatus() === 200 && $this->data['status'] === 'SUCCESS';
    }

    /**
     * @inheritDoc
     */
    public function isCancelled()
    {
        return (int)$this->getHttpStatus() === 200 && in_array($this->data['status'], ['FAILED', 'EXPIRED'], true);
    }

    /**
     * @inheritDoc
     */
    public function isPending()
    {
        return (int)$this->getHttpStatus() === 200 && in_array($this->data['status'], ['UNKNOWN', 'IN_PROGRESS'], true);
    }
}
