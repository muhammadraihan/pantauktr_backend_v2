<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Operator_type;

class OperatorTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $operatorType = [
            ['name' => 'Kementrian'],
            ['name' => 'Pemda'],
            ['name' => 'WHO'],
        ];

        if ($this->command->confirm('Seed operator type data? [y|N]', true)) {
            $this->command->getOutput()->createProgressBar(count($operatorType));
            $this->command->getOutput()->progressStart();
            foreach ($operatorType as $opType) {
                Operator_type::create($opType);
                $this->command->getOutput()->progressAdvance();
            }
            $this->command->getOutput()->progressFinish();
            $this->command->info('Operator type data inserted to database');
        }
    }
}
