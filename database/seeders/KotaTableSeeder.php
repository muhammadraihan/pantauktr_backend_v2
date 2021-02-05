<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kota;
use App\Models\Province;
use App\Models\User;

class KotaTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      //use stream option for ssl verify false on file_get_contents function
      $stream_opts = [
      "ssl" => [
      "verify_peer"=>false,
      "verify_peer_name"=>false,
      ]];

      //url data
      $url = "https://dev.farizdotid.com/api/daerahindonesia/kota?id_provinsi=";
      $province = Province::select('province_name','province_code')->get();
      // Ask for mendownload data, default is no
        if ($this->command->confirm('Anda yakin mendownload data ?')) {
          foreach ($province as $key => $value) {
            $code = $value->province_code;
            $json = file_get_contents($url.$code, false, stream_context_create($stream_opts));
            $data = json_decode($json);
            $this->command->info('Downloading data kota from province '.$value->province_name);
            //progress bar
            $this->command->getOutput()->createProgressBar(count($data->kota_kabupaten));
            $this->command->getOutput()->progressStart();
            foreach ($data->kota_kabupaten as $object) {
                Kota::create(array(
                  'city_name' => $object->nama,
                  'city_code' => $object->id,
                  'province_code' => $object->id_provinsi,
                ));
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
            $this->command->info('Status: OK');
          }
          $this->command->warn('Data inserted to database. :)');
        }
    }
}
