<?php

use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $orderGroup = DB::table('route_groups')->where('name', 'pedidos')->first();
        $permissions = [
            [
                'name' => 'api.reservations.index',
                'label' => 'Reservação',
                'description' => 'Visualizar reservações',
                'route_group_id' => $orderGroup->id,
                'view_path'  => 'reservations',
                'type' => 'api',
                'show_in_menu' => true,
                'guard_name' => 'api',
            ],
            [
                'name' => 'api.reservations.show',
                'label' => 'reservação',
                'description' => 'Visualizar uma reservação',
                'route_group_id' => $orderGroup->id,
                'type' => 'api',
                'show_in_menu' => false,
                'guard_name' => 'api',
            ],
            [
                'name' => 'api.reservations.store',
                'label' => 'Salvar',
                'description' => 'Salvar uma reservação',
                'route_group_id' => $orderGroup->id,
                'type' => 'api',
                'show_in_menu' => false,
                'guard_name' => 'api',
            ],
            [
                'name' => 'api.reservations.update',
                'label' => 'Alterar',
                'description' => 'Aleterar uma reservação',
                'route_group_id' => $orderGroup->id,
                'type' => 'api',
                'show_in_menu' => false,
                'guard_name' => 'api',
            ],
            [
                'name' => 'api.reservations.delete',
                'label' => 'Remover',
                'description' => 'Remover uma reservação',
                'route_group_id' => $orderGroup->id,
                'type' => 'api',
                'show_in_menu' => false,
                'guard_name' => 'api',
            ],
            [
                'name' => 'api.reservations.listByTableAndDate',
                'label' => 'Listar',
                'description' => 'Listar reservação por id da mesa e data',
                'route_group_id' => $orderGroup->id,
                'type' => 'api',
                'show_in_menu' => false,
                'guard_name' => 'api',
            ],
            [
                'name' => 'api.reservations.status',
                'label' => 'Aleterar',
                'description' => 'Alterar o status da reservação',
                'route_group_id' => $orderGroup->id,
                'type' => 'api',
                'show_in_menu' => false,
                'guard_name' => 'api',
            ],
        ];
        foreach ($permissions as $permission) {
            DB::table('permissions')->insert($permission);
        }
        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();
        $admin = Role::where('name', 'admin')->first();
        $admin->givePermissionTo([
            'api.reservations.index',
            'api.reservations.show',
            'api.reservations.update',
            'api.reservations.delete',
            'api.reservations.store',
            'api.reservations.status',
            'api.reservations.listByTableAndDate'
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
