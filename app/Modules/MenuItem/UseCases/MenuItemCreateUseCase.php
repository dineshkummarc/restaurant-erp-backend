<?php
namespace App\Modules\MenuItem\UseCases;

use App\Http\Requests\MenuItem\MenuItemCreateRequest;
use App\Modules\MenuItem\Exceptions\MenuItemException;
use App\Modules\MenuItem\Repository\MenuItemRepository;
use Illuminate\Support\Str;
final class MenuItemCreateUseCase extends \App\Foundation\Base\BaseUseCase
{
    public function __construct(
        private readonly MenuItemRepository $menuItemRepository,
    ){}

    public function execute(MenuItemCreateRequest $request)
    {
        $payload = $request->validated();
        $exists = $this->menuItemRepository->findFirstBy(["name"], [$payload["name"]]);
        if (!empty($exists)){
            throw new MenuItemException("recurso já existe", 400);
        }
        if (isset($payload["image"]) && $payload["image"] instanceof \Illuminate\Http\UploadedFile){
            $extension = $payload["image"]->getClientOriginalExtension();
            $avatarName = md5($payload["image"]->getClientOriginalName() . strtotime("now")).".". $extension;
            $this->cropped_image($payload["image"], 400, 260)
                ->save(storage_path("app/public/menu_items/{$avatarName}"));
                
            $payload['image'] = $avatarName;
        }
        $payload['enable_technical_sheet'] = $payload['enable_technical_sheet'] == 'true' ? true : false;
        $payload['is_active'] = $payload['is_active'] == 'true' ? true : false;
        $this->menuItemRepository->save($payload);
    }
}