<?php

namespace Database\Seeders;

use App\Models\BcConseiller;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ConseillerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        BcConseiller::factory(10)->create();
    }
}
