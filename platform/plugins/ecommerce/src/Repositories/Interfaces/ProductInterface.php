<?php

namespace Botble\Ecommerce\Repositories\Interfaces;

use Botble\Ecommerce\Models\Product;
use Botble\Support\Repositories\Interfaces\RepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

interface ProductInterface extends RepositoryInterface
{
    /**
     * @param string $query
     * @param int $paginate
     * @return mixed
     */
    public function getSearch($query, $paginate = 10);

    /**
     * @param Product $model
     * @param null $categories
     * @return null|array
     */
    public function syncCategories($model, $categories = null);

    /**
     * @param Product $model
     * @param null $productCollections
     * @return null|array
     */
    public function syncProductCollections($model, $productCollections = null);

    /**
     * @param Product $model
     * @return Collection
     */
    public function getRelatedCategories($model);

    /**
     * @param Product $model
     * @return array
     */
    public function getRelatedCategoryIds($model);

    /**
     * @param Product $model
     * @return Collection
     */
    public function getRelatedProductCollections($model);

    /**
     * @param Product $model
     * @return array
     */
    public function getRelatedProductCollectionIds($model);

    /**
     * @param Product $model
     * @param null $products
     * @return array|null
     */
    public function syncProducts($model, $products = null);

    /**
     * @param Product $model
     * @return Collection
     */
    public function getRelatedProducts($model);

    /**
     * @param Product $model
     * @return array
     */
    public function getRelatedProductIds($model);

    /**
     * @param Product $model
     * @param null $products
     * @return array|null
     */
    public function syncCrossSaleProducts($model, $products = null);

    /**
     * @param Product $model
     * @return Collection
     */
    public function getCrossSaleProducts($model);

    /**
     * @param Product $model
     * @return array
     */
    public function getCrossSaleProductIds($model);

    /**
     * @param Product $model
     * @param null $products
     * @return array|null
     */
    public function syncUpSaleProducts($model, $products = null);

    /**
     * @param Product $model
     * @return Collection
     */
    public function getUpSaleProducts($model);

    /**
     * @param Product $model
     * @return array
     */
    public function getUpSaleProductIds($model);

    /**
     * @param Product $product
     * @return Collection
     */
    public function getRelatedProductAttributeSets($product);

    /**
     * @param Product $product
     * @return array
     */
    public function getRelatedProductAttributeSetIds($product);

    /**
     * @param Product $product
     * @return Collection
     */
    public function getRelatedProductAttributes($product);

    /**
     * @param Product $product
     * @return array
     */
    public function getRelatedProductAttributeIds($product);

    /**
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Model|Collection|null
     */
    public function getProducts(array $params);

    /**
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function getProductsWithCategory(array $params);

    /**
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function getOnSaleProducts(array $params);

    /**
     * @param int $configurableProductId
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function getProductVariations($configurableProductId, array $params = []);

    /**
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function getProductsByCollections(array $params);

    /**
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function getProductByBrands(array $params);

    /**
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function getProductByTags(array $params);

    /**
     * @param array $params
     * @return mixed
     */
    public function getProductsByCategories(array $params);

    /**
     * @param array $filters
     * @param array $params
     * @return LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection|Collection|mixed
     */
    public function filterProducts(array $filters, array $params = []);
}
