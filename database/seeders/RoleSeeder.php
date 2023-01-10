<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $role = Role::create(['name' => 'administrator']);
        $role->givePermissionTo([
            'view_dashboard',
            'view_customers',
            'view_security',
            'view_products',
            'view_enticement',
            'view_tickets',
            'view_transactions',
            'view_invoice',
            'view_users',
            'view_reports',
            'view_customer_balance',
            'view_general_reports',
            'view_revenue_reports',
            'view_customer_reports',
            'view_blacklist_reports',
            'view_tickets_reports',
            'view_content_reports',
            'view_settings',
            'add_blacklist',
            'import_blacklist',
            'subscribe_unsubscribe_customer',
            'remove_customer_blacklist',
            'push_enticement',
            'import_contacts',
            'create_product',
        ]);

        $role1 = Role::create(['name' => 'Vodacom']);
        $role1->givePermissionTo([
            'view_customers',
            'view_tickets',
            'add_blacklist',
            'view_security',
            'subscribe_unsubscribe_customer'
        ]);

        $role2 = Role::create(['name' => 'Vodacom Admin']);
        $role2->givePermissionTo([
            'view_customers',
            'view_tickets',
            'add_blacklist',
            'view_security',
            'subscribe_unsubscribe_customer',
            'view_reports',
            'view_tickets_reports',
            'view_customer_balance',
            'import_blacklist'
        ]);
    }
}
