<?php

namespace Botble\Ecommerce\Services\Products;

use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class GetProductService
{
    /**
     * @var ProductInterface
     */
    protected $productRepository;

    /**
     * GetProductService constructor.
     * @param ProductInterface $productRepository
     */
    public function __construct(ProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @param Request $request
     * @param null $category
     * @param null $brand
     * @param array $with
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function getProduct(Request $request, $category = null, $brand = null, array $with = [])
    {
        $queryVar = [
            'keyword'     => $request->input('q'),
            'brands'      => (array)$request->input('brands', []),
            'categories'  => (array)$request->input('categories', []),
            'tags'        => (array)$request->input('tags', []),
            'collections' => (array)$request->input('collections', []),
            'attributes'  => (array)$request->input('attributes', []),
            'max_price'   => $request->input('max_price'),
            'min_price'   => $request->input('min_price'),
            'sort_by'     => $request->input('sort-by'),
            'num'         => $request->input('num') ? (int)$request->input('num') : (int)theme_option('number_of_products_per_page',
                12),
        ];

        if ($category) {
            $queryVar['categories'] = array_merge($queryVar['categories'], [$category]);
        }

        if ($brand) {
            $queryVar['brands'] = array_merge(($queryVar['brands']), [$brand]);
        }

        $countAttributeGroups = 1;
        if (count($queryVar['attributes'])) {
            $countAttributeGroups = DB::table('ec_product_attributes')
                ->whereIn('id', $queryVar['attributes'])
                ->distinct('attribute_set_id')
                ->count('attribute_set_id');
        }

        switch ($queryVar['sort_by']) {
            case 'date_asc':
                $orderBy = [
                    'ec_products.created_at' => 'asc',
                ];
                break;
            case 'date_desc':
                $orderBy = [
                    'ec_products.created_at' => 'desc',
                ];
                break;
            case 'price_asc':
                $orderBy = [
                    'products_with_final_price.final_price' => 'asc',
                ];
                break;
            case 'price_desc':
                $orderBy = [
                    'products_with_final_price.final_price' => 'desc',
                ];
                break;
            case 'name_asc':
                $orderBy = [
                    'ec_products.name' => 'asc',
                ];
                break;
            case 'name_desc':
                $orderBy = [
                    'ec_products.name' => 'desc',
                ];
                break;
            default:
                $orderBy = [
                    'ec_products.order'      => 'ASC',
                    'ec_products.created_at' => 'DESC',
                ];
                break;
        }

        $products = $this->productRepository->filterProducts(
            [
                'keyword'                => $queryVar['keyword'],
                'min_price'              => $queryVar['min_price'],
                'max_price'              => $queryVar['max_price'],
                'categories'             => $queryVar['categories'],
                'tags'                   => $queryVar['tags'],
                'collections'            => $queryVar['collections'],
                'brands'                 => $queryVar['brands'],
                'attributes'             => $queryVar['attributes'],
                'count_attribute_groups' => $countAttributeGroups,
                'order_by'               => $orderBy,
            ],
            [
                'paginate' => [
                    'per_page'      => $queryVar['num'],
                    'current_paged' => (int)$request->query('page', 1),
                ],
                'with'     => $with,
            ]
        );

        return $products;
    }
}
