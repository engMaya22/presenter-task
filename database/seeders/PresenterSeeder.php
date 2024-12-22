<?php

namespace Database\Seeders;

use App\Models\Presenter;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PresenterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Presenter::factory()->count(20)->create();

    }
}
