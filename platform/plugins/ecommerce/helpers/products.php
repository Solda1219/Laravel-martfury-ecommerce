<?php

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCollection;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCollectionInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

if (!function_exists('get_product_by_id')) {
    /**
     * @param int $productId
     * @return mixed
     */
    function get_product_by_id($productId)
    {
        return app(ProductInterface::class)->findById($productId);
    }
}

if (!function_exists('get_products')) {
    /**
     * @param array $params
     * @return mixed
     */
    function get_products(array $params = [])
    {
        $params = array_merge([
            'condition' => [
                'ec_products.status'       => BaseStatusEnum::PUBLISHED,
                'ec_products.is_variation' => 0,
            ],
            'order_by'  => [
                'ec_products.order'      => 'ASC',
                'ec_products.created_at' => 'DESC',
            ],
            'take'      => null,
            'paginate'  => [
                'per_page'      => null,
                'current_paged' => 1,
            ],
            'select'    => [
                'ec_products.*',
            ],
            'with' => ['slugable'],
        ], $params);

        return app(ProductInterface::class)->getProducts($params);
    }
}

if (!function_exists('get_products_by_categories')) {
    /**
     * @param array $params
     * @return mixed
     */
    function get_products_by_categories(array $params = [])
    {
        $params = array_merge([
            'categories' => [
                'by'       => 'id',
                'value_in' => [],
            ],
            'condition'  => [
                'ec_products.status'       => BaseStatusEnum::PUBLISHED,
                'ec_products.is_variation' => 0,
            ],
            'order_by'   => [
                'ec_products.order'      => 'ASC',
                'ec_products.created_at' => 'DESC',
            ],
            'take'       => null,
            'paginate'   => [
                'per_page'      => null,
                'current_paged' => 1,
            ],
            'select'     => [
                'ec_products.*',
                'base_category.id as category_id',
                'base_category.name as category_name',
            ],
            'with'       => [],
        ], $params);

        return app(ProductInterface::class)->getProductsWithCategory($params);
    }
}

if (!function_exists('get_products_on_sale')) {
    /**
     * @param array $params
     * @return mixed
     */
    function get_products_on_sale(array $params = [])
    {
        $params = array_merge([
            'condition' => [
                'ec_products.status'       => BaseStatusEnum::PUBLISHED,
                'ec_products.is_variation' => 0,
            ],
            'order_by'  => [
                'ec_products.order'      => 'ASC',
                'ec_products.created_at' => 'DESC',
            ],
            'take'      => null,
            'paginate'  => [
                'per_page'      => null,
                'current_paged' => 1,
            ],
            'select'    => [
                'ec_products.*',
            ],
            'with'      => [],
        ], $params);

        return app(ProductInterface::class)->getOnSaleProducts($params);
    }
}

if (!function_exists('get_featured_products')) {
    /**
     * @param array $params
     * @return mixed
     */
    function get_featured_products(array $params = [])
    {
        $params = array_merge([
            'condition' => [
                'ec_products.is_featured'  => 1,
                'ec_products.is_variation' => 0,
                'ec_products.status'       => BaseStatusEnum::PUBLISHED,
            ],
            'take'      => null,
            'order_by'  => [
                'ec_products.created_at' => 'DESC',
            ],
            'select'    => ['ec_products.*'],
            'with'      => [],
        ], $params);

        return app(ProductInterface::class)->advancedGet($params);
    }
}

if (!function_exists('get_top_rated_products')) {
    /**
     * @param int $limit
     * @param array $with
     * @return mixed
     */
    function get_top_rated_products($limit = 10, array $with = [])
    {
        $topProductIds = get_top_rated_product_ids($limit);

        $products = get_products([
            'condition' => [
                'ec_products.status'       => BaseStatusEnum::PUBLISHED,
                'ec_products.is_variation' => 0,
                ['ec_products.id', 'IN', $topProductIds],
            ],
            'order_by'  => [
                'ec_products.order'      => 'ASC',
                'ec_products.created_at' => 'DESC',
            ],
            'take'      => null,
            'paginate'  => [
                'per_page'      => null,
                'current_paged' => 1,
            ],
            'select'    => [
                'ec_products.*',
            ],
            'with'      => $with,
        ]);

        $products = $products->sortBy(function($product) use($topProductIds) {
            return array_search($product->id, $topProductIds);
        });

        return $products;
    }
}

if (!function_exists('get_top_rated_product_ids')) {
    /**
     * @param int $limit
     * @return mixed
     */
    function get_top_rated_product_ids($limit = 10)
    {
        return app(ReviewInterface::class)->getModel()
            ->where([
                'ec_reviews.status' => BaseStatusEnum::PUBLISHED,
            ])
            ->selectRaw('ec_reviews.product_id, avg(ec_reviews.star) AS star')
            ->groupBy('ec_reviews.product_id')
            ->orderBy('star', 'DESC')
            ->limit($limit)
            ->pluck('ec_reviews.product_id')
            ->all();
    }
}

