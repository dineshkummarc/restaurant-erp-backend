<?php
namespace App\Foundation\Base;

use App\Foundation\Contracts\BaseRepositoryInterface;
use App\Http\Requests\PaginateRequest;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;

abstract class BaseRepository implements BaseRepositoryInterface
{

    protected array $searchableFields = [];
    protected bool $listBothActiveInactive = false;
    private static $model = null;
    private ?Builder $query = null;

    protected string $table_name {
        get => $this->eloquent()->getTable();
    }

    public ?User $auth {
        get => auth()->user();
    }

    public function __construct()
    {
       $this->getModel();
       //$this->whereRestaurantId();
    }

    public function save(array $data): BaseModel|Model|Authenticatable
    {
        return $this->eloquent()->create($data);
    }

    public function findAll(?PaginateRequest $paginate = null)
    {
        if(!$paginate) {
            return $this->newQuery()->get();
        }
        return $this->paginate($paginate);
    }

    public function find($id)
    {
       
        return $this->eloquent()->find($id);
        
    }

    public function update(BaseModel|Model|Authenticatable $model, array $data)
    {
        if ($model) {
            $model->update($data);
            return $model;
        }
        return null;
    }

    public function delete(BaseModel|Model|Authenticatable $baseModel)
    {
        $baseModel->delete();
    }

    public function newQuery(): Builder
    {
        $query = $this->eloquent()->query();
        if ($this->eloquent()->hasRestaurantFilter()){
            $query->where("{$this->table_name}.restaurant_id", $this->auth->activeRestaurantId());
        }
        if($this->eloquent()->hasChainFilter()){
            $query->where("{$this->table_name}.chain_id", $this->auth->restaurant->chain_id);
        }
        return $query;
    }

    public function getQuery(): Builder
    {
        if (!$this->query) {
            $this->query = $this->eloquent()->query();
        }
        return $this->query;
    }

    public function baseQuerySearch(PaginateRequest $paginate)
    {
        $values = $paginate->all();
        foreach ($values as $property => $field) {
            if ($this->eloquent()->hasActiveFilter() && $property == "is_active"){
                $is_active = $paginate->is_active == "true" ? true : false;
                $this->getQuery()->where('is_active', $is_active);
                continue;
            }
            if ($property == "search" && !blank($field)) {
                $this->getQuery()->where(column: function ($nestedQuery) use ($field, $paginate) {
                    array_map(function ($item) use($nestedQuery, $paginate){
                        $nestedQuery->orWhere($item, 'LIKE', "%{$paginate->search}%");
                    }, $this->searchableFields);
                });
                continue;
            } 
            if (in_array($property, $this->searchableFields)){
                if (!blank($field)){
                    $this->getQuery()->where(column: function ($nestedQuery) use ($field, $paginate, $property) {
                        $nestedQuery->orWhere($property, '=', "$field");
                    });
                }
            }
        }
        return $this;
    }

    public function whereRestaurantId()
    {
        if ($this->eloquent()->hasRestaurantFilter() && $this->auth instanceof User){
            $this->getQuery()->where("{$this->table_name}.restaurant_id", $this->auth->activeRestaurantId());
        }
        return $this;
    }

    public function whereChainId()
    {
        if ($this->eloquent()->hasChainFilter() && $this->auth instanceof User){
            $this->getQuery()->where("{$this->table_name}.chain_id", $this->auth->restaurant->chain_id);
        }
        return $this;
    }

    public function paginate(PaginateRequest $paginate)
    {
        return $this->whereRestaurantId()->whereChainId()->baseQuerySearch($paginate)->get($paginate);
    }
    public function get(PaginateRequest $paginate)
    {
        return $this->getQuery()->offset($paginate->offset)->limit($paginate->limit)->orderBy("{$this->table_name}.id", "DESC")->get();
    }

    protected abstract function eloquent(): BaseModel|Model|Authenticatable;
    public function getModel()
    {
        if (self::$model instanceof BaseModel) {
            return self::$model;
        }
        self::$model =  $this->eloquent();
        return self::$model;
    }

    public function findBy(array $columns, mixed ...$values)
    {
        $filters = [];
        if (count($columns) != count($values)) {
            throw new \Exception("colunas e volores tem ser de tamanho igual");
        }
        foreach ($columns as $key => $column) {
            $notFilterColumn = explode(":", $column);
            if (count($notFilterColumn) == 2) {
                if($notFilterColumn[1] == "like"){
                    $filters[] = [$this->table_name . "." .$notFilterColumn[0], 'like', "%{$values[$key]}%"];
                }else{
                    $filters[] = [$this->table_name . "." .$notFilterColumn[0], '<>', $values[$key]];
                }
            }else{
                $filters[] = [$this->table_name . "." .$column, $values[$key]];
            }
        }
        $this->whereRestaurantId();
        return $this->newQuery()->where($filters)->get()->toArray();
    }
    public function findFirstBy(array $columns, array $values, string $direction = 'DESC', string $oderBy = 'id'): BaseModel|Model|Authenticatable|null
    {
        $filters = [];
        if (count($columns) != count($values)) {
            throw new \Exception("colunas e volores tem ser de tamanho igual");
        }
        foreach ($columns as $key => $column) {
            $notFilterColumn = explode(":", $column);
            if (count($notFilterColumn) == 2) {
                $filters[] = [$this->table_name . "." .$notFilterColumn[0], '<>', $values[$key]];
            }else{
                $filters[] = [$this->table_name . "." .$column, $values[$key]];
            }
        }
        $this->whereRestaurantId();
        return $this->getQuery()->where($filters)->orderBy($oderBy, $direction)->first();
    }
    public function existBy(string $column, $value): bool
    {
        return $this->eloquent()->where($this->table_name . "." .$column, $value)->exists();
    }

    public function saveMany(array $payload, bool $timestamps = true): void
    {
        if ($timestamps){
            $now = now();
            foreach ($payload as &$item) {
                $item['created_at'] = $now;
                $item['updated_at'] = $now;
            }
        }
        $this->eloquent()->insert($payload);
    }
    public function findByIds(array $ids)
    {
        $this->whereRestaurantId();
        return $this->getQuery()->whereIn("id", $ids)->get();
    }
}