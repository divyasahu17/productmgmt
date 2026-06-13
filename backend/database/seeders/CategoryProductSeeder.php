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
        $categoriesData = [
            'Indian Spices & Masalas' => [
                'items' => ['Turmeric', 'Cumin', 'Coriander', 'Garam Masala', 'Cardamom', 'Cloves', 'Black Pepper', 'Red Chilli', 'Mustard Seeds', 'Fenugreek', 'Cinnamon', 'Nutmeg', 'Mace', 'Bay Leaf', 'Saffron'],
                'suffix' => ['Powder', 'Whole', 'Premium', 'Organic']
            ],
            'Ethnic Wear' => [
                'items' => ['Cotton Kurta', 'Silk Saree', 'Lehenga Choli', 'Sherwani', 'Salwar Suit', 'Dupatta', 'Dhoti', 'Anarkali', 'Bandhani Saree', 'Chikankari Kurti', 'Banarasi Saree', 'Pathani Suit', 'Pashmina Shawl', 'Nehru Jacket', 'Palazzo Set'],
                'suffix' => ['for Men', 'for Women', 'Designer', 'Traditional']
            ],
            'Ayurvedic & Herbal' => [
                'items' => ['Ashwagandha', 'Triphala', 'Chyawanprash', 'Neem', 'Tulsi', 'Brahmi', 'Shatavari', 'Giloy', 'Amla', 'Shilajit', 'Kumkumadi Tailam', 'Bhringraj Oil', 'Aloe Vera Juice', 'Karela Jamun Juice', 'Isabgol'],
                'suffix' => ['Powder', 'Tablets', 'Extract', 'Pure']
            ],
            'Indian Sweets & Mithai' => [
                'items' => ['Kaju Katli', 'Gulab Jamun', 'Rasgulla', 'Soan Papdi', 'Motichoor Ladoo', 'Besan Ladoo', 'Rasmalai', 'Jalebi', 'Mysore Pak', 'Peda', 'Barfi', 'Ghevar', 'Cham Cham', 'Kalakand', 'Petha'],
                'suffix' => ['Box (1kg)', 'Box (500g)', 'Premium', 'Special']
            ],
            'Handicrafts & Decor' => [
                'items' => ['Brass Nataraja', 'Terracotta Pot', 'Madhubani Painting', 'Wooden Elephant', 'Kashmiri Carpet', 'Jaipuri Quilt', 'Dhokra Art', 'Warli Painting', 'Blue Pottery Vase', 'Sandalwood Carving', 'Meenakari Box', 'Bamboo Lamp', 'Marble Taj Mahal', 'Tanjore Painting', 'Puppet Set'],
                'suffix' => ['Handmade', 'Antique', 'Authentic', 'Showpiece']
            ],
            'Tea & Beverages' => [
                'items' => ['Darjeeling Tea', 'Assam CTC', 'Masala Chai', 'Green Tea', 'Kashmiri Kahwa', 'Filter Coffee', 'Thandai Mix', 'Rooh Afza', 'Jaljeera Powder', 'Aam Panna', 'Rose Syrup', 'Cardamom Tea', 'Ginger Tea', 'Tulsi Green Tea', 'Lemon Tea'],
                'suffix' => ['Leaves', 'Powder', 'Premium Blend', 'Organic']
            ],
            'Namkeen & Snacks' => [
                'items' => ['Bhujia', 'Moong Dal', 'Mixture', 'Khakhra', 'Chakli', 'Banana Chips', 'Mathri', 'Kachori', 'Samosa', 'Bhel Puri Mix', 'Roasted Makhana', 'Papad', 'Shakarpara', 'Chivda', 'Sev'],
                'suffix' => ['Spicy', 'Plain', 'Special', 'Family Pack']
            ],
            'Pooja Items' => [
                'items' => ['Incense Sticks (Agarbatti)', 'Dhoop Cones', 'Brass Diya', 'Camphor (Kapur)', 'Sandalwood Paste', 'Kumkum', 'Haldi', 'Moli (Kalawa)', 'Rudraksha Mala', 'Ganga Jal', 'Pooja Thali', 'Cotton Wicks', 'Ghee Diya', 'Bell (Ghanti)', 'Idol (Murti)'],
                'suffix' => ['Pack', 'Pure', 'Premium', 'Set']
            ],
            'Indian Books & Literature' => [
                'items' => ['Bhagavad Gita', 'Mahabharata', 'Ramayana', 'Panchatantra', 'Chanakya Neeti', 'Discovery of India', 'God of Small Things', 'Malgudi Days', 'Gitanjali', 'Wings of Fire', 'Midnight\'s Children', 'Shiva Trilogy', 'White Tiger', 'Train to Pakistan', 'Autobiography of a Yogi'],
                'suffix' => ['Paperback', 'Hardcover', 'English Translation', 'Original']
            ],
            'Beauty & Personal Care' => [
                'items' => ['Sandalwood Soap', 'Multani Mitti', 'Rose Water', 'Coconut Hair Oil', 'Henna (Mehendi) Powder', 'Ubtan Face Wash', 'Neem Soap', 'Almond Oil', 'Shikakai Powder', 'Reetha', 'Amla Hair Oil', 'Kajal', 'Turmeric Cream', 'Jasmine Hair Oil', 'Kumkumadi Serum'],
                'suffix' => ['Natural', 'Organic', 'Herbal', 'Pure']
            ]
        ];

        $faker = \Faker\Factory::create();

        foreach ($categoriesData as $categoryName => $data) {
            $categoryId = DB::table('categories')->insertGetId([
                'name' => $categoryName,
                'slug' => Str::slug($categoryName) . '-' . Str::random(5),
                'description' => 'Explore our wide range of ' . $categoryName . '. Authentic and high quality.',
                'status' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $products = [];
            foreach ($data['items'] as $item) {
                $suffix = $data['suffix'][array_rand($data['suffix'])];
                $productName = $item . ' - ' . $suffix;
                $products[] = [
                    'category_id' => $categoryId,
                    'name' => current(explode(' - ', $productName)) . ' ' . Str::random(3), // to ensure uniqueness mostly
                    'slug' => Str::slug($productName) . '-' . Str::random(5),
                    'description' => $faker->paragraph(2),
                    'price' => $faker->numberBetween(50, 5000), // Prices in INR
                    'stock' => $faker->numberBetween(10, 200),
                    'status' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            // Update exact name back
            foreach ($products as $i => $p) {
                $products[$i]['name'] = $data['items'][$i] . ' ' . $data['suffix'][array_rand($data['suffix'])];
            }
            
            DB::table('products')->insert($products);
        }
    }
}
