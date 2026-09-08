<?php
namespace App\Modules\Reservation\Enums;

enum ReservationStatusEnum: int
{
    case PENDING = 1;
    case CONFIRMED = 2;
    case CANCELLED = 3;
    case SEATED = 4;
    case FINISHED = 5;

    public function getLabel(): string
    {
        return match($this){
            self::PENDING   => "Pendente",
            self::CONFIRMED => "Confirmada",
            self::CANCELLED => "Cancelada",
            self::SEATED    => "Sentado",
            self::FINISHED  => "Finalizada",
            default => ""
        };
    }

    public function isPending(): bool
    {
        return $this == self::PENDING;
    }

    public function isConfirmed(): bool
    {
        return $this == self::CONFIRMED;
    }

    public function isCancelled(): bool
    {
        return $this == self::CANCELLED;
    }

    public function isSeated(): bool
    {
        return $this == self::SEATED;
    }

    public function isFinished(): bool
    {
        return $this == self::FINISHED;
    }

    public static function avalaibles(): array
    {
        return [self::CANCELLED->value];
    }

    public function getSeverity(): string
    {
        return match($this){
            self::PENDING => "status-pending",
            self::CONFIRMED => "status-confirmed",
            self::CANCELLED => "status-cancelled",
            self::SEATED => "status-seated",
            default => ""
        };
    }

    public function getLabelSeverity(): string
    {
        return match($this){
            self::PENDING => "b-pending",
            self::CONFIRMED => "b-confirmed",
            self::CANCELLED => "b-cancelled",
            self::SEATED => "b-seated",
            self::FINISHED => "b-confirmed",
            default => ""
        };
    }
}
