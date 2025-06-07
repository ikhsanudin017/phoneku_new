<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Product;
use App\Models\Profile;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin PhoneKu',
            'email' => 'admin@phoneku.com',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        // Create sample customer
        $customer = User::create([
            'name' => 'John Doe',
            'email' => 'customer@phoneku.com',
            'password' => Hash::make('customer123'),
            'role' => 'customer',
        ]);

        // Create profile for customer
        Profile::create([
            'user_id' => $customer->id,
            'username' => 'johndoe',
            'recipient_name' => 'John Doe',
            'address' => 'Jl. Sudirman No. 123, Jakarta',
            'phone' => '081234567890',
            'label' => 'Rumah',
            'gender' => 'male',
            'birth_day' => 15,
            'birth_month' => 6,
            'birth_year' => 1990,
        ]);

        // Create sample smartphones
        $smartphones = [
            [
                'name' => 'iPhone 15 Pro Max',
                'description' => 'Latest iPhone with A17 Pro chip, titanium design, and advanced camera system.',
                'price' => 19999000,
                'original_price' => 21999000,
                'category' => 'handphone',
                'brand' => 'Apple',
                'stock' => 50,
                'is_featured' => true,
                'specifications' => json_encode([
                    'display' => '6.7-inch Super Retina XDR',
                    'processor' => 'A17 Pro chip',
                    'storage' => '256GB',
                    'camera' => '48MP Main + 12MP Ultra Wide + 12MP Telephoto',
                    'battery' => 'Up to 29 hours video playback'
                ])
            ],
            [
                'name' => 'Samsung Galaxy S24 Ultra',
                'description' => 'Premium Android flagship with S Pen, 200MP camera, and AI features.',
                'price' => 18999000,
                'original_price' => 20999000,
                'category' => 'handphone',
                'brand' => 'Samsung',
                'stock' => 45,
                'is_featured' => true,
                'specifications' => json_encode([
                    'display' => '6.8-inch Dynamic AMOLED 2X',
                    'processor' => 'Snapdragon 8 Gen 3',
                    'storage' => '256GB',
                    'camera' => '200MP Main + 50MP Periscope + 12MP Ultra Wide + 10MP Telephoto',
                    'battery' => '5000mAh'
                ])
            ],
            [
                'name' => 'Google Pixel 8 Pro',
                'description' => 'AI-powered photography and pure Android experience.',
                'price' => 14999000,
                'original_price' => 16999000,
                'category' => 'handphone',
                'brand' => 'Google',
                'stock' => 30,
                'is_featured' => true,
                'specifications' => json_encode([
                    'display' => '6.7-inch LTPO OLED',
                    'processor' => 'Google Tensor G3',
                    'storage' => '128GB',
                    'camera' => '50MP Main + 48MP Ultra Wide + 48MP Telephoto',
                    'battery' => '5050mAh'
                ])
            ],
            [
                'name' => 'Xiaomi 14 Ultra',
                'description' => 'Flagship camera phone with Leica partnership.',
                'price' => 12999000,
                'original_price' => 14999000,
                'category' => 'handphone',
                'brand' => 'Xiaomi',
                'stock' => 40,
                'is_featured' => true,
                'specifications' => json_encode([
                    'display' => '6.73-inch LTPO AMOLED',
                    'processor' => 'Snapdragon 8 Gen 3',
                    'storage' => '256GB',
                    'camera' => '50MP Main + 50MP Ultra Wide + 50MP Periscope + 50MP Telephoto',
                    'battery' => '5300mAh'
                ])
            ]
        ];

        foreach ($smartphones as $phone) {
            Product::create($phone);
        }

        // Create sample accessories
        $accessories = [
            [
                'name' => 'AirPods Pro (2nd Gen)',
                'description' => 'Active Noise Cancellation, Transparency mode, and spatial audio.',
                'price' => 3999000,
                'original_price' => 4499000,
                'category' => 'accessory',
                'brand' => 'Apple',
                'stock' => 100,
                'is_featured' => true,
                'specifications' => json_encode([
                    'type' => 'Wireless Earbuds',
                    'battery' => 'Up to 6 hours listening time',
                    'features' => 'Active Noise Cancellation, Transparency mode',
                    'connectivity' => 'Bluetooth 5.3'
                ])
            ],
            [
                'name' => 'Samsung Galaxy Buds2 Pro',
                'description' => 'Premium wireless earbuds with intelligent ANC.',
                'price' => 2999000,
                'original_price' => 3499000,
                'category' => 'accessory',
                'brand' => 'Samsung',
                'stock' => 80,
                'is_featured' => true,
                'specifications' => json_encode([
                    'type' => 'Wireless Earbuds',
                    'battery' => 'Up to 8 hours listening time',
                    'features' => 'Intelligent ANC, 360 Audio',
                    'connectivity' => 'Bluetooth 5.3'
                ])
            ],
            [
                'name' => 'Anker PowerCore 10000',
                'description' => 'Compact portable charger with fast charging technology.',
                'price' => 599000,
                'original_price' => 699000,
                'category' => 'accessory',
                'brand' => 'Anker',
                'stock' => 150,
                'is_featured' => true,
                'specifications' => json_encode([
                    'type' => 'Power Bank',
                    'capacity' => '10000mAh',
                    'output' => '18W Fast Charging',
                    'ports' => 'USB-A, USB-C'
                ])
            ],
            [
                'name' => 'Spigen Tough Armor Case',
                'description' => 'Military-grade protection with dual-layer design.',
                'price' => 299000,
                'original_price' => 399000,
                'category' => 'accessory',
                'brand' => 'Spigen',
                'stock' => 200,
                'is_featured' => false,
                'specifications' => json_encode([
                    'type' => 'Phone Case',
                    'protection' => 'Military Grade Drop Protection',
                    'material' => 'TPU + PC',
                    'features' => 'Kickstand, Wireless Charging Compatible'
                ])
            ]
        ];

        foreach ($accessories as $accessory) {
            Product::create($accessory);
        }

        echo "Database seeded successfully!\n";
        echo "Admin credentials: admin@phoneku.com / admin123\n";
        echo "Customer credentials: customer@phoneku.com / customer123\n";
    }
}
