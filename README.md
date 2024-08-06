## Instalation

    composer require inverseschool/omnipay-digipay

## Example

###### Purchase

#### The result will be a redirect to the gateway or bank.

```php
    $this->gateway = new Gateway(
            new Client(new \Http\Adapter\Guzzle7\Client()),
    );
    $amount = 60;
    $customerPhone = '09xxxxxxxxx';
    
    /** @var CreateOrderResponse $response */
        $response = $this->gateway->purchase([
            'amount' => $amount,
            'phoneNumber' => $customerPhone,
            'transactionId' => rand(1111111, 99999999),
            'returnUrl' => 'http://localhost/return',
            'products' => [192, 193, 194],
            'cartId' => 180
        ])->send();
        
    if ($response->isSuccessful() && $response->isRedirect()) {
    // store the transaction reference to use in completePurchase()
    $transactionReference = $response->getTransactionReference();
    // Redirect to offsite payment gateway
    $response->redirect();
    } else {
        // Payment failed: display message to customer
        echo $response->getMessage();
    }

```
### Complete Purchase (Verify && Deliver)

```php
// Send purchase complete request
    $param= [
            'transactionReference' => '19259313601650191846745',
            'type' => 4,
    ];
    $response = $this->gateway->completePurchase($param)->send();
    
    if (!$response->isSuccessful() || $response->isCancelled()) {
        // Payment failed: display message to customer
        echo $response->getMessage();
    } else {
        $deliverResponse = $gateway->deliver([
        'transactionReference' => '19259313601650191846745',
        'type' => 13,
        'products' => [192,193,194]
        ])->send();
        // Payment was successful
        print_r($deliverResponse);
    }
```

### Refund Order

Refund an order by the $refNum:

```php
    $param= [
            'type' => 4,
            'amount' => 60,
            'transactionId' => 'asd23efawgdfyascdda', // transactionId
            'transactionReference' => 19259313601650191846745, // transactionReference
        ];
    /** @var ReverseTicketResponse $response */
    $response = $this->gateway->refund($param)->send();
    
    if ($response->isSuccessful()) {
        // Refund was successful
        print_r($response);
    } else {
        // Refund failed
        echo $response->getMessage();
    }
```
