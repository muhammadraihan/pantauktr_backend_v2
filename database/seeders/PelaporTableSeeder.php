<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Pelapor;
use Hash;

class PelaporTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Pelapor::create([
            'firstname' => 'Pelapor', 
            'lastname' => 'Satu', 
            'email' => 'superadmin@app.com', 
            'password' => Hash::make('password'),
            'provider' => 'Google',
            'avatar' => '',
            // 'reward_point' => '',
            'last_login_ip' => '',
            'last_login_at' => ','
        ]);
    }
}
