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

        // Crear permisos (view and manage permissions)
        Permission::create(['name' => 'view_raffles', 'display_name' => 'Ver Rifas', 'description' => 'Permite ver la información de las rifas']);
        Permission::create(['name' => 'manage_raffles', 'display_name' => 'Gestionar Rifas', 'description' => 'Permite crear, editar y eliminar rifas']);
        Permission::create(['name' => 'view_finance', 'display_name' => 'Ver Finanzas', 'description' => 'Permite ver la información financiera']);
        Permission::create(['name' => 'manage_finance', 'display_name' => 'Gestionar Finanzas', 'description' => 'Permite gestionar la información financiera']);
        Permission::create(['name' => 'view_reservations', 'display_name' => 'Ver Reservas', 'description' => 'Permite ver la información de las reservas']);
        Permission::create(['name' => 'manage_reservations', 'display_name' => 'Gestionar Reservas', 'description' => 'Permite crear, editar y eliminar reservas']);        
        Permission::create(['name' => 'sell_tickets', 'display_name' => 'Vender Boletos', 'description' => 'Permite vender boletos']);
        Permission::create(['name' => 'view_tickets', 'display_name' => 'Ver Boletas', 'description' => 'Permite ver la información de las boletas vendidas']);
        Permission::create(['name' => 'manage_tickets', 'display_name' => 'Gestionar Boletos', 'description' => 'Permite gestionar la información de las boletas vendidas']);
        Permission::create(['name' => 'view_reports', 'display_name' => 'Ver Reportes', 'description' => 'Permite ver los reportes de ventas y rifas']);
        Permission::create(['name' => 'manage_reports', 'display_name' => 'Gestionar Reportes', 'description' => 'Permite generar y gestionar los reportes de ventas y rifas']);
        Permission::create(['name' => 'view_users', 'display_name' => 'Ver Usuarios', 'description' => 'Permite ver la información de los usuarios']);
        Permission::create(['name' => 'manage_users', 'display_name' => 'Gestionar Usuarios', 'description' => 'Permite crear, editar y eliminar usuarios']);
        Permission::create(['name' => 'view_roles', 'display_name' => 'Ver Roles', 'description' => 'Permite ver la información de los roles']);
        Permission::create(['name' => 'manage_roles', 'display_name' => 'Gestionar Roles', 'description' => 'Permite crear, editar y eliminar roles']);
        Permission::create(['name' => 'view_permissions', 'display_name' => 'Ver Permisos', 'description' => 'Permite ver la información de los permisos']);
        Permission::create(['name' => 'manage_permissions', 'display_name' => 'Gestionar Permisos', 'description' => 'Permite crear, editar y eliminar permisos']);
        Permission::create(['name' => 'view_dashboard', 'display_name' => 'Ver Dashboard', 'description' => 'Permite acceder al dashboard de administración']);
        Permission::create(['name' => 'export_data', 'display_name' => 'Exportar Datos', 'description' => 'Permite exportar datos de rifas, ventas y usuarios']);
        Permission::create(['name' => 'manage_settings', 'display_name' => 'Gestionar Configuración', 'description' => 'Permite gestionar la configuración del sistema']);

        // Crear roles y asignar permisos
        $superadmin = Role::create(['name' => 'superadmin', 'display_name' => 'Super Administrador', 'description' => 'Usuario con acceso completo a todas las funcionalidades']);
        $superadmin->givePermissionTo(Permission::all());

        $admin = Role::create(['name' => 'admin', 'display_name' => 'Administrador', 'description' => 'Usuario con acceso completo a todas las funcionalidades']);
        $admin->givePermissionTo(Permission::all()->except(['manage_settings', 'manage_permissions', 'manage_roles']));

        $partner = Role::create(['name' => 'partner', 'display_name' => 'Socio', 'description' => 'Usuario con acceso limitado a ciertas funcionalidades']);
        $partner->givePermissionTo(['manage_reservations', 'sell_tickets', 'view_reports', 'view_dashboard', 'export_data']);

        // Vendedores
        $seller = Role::create(['name' => 'seller', 'display_name' => 'Vendedor', 'description' => 'Usuario que puede vender boletos pero no gestionar rifas ni finanzas']);       
        $seller->givePermissionTo(['sell_tickets', 'view_reports', 'view_dashboard']);
    }
}
