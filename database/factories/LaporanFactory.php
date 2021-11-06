<?php

namespace Database\Factories;

use App\Helper\Helper;
use App\Models\BentukPelanggaran;
use App\Models\Kawasan;
use App\Models\Kota;
use App\Models\Laporan;
use App\Models\Pelanggaran;
use App\Models\Pelapor;
use App\Models\Province;
use Illuminate\Database\Eloquent\Factories\Factory;

class LaporanFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Laporan::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $jenis_pelanggaran = Pelanggaran::select('uuid')->inRandomOrder()->first();
        $bentuk_pelangaran = BentukPelanggaran::select('uuid')->where('jenis_pelanggaran', $jenis_pelanggaran->uuid)->inRandomOrder()->first();
        $kawasan = Kawasan::select('uuid')->inRandomOrder()->first();
        $pelapor = Pelapor::select('uuid')->inRandomOrder()->first();
        $kota = Kota::select('city_name')->inRandomOrder()->first();
        $propinsi = Province::select('province_name')->inRandomOrder()->first();
        $time = $this->faker->dateTimeBetween($startDate = '-3 year', $endDate = 'now', $timezone = 'Asia/Jakarta');
        $cordinate = $this->faker->localCoordinates();
        return [
            'nomor_laporan' => 'KTR' . '-' . Helper::GenerateReportNumber(13),
            'jenis_pelanggaran' => $jenis_pelanggaran->uuid,
            'bentuk_pelanggaran' => $bentuk_pelangaran->uuid,
            'kawasan' => $kawasan->uuid,
            'created_by' => $pelapor->uuid,
            'photo' => $this->faker->url(),
            'lat' => $cordinate['latitude'],
            'lng' => $cordinate['longitude'],
            'alamat' => $this->faker->address(),
            'kelurahan' => $this->faker->streetName(),
            'kecamatan' => $this->faker->citySuffix(),
            'kota' => $kota->city_name,
            'propinsi' => $propinsi->province_name,
            'negara' => 'Indonesia',
            'place_id' => $this->faker->md5(),
            'status' => $this->faker->randomElement($array = array(0, 1, 2)),
            'created_at' => $time,
        ];
    }
}
