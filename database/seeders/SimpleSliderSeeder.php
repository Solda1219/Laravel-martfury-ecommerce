<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Setting\Models\Setting as SettingModel;
use Botble\SimpleSlider\Models\SimpleSlider;
use Botble\SimpleSlider\Models\SimpleSliderItem;

class SimpleSliderSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->uploadFiles('sliders');

        SimpleSlider::truncate();
        SimpleSliderItem::truncate();

        SimpleSlider::create([
            'name'        => 'Home slider',
            'key'         => 'home-slider',
            'description' => 'The main slider on homepage',
        ]);

        $items = [
            [
                'title' => 'Slider 1',
            ],
            [
                'title' => 'Slider 2',
            ],
            [
                'title' => 'Slider 3',
            ],
        ];

        foreach ($items as $index => $item) {
            $item['order'] = $index + 1;
            $item['simple_slider_id'] = 1;
            $item['image'] = 'sliders/' . ($index + 1) . '.jpg';
            $item['link'] = '/products';

            SimpleSliderItem::create($item);
        }

        SettingModel::insertOrIgnore([
            [
                'key'   => 'simple_slider_using_assets',
                'value' => 0,
            ],
        ]);
    }
}
