<?php

/**
 * @package Omnipay\Digipay
 * @author Amirreza Salari <amirrezasalari1997@gmail.com>
 */

namespace Omnipay\Digipay\Message;

use Omnipay\Common\Exception\InvalidRequestException;

/**
 * Class CreateOrderRequest
 */
class CreateOrderRequest extends AbstractRequest
{
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
            'cellNumber' => $this->getPhoneNumber(),
            'amount' => (int)$this->getAmount(),
            'providerId' => $this->getTransactionId(),
            'callbackUrl' => $this->getReturnUrl(),
            'basketDetailsDto' => $this->getBasketDetail(),
        ];
    }

    /**
     * @param array $courses
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

    /**
     * @param int $cartId
     * @return static
     */
    public function setCartId(int $cartId)
    {
        return $this->setParameter('cartId', $cartId);
    }

    /**
     * @return string
     */
    protected function getCartId(): ?string
    {
        return $this->getParameter('cartId');
    }

    protected function getBasketDetail(): ?array
    {
        $products = $this->getProducts();

        foreach ($products as $product)
        {
            $data['items'] [] = [
                "productCode" => $product,
                "brand" => "Inverse",
                "productType" => 3,
                "count" => 1,
            ];
        }
        $data ["basketId"] =$this->getCartId();

        return $data;
    }

    /**
     * @param string $endpoint
     * @return string
     */
    protected function createUri(string $endpoint): ?string
    {
        return $endpoint . '/tickets/business?type=11';
    }

    /**
     * @param array $data
     * @return CreateOrderResponse
     */
    protected function createResponse(array $data): CreateOrderResponse
    {
        return new CreateOrderResponse($this, $data);
    }
}
