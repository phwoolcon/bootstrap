<?php
namespace Payment\Controllers\Api;

use Payment\Controllers\OrderController;
use Payment\PaymentApiTrait;
use Phwoolcon\Controller\Api;
use Phwoolcon\Model\User;
use Phwoolcon\Payment\Model\Order;

class AlipayController extends OrderController
{
    use Api;
    use PaymentApiTrait;

    public function postRequest()
    {
        $user = new User();
        $user->setId($this->input('user_identifier'))
            ->setUsername($this->input('user_identifier'));
        $quote = [
            'quote_id' => $this->input('trade_id'),
            'amount' => $this->input('amount'),
            'brief_description' => $this->input('product_name'),
            'client_id' => $this->input('client_id'),
            'user' => $user,
        ];
        $result = $this->placeOrder($quote);
        if ($result instanceof Order) {
            return $this->jsonApiReturnData([
                'id' => $result->getId(),
                'type' => 'order',
                'attributes' => $this->getApiOrderData($result),
            ], [
                'payment_gateway_url' => $result->getPaymentGatewayUrl(),
            ]);
        }
        return $this->jsonApiReturnErrors([
            [
                'code' => $result['code'],
                'title' => $result['message'],
            ],
        ]);
    }
}
