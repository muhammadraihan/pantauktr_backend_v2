<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kota;

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
      // $id = 11; // id province
      $id = $this->command->ask('Enter Provinces Id');
      $json = file_get_contents($url.$id, false, stream_context_create($stream_opts));
    //   dd($json);
      // Ask for mendownload data, default is no
        if ($this->command->confirm('Anda yakin mendownload data ?')) {
            $data = json_decode($json);
            // dd($data);
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
            $this->command->info('Here is your datasource:');
            $this->command->warn($url);
            $this->command->info('Here is province id:');
            $this->command->warn($id);
            $this->command->info('Status:');
            $this->command->warn('Data inserted to database. :)');
        }
    }
}
