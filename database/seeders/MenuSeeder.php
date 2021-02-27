<?php

namespace Database\Seeders;

use Botble\Base\Supports\BaseSeeder;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Menu\Models\Menu as MenuModel;
use Botble\Menu\Models\MenuLocation;
use Botble\Menu\Models\MenuNode;
use Botble\Page\Models\Page;
use Illuminate\Support\Arr;
use Menu;

class MenuSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $menus = [
            [
                'name'     => 'Main menu',
                'slug'     => 'main-menu',
                'location' => 'main-menu',
                'items'    => [
                    [
                        'title' => 'Home',
                        'url'   => '/',
                    ],
                    [
                        'title'    => 'Pages',
                        'url'      => '#',
                        'children' => [
                            [
                                'title'          => 'About us',
                                'reference_id'   => 2,
                                'reference_type' => Page::class,
                            ],
                            [
                                'title'          => 'Terms Of Use',
                                'reference_id'   => 3,
                                'reference_type' => Page::class,
                            ],
                            [
                                'title'          => 'Terms & Conditions',
                                'reference_id'   => 4,
                                'reference_type' => Page::class,
                            ],
                            [
                                'title'          => 'Refund Policy',
                                'reference_id'   => 5,
                                'reference_type' => Page::class,
                            ],
                            [
                                'title'          => 'Coming soon',
                                'reference_id'   => 12,
                                'reference_type' => Page::class,
                            ],
                        ],
                    ],
                    [
                        'title'    => 'Shop',
                        'url'      => '/products',
                        'children' => [
                            [
                                'title' => 'All products',
                                'url'   => '/products',
                            ],
                            [
                                'title'          => 'Products Of Category',
                                'reference_id'   => 15,
                                'reference_type' => ProductCategory::class,
                            ],
                            [
                                'title' => 'Product Single',
                                'url'   => '/products/beat-headphone',
                            ],
                        ],
                    ],
                    [
                        'title'          => 'Blog',
                        'reference_id'   => 6,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title'          => 'FAQs',
                        'reference_id'   => 7,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title'          => 'Contact',
                        'reference_id'   => 8,
                        'reference_type' => Page::class,
                    ],
                ],
            ],
            [
                'name'  => 'Quick links',
                'slug'  => 'quick-links',
                'items' => [
                    [
                        'title'          => 'Terms Of Use',
                        'reference_id'   => 3,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title'          => 'Terms & Conditions',
                        'reference_id'   => 4,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title'          => 'Refund Policy',
                        'reference_id'   => 5,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title'          => 'FAQs',
                        'reference_id'   => 7,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title' => '404 Page',
                        'url'   => '/nothing',
                    ],
                ],
            ],
            [
                'name'  => 'Company',
                'slug'  => 'company',
                'items' => [
                    [
                        'title'          => 'About us',
                        'reference_id'   => 2,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title'          => 'Affiliate',
                        'reference_id'   => 10,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title'          => 'Career',
                        'reference_id'   => 11,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title'          => 'Contact us',
                        'reference_id'   => 8,
                        'reference_type' => Page::class,
                    ],
                ],
            ],
            [
                'name'  => 'Business',
                'slug'  => 'business',
                'items' => [
                    [
                        'title'          => 'Our blog',
                        'reference_id'   => 6,
                        'reference_type' => Page::class,
                    ],
                    [
                        'title' => 'Cart',
                        'url'   => '/cart',
                    ],
                    [
                        'title' => 'My account',
                        'url'   => '/customer/overview',
                    ],
                    [
                        'title' => 'Shop',
                        'url'   => '/products',
                    ],
                ],
            ],
        ];

        MenuModel::truncate();
        MenuLocation::truncate();
        MenuNode::truncate();

        foreach ($menus as $index => $item) {
            $menu = MenuModel::create(Arr::except($item, ['items', 'location']));

            if (isset($item['location'])) {
                MenuLocation::create([
                    'menu_id'  => $menu->id,
                    'location' => $item['location'],
                ]);
            }

            foreach ($item['items'] as $menuNode) {
                $this->createMenuNode($index, $menuNode);
            }
        }

        Menu::clearCacheMenuItems();
    }

    /**
     * @param int $index
     * @param array $menuNode
     * @param string $locale
     * @param int $parentId
     */
    protected function createMenuNode(int $index, array $menuNode, int $parentId = 0): void
    {
        $menuNode['menu_id'] = $index + 1;
        $menuNode['parent_id'] = $parentId;

        if (Arr::has($menuNode, 'children')) {
            $children = $menuNode['children'];
            $menuNode['has_child'] = true;

            unset($menuNode['children']);
        } else {
            $children = [];
            $menuNode['has_child'] = false;
        }

        $createdNode = MenuNode::create($menuNode);

        if ($children) {
            foreach ($children as $child) {
                $this->createMenuNode($index, $child, $createdNode->id);
            }
        }
    }
}
