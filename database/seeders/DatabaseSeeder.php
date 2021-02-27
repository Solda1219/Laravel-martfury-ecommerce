<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends BaseSeeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        Artisan::call('cms:plugin:activate:all');

        $this->call(BrandSeeder::class);
        $this->call(CurrencySeeder::class);
        $this->call(ProductCategorySeeder::class);
        $this->call(ProductSeeder::class);
        $this->call(ProductAttributeSeeder::class);
        $this->call(ProductCollectionSeeder::class);
        $this->call(CustomerSeeder::class);
        $this->call(ReviewSeeder::class);
        $this->call(TaxSeeder::class);
        $this->call(ProductTagSeeder::class);
        $this->call(FlashSaleSeeder::class);
        $this->call(ShippingSeeder::class);
        $this->call(ContactSeeder::class);
        $this->call(BlogSeeder::class);
        $this->call(SimpleSliderSeeder::class);
        $this->call(PageSeeder::class);
        $this->call(MenuSeeder::class);
        $this->call(ThemeOptionSeeder::class);
        $this->call(WidgetSeeder::class);
        $this->call(AdsSeeder::class);
        $this->call(FaqSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(SettingSeeder::class);
    }
}
