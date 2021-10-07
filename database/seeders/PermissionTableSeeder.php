<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;
use App\Models\Role;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // superadmin role
        $role = Role::where('name', '=', 'superadmin')->first();
        // Seed the additional permissions
        $permissions = Permission::additionalPermission();
        if ($this->command->confirm('Seed additional permission data? [y|N]', true)) {
            $this->command->getOutput()->createProgressBar(count($permissions));
            $this->command->getOutput()->progressStart();
            foreach ($permissions as $perms) {
                Permission::firstOrCreate(['name' => $perms]);
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
            $this->command->info('Additional permission data inserted to database');
            $this->command->info('Sync superadmin role to new permission');
            if ($role->name == 'superadmin') {
                // assign all permissions
                $role->syncPermissions(Permission::all());
                $this->command->info('superadmin granted all the permissions');
            }
            $this->command->info('Sync superadmin role to permission done');
        }
    }
}
