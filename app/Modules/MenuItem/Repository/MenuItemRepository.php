<?php
namespace App\Modules\MenuItem\Repository;


use App\Foundation\Base\BaseRepository;
use App\Http\Requests\PaginateRequest;
use App\Models\MenuItem;

class MenuItemRepository extends BaseRepository
{
    protected array $searchableFields = [
        "id",
        "name",
        "category_id"
    ];
    public function __construct()
    {
        return parent::__construct();
    }

    public function eloquent(): MenuItem
    {
        return app(MenuItem::class);
    }

    public function findAll(?PaginateRequest $paginate = null)
    {
        $query = $this->getQuery();
        if (!empty($paginate->categories)) {
            $query->whereIn("category_id", $paginate->categories);
        }
        if ($paginate->features){
            $query->where("featured_types", "LIKE" ,"%". implode(",", $paginate->features) . "%");
        }
        return parent::findAll($paginate);
    }
}