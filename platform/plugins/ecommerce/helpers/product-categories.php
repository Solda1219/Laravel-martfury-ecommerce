<?php

use Botble\Base\Supports\SortItemsWithChildrenHelper;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;

if (!function_exists('get_product_categories')) {
    /**
     * @param array $conditions
     * @param array $with
     * @param array $withCount
     * @param bool $parentOnly
     * @return array
     */
    function get_product_categories(array $conditions = [], array $with = [], array $withCount = [], bool $parentOnly = false)
    {
        $repo = app(ProductCategoryInterface::class);
        $categories = $repo->getModel();

        if (!empty($conditions)) {
            $categories = $categories->where($conditions);
        }

        if (!empty($with)) {
            $categories = $categories->with($with);
        }

        if (!empty($withCount)) {
            $categories = $categories->withCount($withCount);
        }

        if ($parentOnly) {
            $categories = $categories->where(function ($query) {
                $query->whereNull('parent_id')
                    ->orWhere('parent_id', 0)
                    ->orWhere('parent_id', '');
            });
        }

        $categories = $categories
            ->orderBy('order', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->get();

        $categories = sort_item_with_children($categories);

        return $categories;
    }
}

if (!function_exists('get_product_categories_with_children')) {
    /**
     * @param array $options
     * @return array
     * @throws Exception
     */
    function get_product_categories_with_children(array $options = [])
    {
        $options = array_merge([
            'status' => null,
        ], $options);

        $categories = app(ProductCategoryInterface::class)->getModel();

        if ($options['status'] !== null) {
            $categories = $categories->where('status', $options['status']);
        }

        $categories = $categories
            ->orderBy('order', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->get();

        /**
         * @var SortItemsWithChildrenHelper $sortHelper
         */
        $sortHelper = app(SortItemsWithChildrenHelper::class);
        $sortHelper->setChildrenProperty('child_cats')->setItems($categories);

        return $sortHelper->sort();
    }
}
