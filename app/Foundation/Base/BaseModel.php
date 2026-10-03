<?php
namespace App\Foundation\Base;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Number;

use Illuminate\Validation\UnauthorizedException;
use function Illuminate\Support\now;

class BaseModel extends Model
{
    use ModelTrait;
    
    public const GENERATE_CODE_TENTATIVE = 3;
    public const DB_DATE_FORMAT = 'Y-m-d';
    public bool $asGuestUser = false;

    protected $casts = [
        "cost"  => "float",
        "price" => "float",
        "is_active" => "boolean"
    ];  

    public function nameInicial(?string $prop = null): string
    {
        if(!$prop) {
            $prop = $this->name;
        }
        $name = explode(" ", $prop);
        if (count($name) == 1) {
            $inicial = $name[0][0]. $name[0][1];
            return strtoupper($inicial);
        }
        $inicial = $name[0][0] . $name[1][0];
        return strtoupper($inicial);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function(BaseModel $model){
            if($model->asGuestUser === false){
                if($model->hasCreatedBy() || $model->hasRestaurantFilter()){
                    /** @var User $auth */
                    $auth = $model->auth();
                    if ($model->hasRestaurantFilter()) {
                        $model->restaurant_id = $auth->activeRestaurantId();
                    }
                    if ($model->hasCreatedBy()) {
                        $model->created_by = $auth->id;
                    }
                }
            }
        });
    }

    public function isActiveLabel(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? "Ativo" : "Inativo"
        );
    }

    public function getCost(): string
    {
        return Number::currency($this->cost ?? 0, 'BRL', 'pt-BR');
    }

    public function getPrice(): string
    {
        return Number::currency($this->price ?? 0, 'BRL', 'pt-BR');
    }

    public function getCurrency(string $column): string
    {
        return Number::currency($this->{$column} ?? 0, 'BRL', 'pt-BR');
    }

    public function since($custom_column_date = 'created_at')
    {
        $date = Carbon::createFromFormat("Y-m-d H:i:s",$this->{$custom_column_date} ?? now());
        $periode_year = Carbon::now()->diffInYears($date);
        $periode = (int)abs(Carbon::now()->diffInDays($date));
        if ($periode == 0){
            $diff = (int)Carbon::now()->diffInHours($date);
            if ($diff == 0){
                return (int)abs(Carbon::now()->diffInMinutes($date)) . " min";
            }else{
                return "+{$diff} horas";
            }
        }else if($periode < 7 && $periode >= 1){
            $dayForHuman = dayForHuman($periode);
            $hour = $date->format("H:i");
            return "{$dayForHuman} às {$hour}";
       }else if($periode >= 7 && $periode < 31){
            return "+". (int)Carbon::now()->diffInWeeks($date) . " sem.";
       }else if($periode >= 31 && $periode_year == 0){
            return "+". (int)Carbon::now()->diffInMonths($date) . " mês";
       }else{
            if ($periode_year >= 1){
                return "+{$periode_year} ano";
            }
       }
    }

    public function isOwner(): bool
    {
        return $this->restaurant_id === $this->auth()->restaurant_id;
    }

    public function authorizeRestaurantOwnership()
    {
        if(!$this->isOwner()){
            throw new UnauthorizedException("voce nao tem permissao para acessar esse recurso", 401);
        }
    }
}