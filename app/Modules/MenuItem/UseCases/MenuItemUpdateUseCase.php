<?php
namespace App\Modules\MenuItem\UseCases;

use App\Http\Requests\MenuItem\MenuItemUpdateRequest;
use App\Models\MenuItem;
use App\Models\TechnicalSheet;
use App\Modules\MenuItem\Exceptions\MenuItemException;
use App\Modules\MenuItem\Exceptions\MenuItemNotFountException;
use App\Modules\MenuItem\Repository\MenuItemRepository;
use App\Modules\TechnicalSheet\Repository\TechnicalSheetRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;


final class MenuItemUpdateUseCase extends \App\Foundation\Base\BaseUseCase
{
    
    public function __construct(
        private readonly MenuItemRepository $menuItemRepository,
        private readonly TechnicalSheetRepository $technicalSheetRepository
    ){}

    public function execute(MenuItem $menuItem, MenuItemUpdateRequest $request)
    {
        $payload = $request->validated();
        $payload['enable_technical_sheet'] = $payload['enable_technical_sheet'] == 'true' ? true : false;
        $payload['is_active'] = $payload['is_active'] == 'true' ? true : false;
        $menuItem = $this->menuItemRepository->find($menuItem->id);
        if (!$menuItem){
            throw new MenuItemNotFountException;
        }
        $exists = $this->menuItemRepository->findFirstBy(["name", "id:not"], [$payload["name"], $menuItem->id]);
        if (!empty($exists)){
            throw new MenuItemException("recurso já existe", 400);
        }
        $sheetIds = array_column($request->getSheet(), "id");
        $itemSheetItem = $this->technicalSheetRepository->findByIds($sheetIds);
        $toSave = [];
        foreach($request->getSheet() as $sheet) {
            if (!isset($sheet["id"])){
                $toSave[] = new TechnicalSheet($sheet);
            }
        }
        if (isset($payload["image"]) instanceof \Illuminate\Http\UploadedFile){
            $extension = $payload["image"]->getClientOriginalExtension();
            $avatarName = md5(Str::password(28) . strtotime("")).".". $extension;
            $this->cropped_image($payload["image"], 400, 260)
                ->save(storage_path("app/public/menu_items/{$avatarName}"));
                
            $payload['image'] = $avatarName;
        }
        DB::transaction(function() use($payload, $toSave, $menuItem, $itemSheetItem, $request){
            $payload['enable_technical_sheet'] = $payload['enable_technical_sheet'] == 'true' ? true : false;
            $payload['is_active'] = $payload['is_active'] == 'true' ? true : false;
            $sheetProductIds = array_column($request->getSheet(), "product_id");
            $this->menuItemRepository->update($menuItem, $payload);
            $menuItem->technicalSheet->each(function(TechnicalSheet $item) use ($sheetProductIds){
                if (!in_array($item->product_id, $sheetProductIds)) {
                    $item->delete();
                }
            });
            if (!empty($toSave)){
                $menuItem->technicalSheet()->saveMany($toSave);
            }
            $itemSheetItem->each(function(TechnicalSheet $item) use ($request){
                $currentItemPayload = array_find($request->getSheet(), fn($input) => $input["id"] == $item->id);
                if ($item->quantity != $currentItemPayload["quantity"]){
                    $item->update($currentItemPayload);
                }
            });
       });
    }
}