if (!function_exists('get_trending_products')) {
    /**
     * @param array $params
     * @return mixed
     */
    function get_trending_products(array $params = [])
    {
        $params = array_merge([
            'condition' => [
                'ec_products.status'       => BaseStatusEnum::PUBLISHED,
                'ec_products.is_variation' => 0,
            ],
            'take'      => 10,
            'order_by'  => [
                'ec_products.views' => 'DESC',
            ],
            'select'    => ['ec_products.*'],
            'with'      => [],
        ], $params);

        return app(ProductInterface::class)->advancedGet($params);
    }
}

if (!function_exists('get_featured_product_categories')) {
    /**
     * Get featured product categories
     * @param array $args ['limit' => int, 'order' => array, 'select' => array()]
     * @return mixed
     */
    function get_featured_product_categories($args = [])
    {
        $params = array_merge([
            'limit'  => 0,
            'order'  => [
                'ec_product_categories.order' => 'DESC',
            ],
            'select' => ['*'],
            'with'   => ['slugable'],
        ], $args);

        return app(ProductCategoryInterface::class)->advancedGet([
            'condition' => [
                'ec_product_categories.is_featured' => 1,
                'ec_product_categories.status'      => BaseStatusEnum::PUBLISHED,
            ],
            'take'      => $params['limit'],
            'order_by'  => $params['order'],
            'select'    => $params['select'],
        ]);
    }
}

if (!function_exists('get_product_collections')) {
    /**
     * @return Collection
     */
    function get_product_collections(array $condition, array $with = [], array $select = ['*'])
    {
        return app(ProductCollectionInterface::class)->allBy($condition, $with, $select);
    }
}

if (!function_exists('get_products_by_collections')) {
    /**
     * @param array $param
     * @return Collection
     */
    function get_products_by_collections($param = [])
    {
        return app(ProductInterface::class)->getProductsByCollections($param);
    }
}

if (!function_exists('get_default_product_variation')) {
    /**
     * @param int $configurableId
     * @return Product
     */
    function get_default_product_variation($configurableId)
    {
        return app(ProductInterface::class)
            ->getProductVariations($configurableId, [
                'condition' => [
                    'ec_products.status'       => BaseStatusEnum::PUBLISHED,
                    'ec_products.is_variation' => 1,
                ],
                'take'      => 1,
                'order_by'  => [
                    'ec_product_variations.is_default' => 'DESC',
                ],
            ]);
    }
}

if (!function_exists('get_product_by_brand')) {
    /**
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    function get_product_by_brand(array $params)
    {
        return app(ProductInterface::class)->getProductByBrands($params);
    }
}

if (!function_exists('the_product_price')) {
    /**
     * @param Product $product
     * @param array $htmlWrap
     * @return string
     */
    function the_product_price($product, $htmlWrap = [])
    {
        $htmlWrapParams = array_merge([
            'open_wrap_price'  => '<del>',
            'close_wrap_price' => '</del>',
            'open_wrap_sale'   => '<ins>',
            'close_wrap_sale'  => '</ins>',
        ], $htmlWrap);
        if ($product->front_sale_price !== $product->price) {
            return $htmlWrapParams['open_wrap_price'] . format_price($product->price) . $htmlWrapParams['close_wrap_price'] .
                $htmlWrapParams['open_wrap_sale'] . format_price($product->front_sale_price) . $htmlWrapParams['close_wrap_sale'];
        }
        return $htmlWrapParams['open_wrap_sale'] . $product->price . $htmlWrapParams['close_wrap_sale'];
    }
}

if (!function_exists('get_related_products')) {
    /**
     * Get related products of $product
     * @param Product $product
     * @param int $limit
     * @return array
     */
    function get_related_products($product, $limit = 4)
    {
        $params = [
            'condition' => [
                'ec_products.status'       => BaseStatusEnum::PUBLISHED,
                'ec_products.is_variation' => 0,
            ],
            'order_by'  => [
                'ec_products.order'      => 'ASC',
                'ec_products.created_at' => 'DESC',
            ],
            'take'      => $limit,
            'select'    => [
                'ec_products.*',
            ],
            'with'      => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ];

        $relatedIds = app(ProductInterface::class)->getRelatedProductIds($product);

        if (!empty($relatedIds)) {
            $params['condition'][] = ['ec_products.id', 'IN', $relatedIds];
        } else {
            $params['condition'][] = ['ec_products.id', '!=', $product->id];
        }

        return app(ProductInterface::class)->getProducts($params);
    }
}

if (!function_exists('get_cross_sale_products')) {
    /**
     * @param Product $product
     * @return array
     */
    function get_cross_sale_products($product)
    {
        return app(ProductInterface::class)->getCrossSaleProductIds($product);
    }
}

if (!function_exists('get_up_sale_products')) {
    /**
     * @param Product $product
     * @return array
     */
    function get_up_sale_products($product)
    {
        return app(ProductInterface::class)->getUpSaleProductIds($product);
    }
}

if (!function_exists('get_product_attributes_with_set')) {
    /**
     * Get list attributes by set id of product
     * @param Product $product
     * @param int $setId
     * @return array
     */
    function get_product_attributes_with_set($product, int $setId)
    {
        $productAttributes = app(ProductInterface::class)->getRelatedProductAttributes($product);
        $attributes = [];

        foreach ($productAttributes as $attribute) {
            if ($attribute->attribute_set_id === $setId) {
                array_push($attributes, $attribute);
            }
        }

        return $attributes;
    }
}
