<?php
namespace Payment;

use Phwoolcon\Payment\Model\Order;

trait PaymentApiTrait
{

    /**
     * @param Order $order
     * @return array
     */
    public function getApiOrderData($order)
    {
        return [
            'trade_id' => $order->getTradeId(),
            'order_id' => $order->getOrderId(),
            'amount' => $order->getAmount() * 1,
            'cash_to_pay' => $order->getData('cash_to_pay') * 1,
            'status' => $order->getStatus(),
        ];
    }
}
