<?php
namespace App\Modules\Order\UseCases;

use App\Models\Order;
use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Order\Exceptions\OrderException;
use App\Modules\Order\Infra\OrderRepository;
use App\Modules\Payment\Enums\PaymentMethodEnum;
use App\Modules\Payment\Enums\PaymentStatusEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

final class OrderUpdatePaymentMethodUseCase
{
    public function __construct(
        private readonly OrderRepository $orderRepository
    ){}

    public function execute(Order $order, int $paymentMethod)
    {
        Gate::authorize("update", $order);
        $method = PaymentMethodEnum::tryFrom($paymentMethod);
        if (!$method){
            throw new OrderException("Metodo de pagamento nao encontrado", 400);
        }
        if ($order->payment_status->isPaid()){
            throw new OrderException("O pedido já foi pago.", 400);
        }
        $payload = [
            'payment_status'    => PaymentStatusEnum::PAID->value,
            'payment_method'    => $method->value,
            'status'            => OrderStatusEnum::CLOSED->value
        ];
        DB::transaction(function () use ($order, $payload) {
            if($order->isFromReservation()){
                if(!$order->reservation->status->isFinished()){
                    $order->reservation->setAsFinished();
                }
            }
            $this->orderRepository->update($order, $payload);
        });
    }
}