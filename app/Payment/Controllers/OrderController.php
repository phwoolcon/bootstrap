<?php
namespace Payment\Controllers;

use Exception;
use Phwoolcon\Auth\Auth;
use Phwoolcon\Config;
use Phwoolcon\Controller;
use Phwoolcon\Log;
use Phwoolcon\Model\User;
use Phwoolcon\Payment\Exception\GeneralException;
use Phwoolcon\Payment\Exception\OrderException;
use Phwoolcon\Payment\Model\Order;
use Phwoolcon\Payment\Process\Payload;
use Phwoolcon\Payment\Processor;

class OrderController extends Controller
{

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

    public function getForm()
    {
        $gateways = Config::get('payment.gateways');
        $paymentMethod = explode('.', $this->input('payment_method'));
        // TODO use real quote model
        $quote = [
            'quote_id' => md5(microtime()),
            'amount' => 0.01,
            'user' => Auth::getUser(),
            'brief_description' => 'Test product',
            'client_id' => 'test_client',
            'gateway' => fnGet($paymentMethod, 0),
            'method' => fnGet($paymentMethod, 1),
        ];
        $this->render('payment', 'form', compact('gateways', 'quote'));
    }

    /**
     * @param $quote
     * @return Order|string
     */
    protected function placeOrder($quote)
    {
        /* @var User $user */
        $user = $quote['user'];
        try {
            $paymentMethod = explode('.', $this->input('payment_method'));
            $payload = Processor::run(Payload::create([
                'gateway' => fnGet($paymentMethod, 0),
                'method' => fnGet($paymentMethod, 1),
                'action' => 'payRequest',
                'data' => [
                    'trade_id' => $quote['quote_id'],
                    'product_name' => $quote['brief_description'],
                    'client_id' => $quote['client_id'],
                    'user_identifier' => $user ? $user->getId() : 'guest',
                    'username' => $user ? $user->getUsername() : 'Guest',
                    'amount' => $quote['amount'],
                ],
            ]));
            $result = $payload->getResult();
            if ($order = $result->getOrder()) {
                return $order;
            }
            $error = $result->getError();
        } catch (GeneralException $e) {
            Log::error($e->getMessage());
            $error = ['code' => $e->getCode(), 'message' => __('Invalid payment method')];
        } catch (OrderException $e) {
            Log::error($e->getMessage());
            $error = ['code' => $e->getCode(), 'message' => $e->getMessage()];
        } catch (Exception $e) {
            Log::exception($e);
            $error = ['code' => 500, 'message' => __('Internal server error')];
        }
        return $error;
    }

    public function postPlace()
    {
        $paymentMethod = explode('.', $this->input('payment_method'));
        // TODO use real quote model
        $quote = [
            'quote_id' => md5(microtime()),
            'amount' => 0.01,
            'user' => Auth::getUser(),
            'brief_description' => 'Test product',
            'client_id' => 'test_client',
            'gateway' => fnGet($paymentMethod, 0),
            'method' => fnGet($paymentMethod, 1),
        ];
        $result = $this->placeOrder($quote);
        if ($result instanceof Order) {
            return $this->redirect($result->getPaymentGatewayUrl());
        }
        return $this->render('page', 'single-message', $result);
    }
}
