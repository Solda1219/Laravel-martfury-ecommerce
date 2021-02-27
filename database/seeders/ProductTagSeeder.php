<?php

namespace Database\Seeders;

use Botble\Ecommerce\Models\ProductTag;
use Botble\Slug\Models\Slug;
use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Str;
use SlugHelper;

class ProductTagSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tags = [
            [
                'name' => 'Electronic',
            ],
            [
                'name' => 'Mobile',
            ],
            [
                'name' => 'Iphone',
            ],
            [
                'name' => 'Printer',
            ],
            [
                'name' => 'Office',
            ],
            [
                'name' => 'IT',
            ],
        ];

        ProductTag::truncate();
        Slug::where('reference_type', ProductTag::class)->delete();

        foreach ($tags as $key => $item) {
            $tag = ProductTag::create($item);

            Slug::create([
                'reference_type' => ProductTag::class,
                'reference_id'   => $tag->id,
                'key'            => Str::slug($tag->name),
                'prefix'         => SlugHelper::getPrefix(ProductTag::class),
            ]);
        }
    }
}
