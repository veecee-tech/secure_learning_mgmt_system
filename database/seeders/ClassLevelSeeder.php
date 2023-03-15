<?php

namespace Database\Seeders;

use App\Models\ClassLevel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClassLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        $classes = ['JSS1', 'JSS2', 'JSS3', 'SS1', 'SS2', 'SS3'];

        foreach ($classes as $class) {
            ClassLevel::create(['name' => $class]);
        }
    }
}
