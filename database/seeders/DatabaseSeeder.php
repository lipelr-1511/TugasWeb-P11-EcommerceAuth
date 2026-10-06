<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /** Katalog realistis: 5 kategori x 12 produk = 60 produk. [judul, harga (Rp)] */
    private const CATALOG = [
        'Elektronik' => [
            ['Smartphone Samsung Galaxy A15 128GB', 2799000], ['Xiaomi Redmi Note 13 8/256GB', 3299000],
            ['Laptop ASUS Vivobook 14 Core i5', 9499000], ['Laptop Acer Aspire 5 Ryzen 5', 8299000],
            ['Smart TV LG 43 Inch 4K UHD', 4699000], ['Earphone TWS JBL Wave Buds', 749000],
            ['Headphone Sony WH-CH520', 799000], ['Powerbank Anker 10000mAh', 429000],
            ['Smartwatch Amazfit Bip 5', 899000], ['Mouse Wireless Logitech M331', 219000],
            ['Keyboard Mechanical Rexus Daxa M84', 489000], ['Speaker Bluetooth JBL Flip 6', 1999000],
        ],
        'Fashion Pria' => [
            ['Kemeja Putih Slim Fit Lengan Panjang', 189000], ['Kaos Polos Cotton Combed 30s', 79000],
            ['Celana Chino Slim Fit Cokelat', 229000], ['Jaket Bomber Hitam Water Resistant', 349000],
            ['Hoodie Zipper Fleece Abu-abu', 259000], ['Sepatu Sneakers Putih Kulit Sintetis', 399000],
            ['Jam Tangan Digital Casio F-91W', 289000], ['Ikat Pinggang Kulit Asli Hitam', 149000],
            ['Celana Jeans Regular Biru Dongker', 279000], ['Batik Lengan Panjang Motif Parang', 239000],
            ['Topi Baseball Cap Hitam', 69000], ['Dompet Kulit Bifold Cokelat Tua', 179000],
        ],
        'Fashion Wanita' => [
            ['Dress Floral Midi Rayon', 199000], ['Blouse Satin Lengan Balon Krem', 169000],
            ['Hijab Pashmina Ceruty Babydoll', 59000], ['Rok Plisket Panjang Hitam', 129000],
            ['Tas Selempang Wanita Kulit Sintetis', 249000], ['Flat Shoes Wanita Nyaman', 179000],
            ['Cardigan Rajut Oversize Mocha', 159000], ['Kulot Linen Wanita Sage', 139000],
            ['Gamis Syari Polos Dusty Pink', 289000], ['Heels Block 5 cm Nude', 219000],
            ['Kacamata Fashion Anti Radiasi', 99000], ['Totebag Kanvas Premium Natural', 89000],
        ],
        'Peralatan Rumah' => [
            ['Rice Cooker Miyako 1.8 Liter', 389000], ['Blender Philips HR2115', 649000],
            ['Setrika Uap Philips GC1905', 349000], ['Kipas Angin Berdiri Cosmos 16 Inch', 399000],
            ['Dispenser Air Sharp Hot & Normal', 799000], ['Set Panci Anti Lengket 5 Pcs', 459000],
            ['Lampu LED Philips 14W Pack 4', 129000], ['Sapu dan Pengki Set Premium', 59000],
            ['Rak Sepatu Susun 4 Tingkat', 149000], ['Vacuum Cleaner Mini Sharp EC-NS18', 899000],
            ['Teko Listrik Kirin 1.8 Liter', 169000], ['Set Sprei Katun Jepang 160x200', 279000],
        ],
        'Olahraga' => [
            ['Sepatu Lari Nike Revolution 7', 1099000], ['Matras Yoga TPE 6mm Anti Slip', 119000],
            ['Dumbbell Vinyl Set 10 Kg', 249000], ['Raket Badminton Yonex Astrox 01', 599000],
            ['Bola Futsal Mikasa Original', 329000], ['Jersey Dry-Fit Running Pria', 129000],
            ['Botol Minum Tumbler 1 Liter BPA Free', 99000], ['Sepeda Lipat 20 Inch Pacific', 2899000],
            ['Resistance Band Set 5 Level', 89000], ['Tas Gym Duffel Waterproof', 189000],
            ['Skipping Rope Speed Bearing', 59000], ['Sarung Tangan Gym Half Finger', 79000],
        ],
    ];

    private const TAGS = ['Terbaru', 'Diskon', 'Best Seller', 'Garansi Resmi', 'Gratis Ongkir', 'Stok Terbatas'];

    public function run(): void
    {
        // --- Users (3 role) : password semua akun = "password" ---
        $admin  = User::factory()->admin()->create(['name' => 'Admin Toko', 'email' => 'admin@example.com']);
        $editor = User::factory()->editor()->create(['name' => 'Editor Toko', 'email' => 'editor@example.com']);
        User::factory()->editor()->create(['name' => 'Editor Dua', 'email' => 'editor2@example.com']);
        User::factory()->create(['name' => 'Pelanggan Satu', 'email' => 'user@example.com']);
        User::factory(5)->create();

        // --- Kategori & Tag ---
        $categories = collect(self::CATALOG)->keys()->mapWithKeys(fn ($name) => [
            $name => Category::create(['name' => $name, 'slug' => Str::slug($name)]),
        ]);
        $tags = collect(self::TAGS)->map(fn ($name) => Tag::create(['name' => $name]));

        // --- 60 Produk realistis ---
        $owners = User::whereIn('role', ['admin', 'editor'])->get();

        foreach (self::CATALOG as $categoryName => $items) {
            foreach ($items as [$title, $price]) {
                $product = Product::create([
                    'category_id' => $categories[$categoryName]->id,
                    'user_id'     => $owners->random()->id,
                    'title'       => $title,
                    'description' => "{$title} - produk {$categoryName} berkualitas dengan harga terbaik. "
                                     .'Barang baru, dikemas rapi, dan siap kirim ke seluruh Indonesia.',
                    'price'       => $price,
                    'stock'       => fake()->numberBetween(0, 80),
                ]);
                $product->tags()->attach($tags->random(rand(1, 3))->pluck('id'));
            }
        }

        // --- Order + Order Items untuk setiap pelanggan ---
        $products = Product::inStock()->get();

        User::where('role', 'user')->get()->each(function (User $customer) use ($products) {
            for ($i = 0; $i < rand(1, 3); $i++) {
                $order = Order::create([
                    'user_id' => $customer->id,
                    'status'  => collect(['pending', 'paid', 'paid', 'cancelled'])->random(),
                ]);

                $total = 0;
                foreach ($products->random(rand(1, 4)) as $product) {
                    $qty = rand(1, 3);
                    $order->items()->create([
                        'product_id' => $product->id,
                        'qty'        => $qty,
                        'price'      => $product->price,
                    ]);
                    $total += $qty * $product->price;
                }
                $order->update(['total_price' => $total]);
            }
        });
    }
}
