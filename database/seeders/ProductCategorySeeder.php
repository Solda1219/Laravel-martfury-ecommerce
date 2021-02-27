<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Slug\Models\Slug;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use MetaBox;
use SlugHelper;

class ProductCategorySeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('product-categories');

        $categories = [
            [
                'name' => 'Hot Promotions',
                'icon' => 'icon-star',
            ],
            [
                'name'        => 'Electronics',
                'icon'        => 'icon-laundry',
                'image'       => 'product-categories/1.jpg',
                'is_featured' => true,
                'children'    => [
                    [
                        'name'     => 'Consumer Electronic',
                        'children' => [
                            [
                                'name'      => 'Home Audio & Theaters',
                                'parent_id' => 15,
                            ],
                            [
                                'name'      => 'TV & Videos',
                                'parent_id' => 15,
                            ],
                            [
                                'name'      => 'Camera, Photos & Videos',
                                'parent_id' => 15,
                            ],
                            [
                                'name'      => 'Cellphones & Accessories',
                                'parent_id' => 15,
                            ],
                            [
                                'name'      => 'Headphones',
                                'parent_id' => 15,
                            ],
                            [
                                'name'      => 'Videos games',
                                'parent_id' => 15,
                            ],
                            [
                                'name'      => 'Wireless Speakers',
                                'parent_id' => 15,
                            ],
                            [
                                'name'      => 'Office Electronic',
                                'parent_id' => 15,
                            ],
                        ],
                    ],
                    [
                        'name'     => 'Accessories & Parts',
                        'children' => [
                            [
                                'name'      => 'Digital Cables',
                                'parent_id' => 16,
                            ],
                            [
                                'name'      => 'Audio & Video Cables',
                                'parent_id' => 16,
                            ],
                            [
                                'name'      => 'Batteries',
                                'parent_id' => 16,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name'        => 'Clothing',
                'icon'        => 'icon-shirt',
                'image'       => 'product-categories/2.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Computers',
                'icon'        => 'icon-desktop',
                'image'       => 'product-categories/3.jpg',
                'is_featured' => true,
                'children'    => [
                    [
                        'name'     => 'Computer & Technologies',
                        'children' => [
                            [
                                'name'      => 'Computer & Tablets',
                                'parent_id' => 17,
                            ],
                            [
                                'name'      => 'Laptop',
                                'parent_id' => 17,
                            ],
                            [
                                'name'      => 'Monitors',
                                'parent_id' => 17,
                            ],
                            [
                                'name'      => 'Computer Components',
                                'parent_id' => 17,
                            ],
                        ],
                    ],
                    [
                        'name'     => 'Networking',
                        'children' => [
                            [
                                'name'      => 'Drive & Storages',
                                'parent_id' => 18,
                            ],
                            [
                                'name'      => 'Gaming Laptop',
                                'parent_id' => 18,
                            ],
                            [
                                'name'      => 'Security & Protection',
                                'parent_id' => 18,
                            ],
                            [
                                'name'      => 'Accessories',
                                'parent_id' => 18,
                            ],
                        ],
                    ],
                ],
            ],
            [
                'name'        => 'Home & Kitchen',
                'icon'        => 'icon-lampshade',
                'image'       => 'product-categories/4.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Health & Beauty',
                'icon'        => 'icon-heart-pulse',
                'image'       => 'product-categories/5.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Jewelry & Watch',
                'icon'        => 'icon-diamond2',
                'image'       => 'product-categories/6.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Technology Toys',
                'icon'        => 'icon-desktop',
                'image'       => 'product-categories/7.jpg',
                'is_featured' => true,
            ],
            [
                'name'        => 'Phones',
                'icon'        => 'icon-smartphone',
                'image'       => 'product-categories/8.jpg',
                'is_featured' => true,
            ],
            [
                'name' => 'Babies & Moms',
                'icon' => 'icon-baby-bottle',
            ],
            [
                'name' => 'Sport & Outdoor',
                'icon' => 'icon-baseball',
            ],
            [
                'name' => 'Books & Office',
                'icon' => 'icon-book2',
            ],
            [
                'name' => 'Cars & Motorcycles',
                'icon' => 'icon-car-siren',
            ],
            [
                'name' => 'Home Improvements',
                'icon' => 'icon-wrench',
            ],
        ];

        ProductCategory::truncate();
        Slug::where('reference_type', ProductCategory::class)->delete();

        foreach ($categories as $key => $item) {
            $item['order'] = $key;
            $category = ProductCategory::create(Arr::except($item, ['icon', 'children']));

            MetaBox::saveMetaBoxData($category, 'icon', $item['icon']);

            Slug::create([
                'reference_type' => ProductCategory::class,
                'reference_id'   => $category->id,
                'key'            => Str::slug($category->name),
                'prefix'         => SlugHelper::getPrefix(ProductCategory::class),
            ]);
        }

        foreach ($categories as $key => $item) {
            foreach (Arr::get($item, 'children', []) as $child) {
                $child['parent_id'] = $key + 1;
                $child = ProductCategory::create(Arr::except($child, ['icon', 'children']));

                Slug::create([
                    'reference_type' => ProductCategory::class,
                    'reference_id'   => $child->id,
                    'key'            => Str::slug($child->name),
                    'prefix'         => SlugHelper::getPrefix(ProductCategory::class),
                ]);
            }
        }

        foreach ($categories as $item) {
            foreach (Arr::get($item, 'children', []) as $subKey => $sub) {
                foreach (Arr::get($sub, 'children', []) as $child) {
                    $child = ProductCategory::create(Arr::except($child, ['icon', 'children']));

                    Slug::create([
                        'reference_type' => ProductCategory::class,
                        'reference_id'   => $child->id,
                        'key'            => Str::slug($child->name),
                        'prefix'         => SlugHelper::getPrefix(ProductCategory::class),
                    ]);
                }
            }
        }
    }
}
