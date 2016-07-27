<?php
namespace Payment\Controllers\Api;

use Payment\Controllers\OrderController;
use Phwoolcon\Config;
use Phwoolcon\Controller\Api;
use Phwoolcon\Model\User;

class AlipayController extends OrderController
{
    use Api;

    public function getDemoRequestForm()
    {
        $this->addPageTitle(__('Payment API'));
        $gateways = Config::get('payment.gateways');
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
        $this->render('payment', 'alipay/demo-request-form', compact('gateways', 'quote'));
    }

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
        return $this->placeOrder($quote);
    }
}
