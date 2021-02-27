<?php

namespace Database\Seeders;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\Order;
use Botble\Ecommerce\Models\OrderAddress;
use Botble\Ecommerce\Models\OrderHistory;
use Botble\Ecommerce\Models\OrderProduct;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductVariation;
use Botble\Ecommerce\Models\ProductVariationItem;
use Botble\Ecommerce\Models\Shipment;
use Botble\Ecommerce\Models\ShipmentHistory;
use Botble\Ecommerce\Models\Wishlist;
use Botble\Slug\Models\Slug;
use Faker\Factory;
use File;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use SlugHelper;

class ProductSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('products');

        $faker = Factory::create();

        $products = [
            [
                'name'        => 'Dual Camera 20MP',
                'price'       => 80.25,
                'is_featured' => true,
            ],
            [
                'name'        => 'Smart Watches',
                'price'       => 40.5,
                'sale_price'  => 35,
                'is_featured' => true,
            ],
            [
                'name'        => 'Beat Headphone',
                'price'       => 20,
                'is_featured' => true,
            ],
            [
                'name'        => 'Red & Black Headphone',
                'price'       => $faker->numberBetween(50, 60),
                'is_featured' => true,
            ],
            [
                'name'        => 'Smart Watch External',
                'price'       => $faker->numberBetween(70, 90),
                'is_featured' => true,
            ],
            [
                'name'        => 'Nikon HD camera',
                'price'       => $faker->numberBetween(40, 50),
                'is_featured' => true,
            ],
            [
                'name'        => 'Audio Equipment',
                'price'       => $faker->numberBetween(50, 60),
                'is_featured' => true,
            ],
            [
                'name'        => 'Smart Televisions',
                'price'       => $faker->numberBetween(110, 130),
                'sale_price'  => $faker->numberBetween(80, 100),
                'is_featured' => true,
            ],
            [
                'name'        => 'Samsung Smart Phone',
                'price'       => $faker->numberBetween(50, 60),
                'is_featured' => true,
            ],
            [
                'name'       => 'Herschel Leather Duffle Bag In Brown Color',
                'price'      => $faker->numberBetween(110, 130),
                'sale_price' => $faker->numberBetween(80, 100),
            ],
            [
                'name'       => 'Xbox One Wireless Controller Black Color',
                'price'      => $faker->numberBetween(110, 130),
                'sale_price' => $faker->numberBetween(80, 100),
            ],
            [
                'name'  => 'EPSION Plaster Printer',
                'price' => $faker->numberBetween(50, 60),
            ],
            [
                'name'  => 'Sound Intone I65 Earphone White Version',
                'price' => $faker->numberBetween(50, 60),
            ],
            [
                'name'  => 'B&O Play Mini Bluetooth Speaker',
                'price' => $faker->numberBetween(50, 60),
            ],
            [
                'name'  => 'Apple MacBook Air Retina 13.3-Inch Laptop',
                'price' => $faker->numberBetween(50, 60),
            ],
            [
                'name'  => 'Apple MacBook Air Retina 12-Inch Laptop',
                'price' => $faker->numberBetween(50, 60),
            ],
            [
                'name'  => 'Samsung Gear VR Virtual Reality Headset',
                'price' => $faker->numberBetween(50, 60),
            ],
            [
                'name'       => 'Aveeno Moisturizing Body Shower 450ml',
                'price'      => $faker->numberBetween(110, 130),
                'sale_price' => $faker->numberBetween(80, 100),
            ],
            [
                'name'       => 'NYX Beauty Couton Pallete Makeup 12',
                'price'      => $faker->numberBetween(110, 130),
                'sale_price' => $faker->numberBetween(80, 100),
            ],
            [
                'name'       => 'NYX Beauty Couton Pallete Makeup 12',
                'price'      => $faker->numberBetween(110, 130),
                'sale_price' => $faker->numberBetween(80, 100),
            ],
            [
                'name'       => 'MVMTH Classical Leather Watch In Black',
                'price'      => '62.35',
                'sale_price' => '57.99',
            ],
            [
                'name'       => 'Baxter Care Hair Kit For Bearded Mens',
                'price'      => '125.17',
                'sale_price' => '93.59',
            ],
            [
                'name'       => 'Ciate Palemore Lipstick Bold Red Color',
                'price'      => '66.78',
                'sale_price' => '42.33',
            ],
        ];

        Product::truncate();
        DB::table('ec_product_with_attribute_set')->truncate();
        DB::table('ec_product_with_attribute')->truncate();
        DB::table('ec_product_variations')->truncate();
        DB::table('ec_product_variation_items')->truncate();
        DB::table('ec_product_collection_products')->truncate();
        DB::table('ec_product_category_product')->truncate();
        DB::table('ec_product_related_relations')->truncate();
        Slug::where('reference_type', Product::class)->delete();
        Wishlist::truncate();
        Order::truncate();
        OrderAddress::truncate();
        OrderProduct::truncate();
        OrderHistory::truncate();
        Shipment::truncate();
        ShipmentHistory::truncate();

        foreach ($products as $key => $item) {
            $item['description'] = '<ul><li> Unrestrained and portable active stereo speaker</li>
            <li> Free from the confines of wires and chords</li>
            <li> 20 hours of portable capabilities</li>
            <li> Double-ended Coil Cord with 3.5mm Stereo Plugs Included</li>
            <li> 3/4″ Dome Tweeters: 2X and 4″ Woofer: 1X</li></ul>';
            $item['content'] = '<p>Short Hooded Coat features a straight body, large pockets with button flaps, ventilation air holes, and a string detail along the hemline. The style is completed with a drawstring hood, featuring Rains&rsquo; signature built-in cap. Made from waterproof, matte PU, this lightweight unisex rain jacket is an ode to nostalgia through its classic silhouette and utilitarian design details.</p>
                                <p>- Casual unisex fit</p>

                                <p>- 64% polyester, 36% polyurethane</p>

                                <p>- Water column pressure: 4000 mm</p>

                                <p>- Model is 187cm tall and wearing a size S / M</p>

                                <p>- Unisex fit</p>

                                <p>- Drawstring hood with built-in cap</p>

                                <p>- Front placket with snap buttons</p>

                                <p>- Ventilation under armpit</p>

                                <p>- Adjustable cuffs</p>

                                <p>- Double welted front pockets</p>

                                <p>- Adjustable elastic string at hempen</p>

                                <p>- Ultrasonically welded seams</p>

                                <p>This is a unisex item, please check our clothing &amp; footwear sizing guide for specific Rains jacket sizing information. RAINS comes from the rainy nation of Denmark at the edge of the European continent, close to the ocean and with prevailing westerly winds; all factors that contribute to an average of 121 rain days each year. Arising from these rainy weather conditions comes the attitude that a quick rain shower may be beautiful, as well as moody- but first and foremost requires the right outfit. Rains focus on the whole experience of going outside on rainy days, issuing an invitation to explore even in the most mercurial weather.</p>';
            $item['status'] = BaseStatusEnum::PUBLISHED;
            $item['sku'] = 'SW-' . $faker->numberBetween(100, 200);
            $item['brand_id'] = $faker->numberBetween(1, 7);
            $item['tax_id'] = 1;
            $item['views'] = $faker->numberBetween(1000, 200000);
            $item['quantity'] = $faker->numberBetween(10, 20);
            $item['length'] = $faker->numberBetween(10, 20);
            $item['wide'] = $faker->numberBetween(10, 20);
            $item['height'] = $faker->numberBetween(10, 20);
            $item['weight'] = $faker->numberBetween(500, 900);
            $item['with_storehouse_management'] = true;

            $images = [
                'products/' . ($key + 1) . '.jpg',
            ];

            for ($i = 1; $i <= 3; $i++) {
                if (File::exists(database_path('seeds/files/products/' . ($key + 1) . '-' . $i . '.jpg'))) {
                    $images[] = 'products/' . ($key + 1) . '-' . $i . '.jpg';
                }
            }

            $item['images'] = json_encode($images);

            $product = Product::create($item);

            $product->productCollections()->sync([$faker->numberBetween(1, 3)]);

            $product->categories()->sync([
                $faker->numberBetween(1, 37),
                $faker->numberBetween(1, 37),
                $faker->numberBetween(1, 37),
                $faker->numberBetween(15, 17),
            ]);

            $product->tags()->sync([
                $faker->numberBetween(1, 6),
                $faker->numberBetween(1, 6),
                $faker->numberBetween(1, 6),
            ]);

            Slug::create([
                'reference_type' => Product::class,
                'reference_id'   => $product->id,
                'key'            => Str::slug($product->name),
                'prefix'         => SlugHelper::getPrefix(Product::class),
            ]);
        }

        foreach ($products as $key => $item) {
            $product = Product::find($key + 1);
            $product->productAttributeSets()->sync([1, 2]);
            $product->productAttributes()->sync([$faker->numberBetween(1, 5), $faker->numberBetween(6, 10)]);

            $product->crossSales()->sync([
                $this->random(1, 20, [$product->id]),
                $this->random(1, 20, [$product->id]),
                $this->random(1, 20, [$product->id]),
                $this->random(1, 20, [$product->id]),
            ]);

            $images = [
                'products/' . ($key + 1) . '.jpg',
            ];

            for ($i = 1; $i <= 3; $i++) {
                if (File::exists(database_path('seeds/files/products/' . ($key + 1) . '-' . $i . '.jpg'))) {
                    $images[] = 'products/' . ($key + 1) . '-' . $i . '.jpg';
                }
            }

            for ($j = 0; $j < $faker->numberBetween(1, 5); $j++) {

                $variation = Product::create([
                    'name'                       => $product->name,
                    'status'                     => BaseStatusEnum::PUBLISHED,
                    'sku'                        => $product->sku . '-A' . $j,
                    'quantity'                   => $product->quantity,
                    'weight'                     => $product->weight,
                    'height'                     => $product->height,
                    'wide'                       => $product->wide,
                    'length'                     => $product->length,
                    'price'                      => $product->price,
                    'sale_price'                 => $product->sale_price ? ($product->price - $product->price * $faker->numberBetween(10,
                            30) / 100) : null,
                    'brand_id'                   => $product->brand_id,
                    'with_storehouse_management' => $product->with_storehouse_management,
                    'is_variation'               => true,
                    'images'                     => json_encode($images),
                ]);

                $productVariation = ProductVariation::create([
                    'product_id'              => $variation->id,
                    'configurable_product_id' => $product->id,
                    'is_default'              => $j == 0,
                ]);

                if ($productVariation->is_default) {
                    $product->update([
                        'sku'        => $variation->sku,
                        'sale_price' => $variation->sale_price,
                        'images'     => json_encode($variation->images),
                    ]);
                }

                ProductVariationItem::create([
                    'attribute_id' => $faker->numberBetween(1, 5),
                    'variation_id' => $productVariation->id,
                ]);

                ProductVariationItem::create([
                    'attribute_id' => $faker->numberBetween(6, 10),
                    'variation_id' => $productVariation->id,
                ]);
            }
        }
    }

    /**
     * @param int $from
     * @param int $to
     * @param array $exceptions
     * @return int
     */
    protected function random(int $from, int $to, array $exceptions = [])
    {
        sort($exceptions); // lets us use break; in the foreach reliably
        $number = rand($from, $to - count($exceptions)); // or mt_rand()
        foreach ($exceptions as $exception) {
            if ($number >= $exception) {
                $number++; // make up for the gap
            } else /*if ($number < $exception)*/ {
                break;
            }
        }

        return $number;
    }
}
