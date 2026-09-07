<?php

namespace App\Models;

use App\Foundation\Base\BaseModel;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\Reservation\Enums\ReservationStatusEnum;
use Carbon\Carbon;

class Reservation extends BaseModel
{
    protected $fillable = [
        'customer',
        'state_registration',
        'phone',
        'email',
        'date',
        'hour',
        'quantity_of_person',
        'observation',
        'table_id',
        'restaurant_id',
        'created_by',
        'status',
        'duration',
        'waiter_id'
    ];

    protected $casts = [
        'status'    => ReservationStatusEnum::class
    ];

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function hour(): Attribute
    {
        return Attribute::make(
            get: fn($value) => substr($value, 0, 5)
        );
    }

    public function duration(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? str_replace(":", "H", substr($value, 0, 5)) : null
        );
    }

    public function waiter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'waiter_id');
    }

    #[Scope]
    protected function countByStatus(Builder $builder, ?ReservationStatusEnum $status = null, ?Carbon $date = null): Builder
    {
        $day = today()->format("Y-m-d");
        if ($date){
            $day = $date->format("Y-m-d");
        }
        $query = $builder->whereDate('date', $day);
        if($status){
            $query->where('status', $status->value);
        }
        return $query;
    }

    public function setAsSeated(): void
    {
        $this->update(['status' => ReservationStatusEnum::SEATED->value]);
    }

}
