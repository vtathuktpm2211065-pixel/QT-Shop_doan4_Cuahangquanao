<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        Category::updateOrCreate(
    ['id' => 1],
    ['name' => 'Do nu', 'slug' => 'do-nu']
);
Category::updateOrCreate(
    ['id' => 2],
    ['name' => 'Do nam', 'slug' => 'do-nam']
);
Category::updateOrCreate(
    ['id' => 3],
    ['name' => 'Tre em', 'slug' => 'tre-em']
);

    }
}
