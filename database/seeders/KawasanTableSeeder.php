<?php

namespace Database\Seeders;

use App\Models\Kawasan;
use App\Models\User;
use Illuminate\Database\Seeder;

class KawasanTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::where('name', '=', 'superadmin')->first()->uuid;

        $kawasans = [
            ['kawasan' => 'Fasilitas pelayanan kesehatan', 'keterangan' => 'Tempat yang digunakan untuk menyelenggarakan upaya pelayanan kesehatan, baik promotif, preventif, kuratif maupun rehabilitatif yang dilakukan oleh pemerintah, pemerintah daerah dan/atau masyarakat', 'created_by' => $user],
            ['kawasan' => 'Tempat proses belajar mengajar', 'keterangan' => 'Sarana yang digunakan untuk kegiatan belajar, mengajar, pendidikan dan/atau pelatihan', 'created_by' => $user],
            ['kawasan' => 'Tempat Bermain Anak', 'keterangan' => 'Area baik tertutup maupun terbuka yang digunakan untuk kegiatan bermain anak-anak', 'created_by' => $user],
            ['kawasan' => 'Tempat Ibadah', 'keterangan' => 'Bangunan atau ruang tertutup yang memiliki ciri-ciri tertentu yang khusus dipergunakan untuk beribadah bagi para pemeluk masing-masing agama secara permanen, tidak termasuk tempat ibadah keluarga', 'created_by' => $user],
            ['kawasan' => 'Tempat kerja', 'keterangan' => 'Ruang atau lapangan tertutup atau terbuka, bergerak atau tetap dimana tenaga kerja bekerja, atau yang dimasuki tenaga kerja untuk keperluan suatu usaha dan dimana terdapat sumber atau sumber-sumber bahaya', 'created_by' => $user],
            ['kawasan' => 'Tempat Umum', 'keterangan' => 'Semua tempat tertutup yang dapat diakses oleh masyarakat umum dan/atau tempat yang dapat dimanfaatkan bersama-sama untuk kegiatan masyarakat yang dikelola oleh pemerintah, swasta dan masyarakat', 'created_by' => $user],
            ['kawasan' => 'Angkutan Umum', 'keterangan' => 'Alat angkutan bagi masyarakat yang dapat berupa kendaraan darat, air dan udara biasanya dengan kompensasi', 'created_by' => $user],
        ];

        if ($this->command->confirm('Seed data referensi kawasan? [y|N]', true)) {
            $this->command->getOutput()->createProgressBar(count($kawasans));
            $this->command->getOutput()->progressStart();
            foreach ($kawasans as $kawasan) {
                Kawasan::create($kawasan);
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
            $this->command->info('Data referensi kawasan inserted to database');
        }
    }
}
