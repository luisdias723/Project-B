<?php
namespace App\Extra;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

/**
 * Class Acl
 *
 */
final class Acl
{
    const ROLE_ADMIN = 'admin';
    const ROLE_MANAGER = 'gestor';
    const ROLE_EDITOR = 'terapeuta';
    const ROLE_USER = 'cliente';
    const ROLE_VISITOR = 'visitor';

    const PERMISSION_VIEW_MENU_PERMISSION = 'view menu permissões';
    const PERMISSION_VIEW_MENU_CLIENTS = 'view menu clientes';
    const PERMISSION_VIEW_MENU_IMOVEIS = 'view menu imoveis';
    const PERMISSION_VIEW_MENU_INVESTORS = 'view menu investors';
    const PERMISSION_VIEW_MENU_TRADES = 'view menu oportunidades';
    const PERMISSION_VIEW_MENU_FINANCIAL = 'view menu financeiro';
    const PERMISSION_VIEW_MENU_PROPOSALS = 'view menu propostas';
    const PERMISSION_VIEW_MENU_GROUPS = 'view menu grupos';
    const PERMISSION_VIEW_MENU_ADMINISTRATOR = 'view menu administrator';
    const PERMISSION_VIEW_MENU_EVENTS = 'view menu eventos';
    const PERMISSION_VIEW_MENU_PAYMENTS = 'view menu pagamentos';
    const PERMISSION_VIEW_MENU_AUCTIONS = 'view menu leiloes';

    const PERMISSION_USER_MANAGE = 'manage user';
    const PERMISSION_ARTICLE_MANAGE = 'manage article';
    const PERMISSION_PERMISSION_MANAGE = 'manage permission';

    /**
     * @param array $exclusives Exclude some permissions from the list
     * @return array
     */
    public static function permissions(array $exclusives = []): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) use ($exclusives) {
                return !in_array($value, $exclusives) && Str::startsWith($key, 'PERMISSION_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    public static function menuPermissions(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $permissions = Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'PERMISSION_VIEW_MENU_');
            });

            return array_values($permissions);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }

    /**
     * @return array
     */
    public static function roles(): array
    {
        try {
            $class = new \ReflectionClass(__CLASS__);
            $constants = $class->getConstants();
            $roles =  Arr::where($constants, function($value, $key) {
                return Str::startsWith($key, 'ROLE_');
            });

            return array_values($roles);
        } catch (\ReflectionException $exception) {
            return [];
        }
    }
}
