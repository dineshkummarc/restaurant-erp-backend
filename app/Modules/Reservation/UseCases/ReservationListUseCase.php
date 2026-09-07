<?php
namespace App\Modules\Reservation\UseCases;

use App\Http\Requests\PaginateRequest;
use App\Http\Resources\Reservation\ReservationResource;
use App\Modules\Reservation\Infra\Repository\ReservationRepository;
use Illuminate\Http\Resources\Json\JsonResource;

final class ReservationListUseCase
{

    public function __construct(
        private readonly ReservationRepository $reservationRepository
    ){}

    public function execute(PaginateRequest $paginate)
    {
        $collection = ReservationResource::collection(
            $this->reservationRepository->findAll($paginate)
        );
        return response()->json([
            'data' => $collection->collection->groupBy(
                fn($res) => explode(':', $res->hour)[0]
            ),
            'meta' => $this->reservationRepository->metas()
        ]);
    }

    public function listById(int $id)
    {
        return new ReservationResource(
            $this->reservationRepository->find($id)
        );
    }

    public function listByTableAndDate(int $tableId, string $date): ReservationResource
    {
        return new ReservationResource(
            $this->reservationRepository->findByTableAndDate($tableId, $date)
        );
    }
}
