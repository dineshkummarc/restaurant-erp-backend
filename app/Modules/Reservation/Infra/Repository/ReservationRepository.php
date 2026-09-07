<?php
namespace App\Modules\Reservation\Infra\Repository;

use App\Foundation\Base\BaseRepository;
use App\Http\Requests\PaginateRequest;
use App\Models\Reservation;
use App\Modules\Reservation\Enums\ReservationStatusEnum;
use App\Modules\Reservation\Infra\Filters\ReservationFilter;
use Carbon\Carbon;

class ReservationRepository extends BaseRepository
{

    protected function eloquent(): Reservation
    {
        return app(Reservation::class);
    }

    public function findAll(?PaginateRequest $paginate = null)
    {
        $query = $this->getQuery();
        $filter = new ReservationFilter($paginate);
        $filter->apply($query);
        return parent::findAll($paginate);
    }

    public function metas(?Carbon $date = null)
    {
        return [
            "total"     => Reservation::countByStatus(null, $date)->count(),
            "confirmed" => Reservation::countByStatus(ReservationStatusEnum::CONFIRMED, $date)->count(),
            "pending"   => Reservation::countByStatus(ReservationStatusEnum::PENDING, $date)->count(),
            "seated"    => Reservation::countByStatus(ReservationStatusEnum::SEATED, $date)->count()
        ];
    }

    public function findByTableAndDate(int $tableId, string $date): ?Reservation
    {
        return $this->getQuery()
            ->where('table_id', $tableId)
                ->whereDate('date', $date)
                    ->first();
    }
}
