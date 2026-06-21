<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos
        Permission::create(['name' => 'manage-raffles', 'display_name' => 'Gestionar Rifas', 'description' => 'Permite crear, editar y eliminar rifas']);
        Permission::create(['name' => 'view-finance', 'display_name' => 'Ver Finanzas', 'description' => 'Permite ver la información financiera']);
        Permission::create(['name' => 'manage-reservations', 'display_name' => 'Gestionar Reservas', 'description' => 'Permite crear, editar y eliminar reservas']);
        Permission::create(['name' => 'sell-tickets', 'display_name' => 'Vender Boletos', 'description' => 'Permite vender boletos']);
        Permission::create(['name' => 'view-reports', 'display_name' => 'Ver Reportes', 'description' => 'Permite ver los reportes de ventas y rifas']);
        Permission::create(['name' => 'manage-users', 'display_name' => 'Gestionar Usuarios', 'description' => 'Permite crear, editar y eliminar usuarios']);
        Permission::create(['name' => 'manage-roles', 'display_name' => 'Gestionar Roles', 'description' => 'Permite crear, editar y eliminar roles']);
        Permission::create(['name' => 'manage-permissions', 'display_name' => 'Gestionar Permisos', 'description' => 'Permite crear, editar y eliminar permisos']);
        Permission::create(['name' => 'view-dashboard', 'display_name' => 'Ver Dashboard', 'description' => 'Permite acceder al dashboard de administración']);
        Permission::create(['name' => 'export-data', 'display_name' => 'Exportar Datos', 'description' => 'Permite exportar datos de rifas, ventas y usuarios']);
        Permission::create(['name' => 'manage-settings', 'display_name' => 'Gestionar Configuración', 'description' => 'Permite gestionar la configuración del sistema']);

        // Crear roles y asignar permisos
        $admin = Role::create(['name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Usuario con acceso completo a todas las funcionalidades']);
        $admin->givePermissionTo(Permission::all());

        $partner = Role::create(['name' => 'partner', 'display_name' => 'Socio', 'description' => 'Usuario con acceso limitado a ciertas funcionalidades']);
        $partner->givePermissionTo(['manage-reservations', 'sell-tickets', 'view-reports', 'view-dashboard', 'export-data']);

        // Vendedores
        $seller = Role::create(['name' => 'seller', 'display_name' => 'Vendedor', 'description' => 'Usuario que puede vender boletos pero no gestionar rifas ni finanzas']);       
        $seller->givePermissionTo(['sell-tickets', 'view-reports', 'view-dashboard']);
    }
}
