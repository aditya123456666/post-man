<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Laptop ASUS ROG Strix G15',
                'description' => 'Laptop gaming performa tinggi dengan prosesor AMD Ryzen 7 dan VGA NVIDIA RTX 3060.',
                'price' => 18500000,
                'stock' => 8,
                'image' => 'products/laptop-rog.svg',
            ],
            [
                'name' => 'Mouse Gaming Logitech G102 Lightsync',
                'description' => 'Mouse gaming RGB presisi tinggi dengan sensor 8.000 DPI dan 6 tombol yang dapat diprogram.',
                'price' => 285000,
                'stock' => 35,
                'image' => 'products/mouse-gaming.svg',
            ],
            [
                'name' => 'Keyboard Mechanical Keychron K2 Wireless',
                'description' => 'Keyboard mekanikal Bluetooth dengan Gateron Brown Switch dan tata letak ringkas 75%.',
                'price' => 1350000,
                'stock' => 15,
                'image' => 'products/keyboard-mechanical.svg',
            ],
            [
                'name' => 'Monitor Gaming LG UltraGear 27 Inch',
                'description' => 'Monitor layar IPS FHD 144Hz 1ms dengan dukungan AMD FreeSync Premium.',
                'price' => 3299000,
                'stock' => 10,
                'image' => 'products/monitor-gaming.svg',
            ],
            [
                'name' => 'Headset Gaming HyperX Cloud II',
                'description' => 'Headset audio surround sound 7.1 dengan memory foam yang sangat nyaman untuk penggunaan lama.',
                'price' => 1150000,
                'stock' => 20,
                'image' => 'products/headset-gaming.svg',
            ],
            [
                'name' => 'SSD NVMe M.2 Samsung 980 PRO 1TB',
                'description' => 'Penyimpanan PCIe 4.0 super cepat dengan kecepatan baca hingga 7.000 MB/s.',
                'price' => 1650000,
                'stock' => 25,
                'image' => 'products/ssd-nvme.svg',
            ],
            [
                'name' => 'Webcam Logitech C920 HD Pro',
                'description' => 'Webcam Full HD 1080p untuk streaming dan konferensi video dengan stereo audio jernih.',
                'price' => 980000,
                'stock' => 12,
                'image' => 'products/webcam-hd.svg',
            ],
            [
                'name' => 'RAM Corsair Vengeance RGB Pro 16GB (2x8GB)',
                'description' => 'Memori DDR4 3200MHz berkecepatan tinggi dengan pencahayaan RGB dinamis.',
                'price' => 920000,
                'stock' => 18,
                'image' => 'products/ram-rgb.svg',
            ],
            [
                'name' => 'Chair Gaming Secretlab TITAN Evo 2022',
                'description' => 'Kursi ergonomis kelas premium berbahan kulit sintetis SoftWeave untuk kenyamanan maksimal.',
                'price' => 6700000,
                'stock' => 5,
                'image' => 'products/gaming-chair.svg',
            ],
            [
                'name' => 'Microphone Condenser Shure MV7 USB/XLR',
                'description' => 'Mikrofon profesional untuk podcasting, recording, dan live streaming dengan kontrol panel sentuh.',
                'price' => 4200000,
                'stock' => 7,
                'image' => 'products/microphone-studio.svg',
            ],
        ];

        foreach ($products as $product) {
            $slug = Str::slug($product['name']);
            $seededProduct = Product::firstOrCreate(
                ['slug' => $slug],
                [...$product, 'slug' => $slug],
            );

            if (! $seededProduct->image) {
                $seededProduct->update(['image' => $product['image']]);
            }
        }
    }
}
