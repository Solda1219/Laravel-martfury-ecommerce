<?php

namespace Theme\Martfury\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Blog\Repositories\Interfaces\PostInterface;
use Botble\Ecommerce\Repositories\Interfaces\FlashSaleInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\Ecommerce\Services\Products\GetProductService;
use Botble\Theme\Http\Controllers\PublicController;
use Cart;
use EmailHandler;
use Illuminate\Http\Request;
use Theme;
use Theme\Martfury\Http\Requests\SendDownloadAppLinksRequest;
use Theme\Martfury\Http\Resources\BrandResource;
use Theme\Martfury\Http\Resources\PostResource;
use Theme\Martfury\Http\Resources\ProductCategoryResource;
use Theme\Martfury\Http\Resources\ReviewResource;

class MartfuryController extends PublicController
{
    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $products = get_products_by_collections([
            'collections' => [
                'by'       => 'id',
                'value_in' => [$request->input('collection_id')],
            ],
            'take'        => 10,
            'with'        => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getFeaturedProductCategories(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $categories = get_featured_product_categories();

        return $response->setData(ProductCategoryResource::collection($categories));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetTrendingProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $products = get_trending_products([
            'take' => 10,
            'with' => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetFeaturedBrands(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $brands = get_featured_brands();

        return $response->setData(BrandResource::collection($brands));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetFeaturedProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $data = [];

        $products = get_featured_products([
            'take' => 10,
            'with' => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ]);

        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetTopRatedProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $products = get_top_rated_products(10, [
            'slugable',
            'variations',
            'productCollections',
            'variationAttributeSwatchesForProductList',
            'promotions',
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetOnSaleProducts(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $products = get_products_on_sale([
            'take' => 10,
            'with' => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxCart(Request $request, BaseHttpResponse $response)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        return $response->setData([
            'count' => Cart::instance('cart')->count(),
            'html'  => Theme::partial('cart'),
        ]);
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return mixed
     */
    public function getQuickView(Request $request, $id, BaseHttpResponse $response)
    {
        if (!$request->ajax()) {
            abort(404);
        }

        $product = get_products([
            'condition' => [
                'ec_products.id'     => $id,
                'ec_products.status' => BaseStatusEnum::PUBLISHED,
            ],
            'take'      => 1,
            'with'      => [
                'defaultProductAttributes',
                'slugable',
                'tags',
                'tags.slugable',
            ],
        ]);

        if (!$product) {
            abort(404);
        }

        return $response->setData(Theme::partial('quick-view', compact('product')));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param PostInterface $postRepository
     * @return BaseHttpResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Http\Resources\Json\JsonResource
     */
    public function ajaxGetFeaturedPosts(Request $request, BaseHttpResponse $response, PostInterface $postRepository)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $posts = $postRepository->getFeatured(3);

        return $response
            ->setData(PostResource::collection($posts))
            ->toApiResponse();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ProductInterface $productRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetRelatedProducts(
        $id,
        Request $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository
    ) {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $product = $productRepository->findOrFail($id);

        $products = get_related_products($product, $request->input('limit'));

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ReviewInterface $reviewRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetProductReviews(
        $id,
        Request $request,
        BaseHttpResponse $response,
        ReviewInterface $reviewRepository
    ) {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $reviews = $reviewRepository->advancedGet([
            'condition' => [
                'status'     => BaseStatusEnum::PUBLISHED,
                'product_id' => $id,
            ],
            'order_by'  => ['created_at' => 'desc'],
            'paginate'  => [
                'per_page'      => (int)$request->input('per_page', 10),
                'current_paged' => (int)$request->input('page', 1),
            ],
        ]);

        return $response->setData(ReviewResource::collection($reviews))->toApiResponse();
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxSearchProducts(Request $request, BaseHttpResponse $response, GetProductService $productService)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $request->merge(['num' => 10]);

        $products = $productService->getProduct($request, null, null,
            ['slugable', 'variations', 'productCollections', 'variationAttributeSwatchesForProductList', 'promotions']);

        $query = $request->get('q');

        return $response->setData(Theme::partial('ajax-search-results', compact('products', 'query')));
    }

    /**
     * @param SendDownloadAppLinksRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Throwable
     */
    public function ajaxSendDownloadAppLinks(SendDownloadAppLinksRequest $request, BaseHttpResponse $response)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        EmailHandler::setModule(Theme::getThemeName())
            ->sendUsingTemplate('download_app', $request->input('email'), [], false, 'themes', __('Download apps'));

        return $response->setMessage(__('We sent an email with download links to your email, please check it!'));
    }

    /**
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function ajaxGetProductsByCategoryId(
        Request $request,
        BaseHttpResponse $response,
        ProductInterface $productRepository
    ) {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $categoryId = $request->input('category_id');

        if (!$categoryId) {
            return $response;
        }

        $products = $productRepository->getProductsByCategories([
            'categories' => [
                'by'       => 'id',
                'value_in' => [$categoryId],
            ],
        ]);

        $data = [];
        foreach ($products as $product) {
            $data[] = Theme::partial('product-item', compact('product'));
        }

        return $response->setData($data);
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @param ProductCategoryInterface $productCategoryRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetProductCategories(
        Request $request,
        BaseHttpResponse $response,
        ProductCategoryInterface $productCategoryRepository
    ) {

        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $categoryIds = $request->input('categories', []);
        if (empty($categoryIds)) {
            return $response;
        }

        $categories = $productCategoryRepository->advancedGet([
            'condition' => [
                'status' => BaseStatusEnum::PUBLISHED,
                ['id', 'IN', $categoryIds],
            ],
            'with'      => ['slugable'],
        ]);

        return $response->setData(ProductCategoryResource::collection($categories));
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @param FlashSaleInterface $flashSaleRepository
     * @return BaseHttpResponse
     */
    public function ajaxGetFlashSale(Request $request, $id, BaseHttpResponse $response, FlashSaleInterface $flashSaleRepository)
    {
        if (!$request->ajax() || !$request->wantsJson()) {
            abort(404);
        }

        $flashSale = $flashSaleRepository->getModel()
            ->notExpired()
            ->where('id', $id)
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->with([
                'products' => function ($query) {
                    return $query->where('status', BaseStatusEnum::PUBLISHED);
                },
            ])
            ->first();

        if (!$flashSale) {
            return $response->setData([]);
        }

        $data = [];
        foreach ($flashSale->products as $product) {
            $data[] = Theme::partial('flash-sale-product', compact('product', 'flashSale'));
        }

        return $response->setData($data);
    }
}
