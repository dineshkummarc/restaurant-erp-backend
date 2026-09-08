<?php
namespace App\Modules\Reservation\UseCases;

use App\Http\Requests\Reservation\ReservationCreateRequest;
use App\Models\Table;
use App\Modules\Reservation\Exceptions\ReservationException;
use App\Modules\Reservation\Infra\Repository\ReservationRepository;
use App\Modules\Table\Exceptions\TableException;
use App\Modules\Table\Repository\TableRepository;

final class ReservationCreateUseCase
{
    public function __construct(
        private readonly ReservationRepository $reservationRepository,
        private readonly TableRepository $tableRepository
    ){}

    public function execute(ReservationCreateRequest $request)
    {
        $payload = $request->validated();
        /* @var Table $table */
        $table = $this->tableRepository->find($payload['table_id']);
        if(!$table){
            throw new TableException("mesa nao encontrada", 404);
        }
        if($table->hasOpenningReservationAt($payload['date'])){
            throw new ReservationException("A mesa selecionada está com uma reserva aberta", 400);
        }
        $payload['date'] = date('Y-m-d', strtotime($payload['date']));
        $payload['duration']    = $payload['duration'] ? date('H:i', strtotime($payload['duration'])) : null;
        $this->reservationRepository->save($payload);
    }
}
