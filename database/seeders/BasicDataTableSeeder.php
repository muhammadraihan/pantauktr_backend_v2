<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Jenis_laporan;
use App\Models\Jenis_apresiasi;
use App\Models\Pelanggaran;

class BasicDataTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('name','=','superadmin')->first()->uuid;

        $laporans = [
            ['name' => 'Pelanggaran','created_by' => $user],
            ['name' => 'Apresiasi','created_by' => $user],
        ];
        
        $pelanggarans = [
            ['name' => 'Kawasan Tanpa Rokok','keterangan' => 'Jenis pelanggaran di kawasan yang tidak diperbolehkan untuk merokok sesuai peraturan daerah yang berlaku','created_by' => $user],
            ['name' => 'Point of Sale','keterangan' => 'Jenis pelanggaran berupa penjualan rokok ataupun pemberitahuan tentang adanya penjualan rokok sesuai peraturan daerah yang berlaku','created_by' => $user],
            ['name' => 'TAPS Ban','keterangan' => 'Jenis pelanggaran berupa pelarangan iklan media luar ruang, promosi dan sponsor produk tembakau.','created_by' => $user],
        ];

        $apresiasi = [
            'Saran',
            'Masukan',
        ];

        if ($this->command->confirm('Seed data referensi jenis laporan? [y|N]', true)) {
            $this->command->getOutput()->createProgressBar(count($laporans));
            $this->command->getOutput()->progressStart();
            foreach ($laporans as $laporan) {
                Jenis_laporan::create($laporan);
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
            $this->command->info('Data referensi jenis laporan inserted to database');
        }

        if ($this->command->confirm('Seed data referensi Tipe Pelanggaran? [y|N]', true)) {
            $this->command->getOutput()->createProgressBar(count($pelanggarans));
            $this->command->getOutput()->progressStart();
            foreach ($pelanggarans as $pelanggaran) {
                Pelanggaran::create($pelanggaran);
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
            $this->command->info('Data referensi jenis pelanggaran inserted to database');
        }
        
        if ($this->command->confirm('Seed data referensi apresiasi? [y|N]', true)) {
            $this->command->getOutput()->createProgressBar(count($apresiasi));
            $this->command->getOutput()->progressStart();
            foreach ($apresiasi as $apres) {
                Jenis_apresiasi::firstOrCreate(['name' => $apres,'created_by' => $user]);
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
            $this->command->info('Data Apresiasi inserted to database');
        }

    }
}
