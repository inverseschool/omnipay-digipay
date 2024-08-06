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
class DeliverOrderRequest extends AbstractRequest
{

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
     */
    public function getData()
    {
        return [
            'deliveryDate' => strtotime('now'),
            'invoiceNumber' => rand(1111111111111, 9999999999999),
            'trackingCode' => $this->getTransactionReference(),
            'products' => $this->getBasketDetail()
        ];
    }

    /**
     * @param array $products
     * @return static
     */
    public function setProducts(array $products)
    {
        return $this->setParameter('products', $products);
    }

    /**
     * @return array
     */
    protected function getProducts(): ?array
    {
        return $this->getParameter('products');
    }

    protected function getBasketDetail(): ?array
    {
        $products = $this->getProducts();

        $data = [];
        foreach ($products as $product)
        {
            $data[] = 'product-'.$product;
        }

        return $data;
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function createUri(string $endpoint)
    {
        return $endpoint . '/purchases/deliver?type=' . $this->getType();
    }

    /**
     * @param array $data
     * @return DeliverOrderResponse
     */
    protected function createResponse(array $data)
    {
        return new DeliverOrderResponse($this, $data);
    }
}