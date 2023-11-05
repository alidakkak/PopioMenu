<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

         \App\Models\User::factory()->create([
             'name' => 'Admin',
             'email' => 'admin@gmail.com',
             'password' => '00000000'
         ]);

        \App\Models\Category::create([
            'name' => 'Drink',
            'name_ar' => 'مشروبات'
        ]);

        \App\Models\Product::create([
            'name' => 'Orange juice',
            'name_ar' => 'عصير برتقال',
            'category_id' => 1
        ]);

        \App\Models\Size::create([
            'size' => 'Large',
            'size_ar' => 'كبير',
            'price' => 10,
            'product_id' => 1
        ]);

        \App\Models\Size::create([
            'size' => 'Small',
            'size_ar' => 'صغير',
            'price' => 5,
            'product_id' => 1
        ]);

    }
}
