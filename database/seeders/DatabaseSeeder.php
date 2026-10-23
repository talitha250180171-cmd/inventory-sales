<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Akun demo
        User::create([
            'name' => 'Admin Toko',
            'email' => 'admin@example.com',
            'password' => 'password123',
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Staff Toko',
            'email' => 'staff@example.com',
            'password' => 'password123',
            'role' => 'staff',
        ]);

        // Kategori
        foreach (['Minuman', 'Makanan Ringan', 'Kebutuhan Rumah Tangga', 'Alat Tulis'] as $name) {
            Category::create(['name' => $name]);
        }

        // Supplier
        Supplier::create([
            'name' => 'PT Sumber Makmur',
            'phone' => '0811111111',
            'email' => 'sales@sumbermakmur.test',
            'address' => 'Jl. Merdeka No. 1, Lhokseumawe',
        ]);
        Supplier::create([
            'name' => 'CV Jaya Abadi',
            'phone' => '0822222222',
            'email' => 'order@jayaabadi.test',
            'address' => 'Jl. Pase No. 5, Lhokseumawe',
        ]);
        Supplier::create([
            'name' => 'UD Berkah Niaga',
            'phone' => '0833333333',
            'email' => null,
            'address' => 'Jl. Iskandar Muda No. 9, Lhokseumawe',
        ]);

        // Produk: [sku, nama, kategori, harga, stok, stok minimum]
        $categoryIds = Category::pluck('id', 'name');

        $products = [
            ['MNM-001', 'Air Mineral 600ml', 'Minuman', 3500, 120, 24],
            ['MNM-002', 'Teh Botol 350ml', 'Minuman', 5000, 80, 20],
            ['MKN-001', 'Keripik Kentang 70g', 'Makanan Ringan', 12000, 45, 10],
            ['MKN-002', 'Biskuit Cokelat 120g', 'Makanan Ringan', 9500, 3, 10],
            ['RT-001', 'Sabun Cuci Piring 800ml', 'Kebutuhan Rumah Tangga', 18500, 30, 8],
            ['RT-002', 'Deterjen Bubuk 1kg', 'Kebutuhan Rumah Tangga', 22000, 25, 8],
            ['ATK-001', 'Pulpen Hitam', 'Alat Tulis', 3000, 200, 50],
            ['ATK-002', 'Buku Tulis 38 Lembar', 'Alat Tulis', 4500, 150, 40],
        ];

        foreach ($products as [$sku, $name, $category, $price, $stock, $minStock]) {
            Product::create([
                'category_id' => $categoryIds[$category],
                'sku' => $sku,
                'name' => $name,
                'price' => $price,
                'stock' => $stock,
                'min_stock' => $minStock,
            ]);
        }
    }
}