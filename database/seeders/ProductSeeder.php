<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $products = [
            [
                'name' => 'Esensi Oud Royal',
                'price' => 4500000,
                'category' => 'Oud Premium',
                'brand' => 'Nura Oud Essence',
                'scent' => 'Oud, Amber, Kayu Cendana',
                'description' => 'Campuran mewah dari kayu oud langka dan aroma amber hangat, menciptakan aroma tanda tangan yang tak terlupakan.',
                'image' => 'products/product-1.jpg',
                'rating' => 4.9,
                'reviews' => 127,
                'stock' => 45,
                'bestseller' => true,
                'volume' => '100ml'
            ],
            [
                'name' => 'Romansa Rose Oud',
                'price' => 3750000,
                'category' => 'Oud Floral',
                'brand' => 'Nura Oud Essence',
                'scent' => 'Mawar, Oud, Melati',
                'description' => 'Fusi memikat dari kelopak mawar Damaskus dan oud berharga, sempurna untuk malam elegan.',
                'image' => 'products/product-2.jpg',
                'rating' => 4.8,
                'reviews' => 98,
                'stock' => 32,
                'bestseller' => true,
                'volume' => '50ml'
            ],
            [
                'name' => 'Prestise Amber Oud',
                'price' => 5250000,
                'category' => 'Oud Premium',
                'brand' => 'Nura Oud Essence',
                'scent' => 'Amber, Oud, Musk',
                'description' => 'Amber kaya dan oud menciptakan aroma yang kuat dan tahan lama untuk para peminat yang cerdas.',
                'image' => 'products/product-3.jpg',
                'rating' => 4.9,
                'reviews' => 156,
                'stock' => 28,
                'bestseller' => true,
                'volume' => '100ml'
            ],
            [
                'name' => 'Mistik Sandalwood Oud',
                'price' => 4200000,
                'category' => 'Oud Kayu',
                'brand' => 'Nura Oud Essence',
                'scent' => 'Kayu Cendana, Oud, Cedar',
                'description' => 'Kayu cendana krimi yang harmonis dicampur dengan oud tua untuk aroma kayu yang canggih.',
                'image' => 'products/product-4.jpg',
                'rating' => 4.7,
                'reviews' => 89,
                'stock' => 41,
                'bestseller' => false,
                'volume' => '75ml'
            ],
            [
                'name' => 'Elegansi Musk Oud',
                'price' => 3450000,
                'category' => 'Oud Musk',
                'brand' => 'Nura Oud Essence',
                'scent' => 'Musk Putih, Oud, Vanilla',
                'description' => 'Campuran lembut namun memikat dari musk putih dan oud dengan sentuhan manis vanilla.',
                'image' => 'products/product-1.jpg',
                'rating' => 4.6,
                'reviews' => 73,
                'stock' => 55,
                'bestseller' => false,
                'volume' => '50ml'
            ],
            [
                'name' => 'Royale Saffron Oud',
                'price' => 6000000,
                'category' => 'Oud Premium',
                'brand' => 'Nura Oud Essence',
                'scent' => 'Saffron, Oud, Kulit',
                'description' => 'Benang saffron mewah bertemu dengan oud kaya dan aroma kulit dalam komposisi kerajaan ini.',
                'image' => 'products/product-2.jpg',
                'rating' => 5.0,
                'reviews' => 142,
                'stock' => 18,
                'bestseller' => true,
                'volume' => '100ml'
            ]
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
