<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CategoryProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function up()
    {
        // Not used, down/up shouldn't be here, but let's just write run()
    }

    public function run()
    {
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10; $i++) {
            $categoryName = $faker->unique()->words(2, true);
            
            $categoryId = DB::table('categories')->insertGetId([
                'name' => ucwords($categoryName),
                'slug' => Str::slug($categoryName) . '-' . Str::random(5),
                'description' => $faker->sentence(10),
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $products = [];
            for ($j = 0; $j < 15; $j++) {
                $productName = $faker->unique()->words(3, true);
                $products[] = [
                    'category_id' => $categoryId,
                    'name' => ucwords($productName),
                    'slug' => Str::slug($productName) . '-' . Str::random(5),
                    'description' => $faker->paragraph(3),
                    'price' => $faker->randomFloat(2, 10, 1000),
                    'stock' => $faker->numberBetween(10, 100),
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            
            DB::table('products')->insert($products);
        }
    }
}
