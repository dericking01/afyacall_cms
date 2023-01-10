<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {


        Artisan::call('cache:clear');
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
        
        Permission::create(['name' => 'view_dashboard']);
        Permission::create(['name' => 'view_customers']);
        Permission::create(['name' => 'view_security']);
        Permission::create(['name' => 'view_products']);
        Permission::create(['name' => 'view_enticement']);
        Permission::create(['name' => 'view_tickets']);
        Permission::create(['name' => 'view_transactions']);
        Permission::create(['name' => 'view_invoice']);
        Permission::create(['name' => 'view_users']);
        Permission::create(['name' => 'view_reports']);
        Permission::create(['name' => 'view_general_reports']);
        Permission::create(['name' => 'view_settings']);
        Permission::create(['name' => 'view_revenue_reports']);
        Permission::create(['name' => 'view_customer_reports']);
        Permission::create(['name' => 'view_blacklist_reports']);
        Permission::create(['name' => 'view_tickets_reports']);
        Permission::create(['name' => 'view_content_reports']);
        Permission::create(['name' => 'add_blacklist']);
        Permission::create(['name' => 'import_blacklist']);
        Permission::create(['name' => 'view_customer_balance']);
        Permission::create(['name' => 'subscribe_unsubscribe_customer']);
        Permission::create(['name' => 'remove_customer_blacklist']);
        Permission::create(['name' => 'push_enticement']);
        Permission::create(['name' => 'import_contacts']);
        Permission::create(['name' => 'create_product']);    
    }
}
