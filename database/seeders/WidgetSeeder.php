<?php

namespace Database\Seeders;

use Botble\Widget\Models\Widget as WidgetModel;
use Botble\Base\Supports\BaseSeeder;
use Theme;

class WidgetSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        WidgetModel::truncate();

        $widgets = [
            [
                'widget_id'  => 'CustomMenuWidget',
                'sidebar_id' => 'footer_sidebar',
                'position'   => 1,
                'data'       => [
                    'id'      => 'CustomMenuWidget',
                    'name'    => 'Quick links',
                    'menu_id' => 'quick-links',
                ],
            ],
            [
                'widget_id'  => 'CustomMenuWidget',
                'sidebar_id' => 'footer_sidebar',
                'position'   => 2,
                'data'       => [
                    'id'      => 'CustomMenuWidget',
                    'name'    => 'Company',
                    'menu_id' => 'company',
                ],
            ],
            [
                'widget_id'  => 'CustomMenuWidget',
                'sidebar_id' => 'footer_sidebar',
                'position'   => 3,
                'data'       => [
                    'id'      => 'CustomMenuWidget',
                    'name'    => 'Business',
                    'menu_id' => 'business',
                ],
            ],

            [
                'widget_id'  => 'BlogSearchWidget',
                'sidebar_id' => 'primary_sidebar',
                'position'   => 1,
                'data'       => [
                    'id'      => 'BlogSearchWidget',
                    'name'    => 'Search',
                ],
            ],
            [
                'widget_id'  => 'BlogCategoriesWidget',
                'sidebar_id' => 'primary_sidebar',
                'position'   => 2,
                'data'       => [
                    'id'      => 'BlogCategoriesWidget',
                    'name'    => 'Categories',
                ],
            ],
            [
                'widget_id'  => 'RecentPostsWidget',
                'sidebar_id' => 'primary_sidebar',
                'position'   => 3,
                'data'       => [
                    'id'      => 'RecentPostsWidget',
                    'name'    => 'Recent Posts',
                ],
            ],
            [
                'widget_id'  => 'TagsWidget',
                'sidebar_id' => 'primary_sidebar',
                'position'   => 4,
                'data'       => [
                    'id'      => 'TagsWidget',
                    'name'    => 'Popular Tags',
                ],
            ],
            [
                'widget_id'  => 'ProductCategoriesWidget',
                'sidebar_id' => 'bottom_footer_sidebar',
                'position'   => 1,
                'data'       => [
                    'id'         => 'ProductCategoriesWidget',
                    'name'       => 'Consumer Electric',
                    'categories' => [18, 2, 3, 4, 5, 6, 7],
                ],
            ],
            [
                'widget_id'  => 'ProductCategoriesWidget',
                'sidebar_id' => 'bottom_footer_sidebar',
                'position'   => 2,
                'data'       => [
                    'id'         => 'ProductCategoriesWidget',
                    'name'       => 'Clothing & Apparel',
                    'categories' => [8, 9, 10, 11, 12],
                ],
            ],
            [
                'widget_id'  => 'ProductCategoriesWidget',
                'sidebar_id' => 'bottom_footer_sidebar',
                'position'   => 3,
                'data'       => [
                    'id'         => 'ProductCategoriesWidget',
                    'name'       => 'Home, Garden & Kitchen',
                    'categories' => [13, 14, 15, 16, 17],
                ],
            ],
            [
                'widget_id'  => 'ProductCategoriesWidget',
                'sidebar_id' => 'bottom_footer_sidebar',
                'position'   => 4,
                'data'       => [
                    'id'         => 'ProductCategoriesWidget',
                    'name'       => 'Health & Beauty',
                    'categories' => [20, 21, 22, 23, 24],
                ],
            ],
            [
                'widget_id'  => 'ProductCategoriesWidget',
                'sidebar_id' => 'bottom_footer_sidebar',
                'position'   => 5,
                'data'       => [
                    'id'         => 'ProductCategoriesWidget',
                    'name'       => 'Computer & Technologies',
                    'categories' => [25, 26, 27, 28, 29, 19],
                ],
            ],
        ];

        foreach ($widgets as $item) {
            $item['theme'] = Theme::getThemeName();

            WidgetModel::create($item);
        }
    }
}
