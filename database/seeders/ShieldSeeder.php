<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use BezhanSalleh\FilamentShield\Support\Utils;
use Spatie\Permission\PermissionRegistrar;

class ShieldSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $rolesWithPermissions = '[{"name":"super_admin","guard_name":"web","permissions":["view_approval::flow","view_any_approval::flow","create_approval::flow","update_approval::flow","restore_approval::flow","restore_any_approval::flow","replicate_approval::flow","reorder_approval::flow","delete_approval::flow","delete_any_approval::flow","force_delete_approval::flow","force_delete_any_approval::flow","view_bank::account","view_any_bank::account","create_bank::account","update_bank::account","restore_bank::account","restore_any_bank::account","replicate_bank::account","reorder_bank::account","delete_bank::account","delete_any_bank::account","force_delete_bank::account","force_delete_any_bank::account","view_branch","view_any_branch","create_branch","update_branch","restore_branch","restore_any_branch","replicate_branch","reorder_branch","delete_branch","delete_any_branch","force_delete_branch","force_delete_any_branch","view_contract::type","view_any_contract::type","create_contract::type","update_contract::type","restore_contract::type","restore_any_contract::type","replicate_contract::type","reorder_contract::type","delete_contract::type","delete_any_contract::type","force_delete_contract::type","force_delete_any_contract::type","view_currency","view_any_currency","create_currency","update_currency","restore_currency","restore_any_currency","replicate_currency","reorder_currency","delete_currency","delete_any_currency","force_delete_currency","force_delete_any_currency","view_customer","view_any_customer","create_customer","update_customer","restore_customer","restore_any_customer","replicate_customer","reorder_customer","delete_customer","delete_any_customer","force_delete_customer","force_delete_any_customer","view_employee::salary","view_any_employee::salary","create_employee::salary","update_employee::salary","restore_employee::salary","restore_any_employee::salary","replicate_employee::salary","reorder_employee::salary","delete_employee::salary","delete_any_employee::salary","force_delete_employee::salary","force_delete_any_employee::salary","view_exchange::rate","view_any_exchange::rate","create_exchange::rate","update_exchange::rate","restore_exchange::rate","restore_any_exchange::rate","replicate_exchange::rate","reorder_exchange::rate","delete_exchange::rate","delete_any_exchange::rate","force_delete_exchange::rate","force_delete_any_exchange::rate","view_expense","view_any_expense","create_expense","update_expense","restore_expense","restore_any_expense","replicate_expense","reorder_expense","delete_expense","delete_any_expense","force_delete_expense","force_delete_any_expense","view_expense::category","view_any_expense::category","create_expense::category","update_expense::category","restore_expense::category","restore_any_expense::category","replicate_expense::category","reorder_expense::category","delete_expense::category","delete_any_expense::category","force_delete_expense::category","force_delete_any_expense::category","view_fund::request","view_any_fund::request","create_fund::request","update_fund::request","restore_fund::request","restore_any_fund::request","replicate_fund::request","reorder_fund::request","delete_fund::request","delete_any_fund::request","force_delete_fund::request","force_delete_any_fund::request","view_kyc","view_any_kyc","create_kyc","update_kyc","restore_kyc","restore_any_kyc","replicate_kyc","reorder_kyc","delete_kyc","delete_any_kyc","force_delete_kyc","force_delete_any_kyc","view_leave","view_any_leave","create_leave","update_leave","restore_leave","restore_any_leave","replicate_leave","reorder_leave","delete_leave","delete_any_leave","force_delete_leave","force_delete_any_leave","view_offline::transfer","view_any_offline::transfer","create_offline::transfer","update_offline::transfer","restore_offline::transfer","restore_any_offline::transfer","replicate_offline::transfer","reorder_offline::transfer","delete_offline::transfer","delete_any_offline::transfer","force_delete_offline::transfer","force_delete_any_offline::transfer","view_province","view_any_province","create_province","update_province","restore_province","restore_any_province","replicate_province","reorder_province","delete_province","delete_any_province","force_delete_province","force_delete_any_province","view_referral","view_any_referral","create_referral","update_referral","restore_referral","restore_any_referral","replicate_referral","reorder_referral","delete_referral","delete_any_referral","force_delete_referral","force_delete_any_referral","view_role","view_any_role","create_role","update_role","delete_role","delete_any_role","view_store","view_any_store","create_store","update_store","restore_store","restore_any_store","replicate_store","reorder_store","delete_store","delete_any_store","force_delete_store","force_delete_any_store","view_store::contact","view_any_store::contact","create_store::contact","update_store::contact","restore_store::contact","restore_any_store::contact","replicate_store::contact","reorder_store::contact","delete_store::contact","delete_any_store::contact","force_delete_store::contact","force_delete_any_store::contact","view_token","view_any_token","create_token","update_token","restore_token","restore_any_token","replicate_token","reorder_token","delete_token","delete_any_token","force_delete_token","force_delete_any_token","view_transaction","view_any_transaction","create_transaction","update_transaction","restore_transaction","restore_any_transaction","replicate_transaction","reorder_transaction","delete_transaction","delete_any_transaction","force_delete_transaction","force_delete_any_transaction","view_transfer","view_any_transfer","create_transfer","update_transfer","restore_transfer","restore_any_transfer","replicate_transfer","reorder_transfer","delete_transfer","delete_any_transfer","force_delete_transfer","force_delete_any_transfer","view_user","view_any_user","create_user","update_user","restore_user","restore_any_user","replicate_user","reorder_user","delete_user","delete_any_user","force_delete_user","force_delete_any_user","view_wallet","view_any_wallet","create_wallet","update_wallet","restore_wallet","restore_any_wallet","replicate_wallet","reorder_wallet","delete_wallet","delete_any_wallet","force_delete_wallet","force_delete_any_wallet","view_withdrawal::request","view_any_withdrawal::request","create_withdrawal::request","update_withdrawal::request","restore_withdrawal::request","restore_any_withdrawal::request","replicate_withdrawal::request","reorder_withdrawal::request","delete_withdrawal::request","delete_any_withdrawal::request","force_delete_withdrawal::request","force_delete_any_withdrawal::request","widget_BankAccountOverviewStats","widget_WalletOverviewStats","widget_ExpenseStatusStats","widget_UserStats"]}]';
        $directPermissions = '[]';

        static::makeRolesWithPermissions($rolesWithPermissions);
        static::makeDirectPermissions($directPermissions);

        $this->command->info('Shield Seeding Completed.');
    }

    protected static function makeRolesWithPermissions(string $rolesWithPermissions): void
    {
        if (! blank($rolePlusPermissions = json_decode($rolesWithPermissions, true))) {
            /** @var Model $roleModel */
            $roleModel = Utils::getRoleModel();
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($rolePlusPermissions as $rolePlusPermission) {
                $role = $roleModel::firstOrCreate([
                    'name' => $rolePlusPermission['name'],
                    'guard_name' => $rolePlusPermission['guard_name'],
                ]);

                if (! blank($rolePlusPermission['permissions'])) {
                    $permissionModels = collect($rolePlusPermission['permissions'])
                        ->map(fn ($permission) => $permissionModel::firstOrCreate([
                            'name' => $permission,
                            'guard_name' => $rolePlusPermission['guard_name'],
                        ]))
                        ->all();

                    $role->syncPermissions($permissionModels);
                }
            }
        }
    }

    public static function makeDirectPermissions(string $directPermissions): void
    {
        if (! blank($permissions = json_decode($directPermissions, true))) {
            /** @var Model $permissionModel */
            $permissionModel = Utils::getPermissionModel();

            foreach ($permissions as $permission) {
                if ($permissionModel::whereName($permission)->doesntExist()) {
                    $permissionModel::create([
                        'name' => $permission['name'],
                        'guard_name' => $permission['guard_name'],
                    ]);
                }
            }
        }
    }
}
