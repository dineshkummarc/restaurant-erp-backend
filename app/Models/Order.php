<?php

namespace App\Models;

use App\Foundation\Base\BaseModel;
use App\Modules\Order\Enums\OrderStatusEnum;
use App\Modules\Payment\Enums\PaymentMethodEnum;
use App\Modules\Payment\Enums\PaymentStatusEnum;
use App\Policies\OrderPolicy;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Attributes\UsePolicy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[UsePolicy(OrderPolicy::class)]
class Order extends BaseModel
{
    protected $fillable = [
        "restaurant_id",
        "waiter_id",
        "table_id",
        "customer_name",
        "status",
        "payment_status",
        "payment_method",
        "observation",
        "fiscal_document_id",
        "fiscal_status",
        "parent_order_id",
        "transfert_reason",
        "close_at",
        "business_day",
        "created_by",
        "customers_quantity",
        "reservation_id"
    ];

    protected $casts = [
        "payment_status"    => PaymentStatusEnum::class,
        "payment_method"    => PaymentMethodEnum::class,
        "status"            => OrderStatusEnum::class,
        'business_day'      => 'date'
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    public function cancelItems(): HasMany
    {
        return $this->hasMany(OrderItemCancellation::class);
    }

    public static function boot()
    {
        static::creating(function(Order $order) {
            if (!$order->status) {
                $order->status = OrderStatusEnum::OPEN;
                $order->payment_status = PaymentStatusEnum::PENDING;
            }
            $auth = $order->auth();
            if (!$order->waiter_id){
                $order->waiter_id = $auth->id;
            }
            if(!$order->created_by){
                $order->created_by = $auth->id;
            }
            $order->business_day = self::getBusinessDay();
        });
        parent::boot();
    }

    public function getTotal(): float
    {
        $items = $this->items;
        $total = 0;
        $items->each(function(OrderItem $item) use(&$total){
            $total += $item->effective_total;
        });
        return $total;
    }

    public static function getBusinessDay(?Carbon $dateTime = null): string
    {
        $now = $dateTime ?? now();
        if ($now->hour < 5) {
            return $now->subDay()->toDateString();
        }
        return $now->toDateString();
    }
}
