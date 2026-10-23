<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DrogueriaRolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar caché de roles y permisos
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // 1. Definición de permisos por módulo y acciones
        $permissions = [
            // Módulo Usuarios
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            // Módulo Roles y Seguridad
            'roles.ver',
            'roles.editar',

            // Módulo Productos (Medicamentos e insumos)
            'productos.ver',
            'productos.crear',
            'productos.editar',
            'productos.eliminar',

            // Módulo Lotes y Trazabilidad
            'lotes.ver',
            'lotes.crear',
            'lotes.editar',
            'lotes.eliminar',
            'recibir-lotes', // Acción propia de recepción

            // Módulo Proveedores
            'proveedores.ver',
            'proveedores.crear',
            'proveedores.editar',
            'proveedores.eliminar',

            // Módulo Compras
            'compras.ver',
            'compras.crear',
            'compras.editar',
            'compras.eliminar',
            'compras.aprobar',

            // Módulo Ventas y Dispensación
            'ventas.ver',
            'ventas.crear',
            'ventas.cobrar',
            'ventas.anular',
            'dispensar-control-especial', // Acción propia exclusiva del regente

            // Módulo Inventario
            'inventario.ver',
            'inventario.ajustar',

            // Módulo Reportes / Indicadores (KPIs)
            'reportes.ver',
        ];

        // Crear permisos usando findOrCreate
        foreach ($permissions as $permissionName) {
            Permission::findOrCreate($permissionName, 'web');
        }

        // 2. Definición de la asignación de permisos según la Matriz de Seguridad y Segregación de Funciones
        $rolesPermissions = [
            // Administrador (dueño): Gestión comercial, compras, usuarios y finanzas.
            // NO recibe en bodega para cumplir segregación de funciones con compras.
            'admin' => [
                'usuarios.ver',
                'usuarios.crear',
                'usuarios.editar',
                'usuarios.eliminar',
                'roles.ver',
                'roles.editar',
                'productos.ver',
                'productos.crear',
                'productos.editar',
                'productos.eliminar',
                'proveedores.ver',
                'proveedores.crear',
                'proveedores.editar',
                'proveedores.eliminar',
                'compras.ver',
                'compras.crear',
                'compras.editar',
                'compras.eliminar',
                'compras.aprobar',
                'ventas.ver',
                'ventas.anular',
                'lotes.ver',
                'inventario.ver',
                'inventario.ajustar',
                'reportes.ver',
            ],

            // Regente de farmacia: Responsable técnico sanitario.
            // Recibe lotes (inspección técnica), autoriza control especial.
            // NO crea compras (segregación de funciones).
            'regente' => [
                'productos.ver',
                'lotes.ver',
                'lotes.crear',
                'lotes.editar',
                'recibir-lotes',
                'compras.ver',
                'inventario.ver',
                'ventas.ver',
                'dispensar-control-especial',
                'reportes.ver',
            ],

            // Cajero: Atención en punto de venta, cobros ordinarios.
            // NO puede crear compras, ni recibir lotes, ni dispensar control especial sin regente.
            'cajero' => [
                'productos.ver',
                'inventario.ver',
                'ventas.ver',
                'ventas.crear',
                'ventas.cobrar',
            ],

            // Auxiliar de bodega: Logística física, descarga y acomodación de mercancía.
            // NO crea compras ni realiza ventas ni dispensa control especial.
            'auxiliar-bodega' => [
                'productos.ver',
                'lotes.ver',
                'recibir-lotes',
                'compras.ver',
                'inventario.ver',
            ],
        ];

        // 3. Crear roles usando findOrCreate y sincronizar permisos con syncPermissions
        foreach ($rolesPermissions as $roleName => $perms) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($perms);
        }
    }
}
