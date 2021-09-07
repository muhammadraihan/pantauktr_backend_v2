<?php

namespace Database\Seeders;

use App\Models\BentukPelanggaran;
use App\Models\User;
use Illuminate\Database\Seeder;

class BentukPelanggaranTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('name', '=', 'superadmin')->first()->uuid;

        $bentuks = [
            ['bentuk_pelanggaran' => 'Orang Merokok', 'keterangan' => 'Ditemukan orang merokok di tempat yang ditetapkan sebagai KTR', 'created_by' => $user],
            ['bentuk_pelanggaran' => 'Asbak', 'keterangan' => 'Ditemukan asbak di tempat yang ditetapkan sebagai KTR', 'created_by' => $user],
            ['bentuk_pelanggaran' => 'Puntung rokok', 'keterangan' => 'Ditemukan puntung rokok di tempat yang ditetapkan sebagai KTR', 'created_by' => $user],
            ['bentuk_pelanggaran' => 'Bungkus Rokok', 'keterangan' => 'Ditemukan bungkus rokok di tempat yang ditetapkan sebagai KTR', 'created_by' => $user],
            ['bentuk_pelanggaran' => 'Spanduk/Baliho Rokok', 'keterangan' => 'Ditemukan Spanduk atau baliho tentang iklan rokok di tempat yang ditetapkan sebagai KTR', 'created_by' => $user],
            ['bentuk_pelanggaran' => 'Menjual Produk rokok', 'keterangan' => 'Ditemukan penjualan rokok di tempat yang ditetapkan sebagai KTR', 'created_by' => $user],
        ];

        if ($this->command->confirm('Seed data referensi bentuk pelanggaran? [y|N]', true)) {
            $this->command->getOutput()->createProgressBar(count($bentuks));
            $this->command->getOutput()->progressStart();
            foreach ($bentuks as $bentuk) {
                BentukPelanggaran::create($bentuk);
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
            $this->command->info('Data referensi bentuk pelanggaran inserted to database');
        }
    }
}
