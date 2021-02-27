<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Base\Supports\Helper;
use Botble\Ecommerce\Http\Requests\ReviewRequest;
use Botble\Ecommerce\Models\Brand;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Models\ProductCategory;
use Botble\Ecommerce\Models\ProductTag;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\OrderInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductTagInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ReviewInterface;
use Botble\Ecommerce\Services\Products\GetProductService;
use Botble\SeoHelper\SeoOpenGraph;
use Botble\Slug\Repositories\Interfaces\SlugInterface;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Response;
use RvMedia;
use SeoHelper;
use SlugHelper;
use Theme;

class PublicProductController
{
    /**
     * @var ProductInterface
     */
    protected $productRepository;

    /**
     * @var ProductCategoryInterface
     */
    protected $productCategoryRepository;

    /**
     * @var ProductAttributeSetInterface
     */
    protected $productAttributeSetRepository;

    /**
     * @var BrandInterface
     */
    protected $brandRepository;

    /**
     * @var ProductVariationInterface
     */
    protected $productVariationRepository;

    /**
     * @var ReviewInterface
     */
    protected $reviewRepository;

    /**
     * @var SlugInterface
     */
    protected $slugRepository;

    /**
     * PublicProductController constructor.
     * @param ProductInterface $productRepository
     * @param ProductCategoryInterface $productCategoryRepository
     * @param ProductAttributeSetInterface $productAttributeSet
     * @param BrandInterface $brandRepository
     * @param ProductVariationInterface $productVariationRepository
     * @param ReviewInterface $reviewRepository
     * @param SlugInterface $slugRepository
     */
    public function __construct(
        ProductInterface $productRepository,
        ProductCategoryInterface $productCategoryRepository,
        ProductAttributeSetInterface $productAttributeSet,
        BrandInterface $brandRepository,
        ProductVariationInterface $productVariationRepository,
        ReviewInterface $reviewRepository,
        SlugInterface $slugRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productAttributeSetRepository = $productAttributeSet;
        $this->brandRepository = $brandRepository;
        $this->productVariationRepository = $productVariationRepository;
        $this->reviewRepository = $reviewRepository;
        $this->slugRepository = $slugRepository;
    }

    /**
     * @param Request $request
     * @param GetProductService $productService
     * @return Response
     */
    public function getProducts(Request $request, GetProductService $productService)
    {
        $query = $request->get('q');

        if ($query) {
            $products = $productService->getProduct($request);

            SeoHelper::setTitle(__('Search result "' . $query . '" '))
                ->setDescription(__('Products: ') . '"' . $request->get('q') . '"');
            Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Search'), route('public.products'));

            return Theme::scope('ecommerce.search', compact('products', 'query'),
                'plugins/ecommerce::themes.search')->render();
        }

        $products = $productService->getProduct($request, null, null,
            ['slugable', 'variations', 'productCollections', 'variationAttributeSwatchesForProductList', 'promotions']);

        Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Products'), route('public.products'));
        SeoHelper::setTitle(__('Products'))->setDescription(__('Products'));

        do_action(PRODUCT_MODULE_SCREEN_NAME);

        return Theme::scope('ecommerce.products', compact('products'),
            'plugins/ecommerce::themes.products')->render();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|Response
     */
    public function getProduct($slug)
    {
        $slug = $this->slugRepository->getFirstBy([
            'key'            => $slug,
            'reference_type' => Product::class,
            'prefix'         => SlugHelper::getPrefix(Product::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $condition = [
            'ec_products.id'     => $slug->reference_id,
            'ec_products.status' => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::check() && request()->input('preview')) {
            Arr::forget($condition, 'status');
        }

        $product = get_products([
            'condition' => $condition,
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

        if ($product->slugable->key !== $slug->key) {
            return redirect()->to($product->url);
        }

        SeoHelper::setTitle($product->name)->setDescription($product->description);

        $meta = new SeoOpenGraph;
        if ($product->image) {
            $meta->setImage(RvMedia::getImageUrl($product->image));
        }
        $meta->setDescription($product->description);
        $meta->setUrl($product->url);
        $meta->setTitle($product->name);

        SeoHelper::setSeoOpenGraph($meta);

        Helper::handleViewCount($product, 'viewed_product');

        Theme::breadcrumb()->add(__('Home'), url('/'))
            ->add(__('Products'), route('public.products'));

        $category = $product->categories->first();
        if ($category) {
            Theme::breadcrumb()->add($category->name, $category->url);
        }

        Theme::breadcrumb()->add($product->name, $product->url);

        admin_bar()
            ->registerLink(trans('plugins/ecommerce::products.edit_this_product'),
                route('products.edit', $product->id));

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PRODUCT_CATEGORY_MODULE_SCREEN_NAME, $product);

        return Theme::scope('ecommerce.product', compact('product'), 'plugins/ecommerce::themes.product')->render();
    }

    /**
     * @param string $slug
     * @param Request $request
     * @param ProductTagInterface $tagRepository
     * @return \Illuminate\Http\RedirectResponse|Response
     */
    public function getProductTag($slug, Request $request, ProductTagInterface $tagRepository)
    {
        $slug = $this->slugRepository->getFirstBy([
            'key'            => $slug,
            'reference_type' => ProductTag::class,
            'prefix'         => SlugHelper::getPrefix(ProductTag::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $condition = [
            'ec_product_categories.id'     => $slug->reference_id,
            'ec_product_categories.status' => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::check() && request()->input('preview')) {
            Arr::forget($condition, 'status');
        }

        $tag = $tagRepository->getFirstBy(['id' => $slug->reference_id], ['*'], ['slugable', 'products']);

        if (!$tag) {
            abort(404);
        }

        if ($tag->slugable->key !== $slug->key) {
            return redirect()->to($tag->url);
        }

        $products = $this->productRepository->getProductByTags([
            'product_tag' => [
                'by'       => 'id',
                'value_in' => [$tag->id],
            ],
            'paginate'    => [
                'per_page'      => (int)theme_option('number_of_products_per_page', 12),
                'current_paged' => (int)$request->input('page', 1),
            ],
            'with'        => [
                'slugable',
                'variations',
                'productCollections',
                'variationAttributeSwatchesForProductList',
                'promotions',
            ],
        ]);

        SeoHelper::setTitle($tag->name)->setDescription($tag->description);

        $meta = new SeoOpenGraph;
        $meta->setDescription($tag->description);
        $meta->setUrl($tag->url);
        $meta->setTitle($tag->name);

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()->add(__('Home'), url('/'))
            ->add(__('Products'), route('public.products'))
            ->add($tag->name, $tag->url);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PRODUCT_TAG_MODULE_SCREEN_NAME, $tag);

        return Theme::scope('ecommerce.product-tag', compact('tag', 'products'),
            'plugins/ecommerce::themes.product-tag')->render();
    }

    /**
     * @param string $slug
     * @param Request $request
     * @param ProductCategoryInterface $categoryRepository
     * @param GetProductService $getProductService
     * @return \Illuminate\Http\RedirectResponse|Response
     */
    public function getProductCategory(
        $slug,
        Request $request,
        ProductCategoryInterface $categoryRepository,
        GetProductService $getProductService
    ) {
        $slug = $this->slugRepository->getFirstBy([
            'key'            => $slug,
            'reference_type' => ProductCategory::class,
            'prefix'         => SlugHelper::getPrefix(ProductCategory::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $condition = [
            'ec_product_categories.id'     => $slug->reference_id,
            'ec_product_categories.status' => BaseStatusEnum::PUBLISHED,
        ];

        if (Auth::check() && request()->input('preview')) {
            Arr::forget($condition, 'status');
        }

        $category = $categoryRepository->getFirstBy($condition, ['*'], ['slugable']);

        if (!$category) {
            abort(404);
        }

        if ($category->slugable->key !== $slug->key) {
            return redirect()->to($category->url);
        }

        $products = $getProductService->getProduct($request, $category->id, null,
            ['slugable', 'variations', 'productCollections', 'variationAttributeSwatchesForProductList', 'promotions']);

        SeoHelper::setTitle($category->name)->setDescription($category->description);

        $meta = new SeoOpenGraph;
        if ($category->image) {
            $meta->setImage(RvMedia::getImageUrl($category->image));
        }
        $meta->setDescription($category->description);
        $meta->setUrl($category->url);
        $meta->setTitle($category->name);

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), url('/'))
            ->add(__('Products'), route('public.products'))
            ->add($category->name, $category->url);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PRODUCT_CATEGORY_MODULE_SCREEN_NAME, $category);

        return Theme::scope('ecommerce.product-category', compact('category', 'products'),
            'plugins/ecommerce::themes.product-category')->render();
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getProductVariation($id, Request $request, BaseHttpResponse $response)
    {
        $attributes = $request->input('attributes', []);

        $variation = $this->productVariationRepository->getVariationByAttributes($id, $attributes);

        $product = null;

        if ($variation) {
            $product = $this->productRepository->getProductVariations($id, [
                'condition' => [
                    'ec_product_variations.id' => $variation->id,
                    'ec_products.status'       => BaseStatusEnum::PUBLISHED,
                ],
                'select'    => [
                    'ec_products.id',
                    'ec_products.name',
                    'ec_products.quantity',
                    'ec_products.price',
                    'ec_products.sale_price',
                    'ec_products.allow_checkout_when_out_of_stock',
                    'ec_products.with_storehouse_management',
                    'ec_products.images',
                    'ec_products.sku',
                    'ec_products.description',
                    'original_products.images as original_images',
                ],
                'take'      => 1,
            ]);

            if ($product) {
                if ($product->images) {
                    $product->image_with_sizes = rv_get_image_list($product->images, [
                        'origin',
                        'thumb',
                    ]);
                } else {
                    $originalImages = json_decode($product->original_images);
                    $product->image_with_sizes = rv_get_image_list($originalImages, [
                        'origin',
                        'thumb',
                    ]);
                }
            }
        }

        if (!$product) {
            return $response->setError()->setMessage(__(':number product(s) available', ['number' => 0]));
        }

        return $response
            ->setData([
                'id'                         => $product->id,
                'name'                       => $product->name,
                'sku'                        => $product->sku,
                'description'                => $product->description,
                'slug'                       => $product->slug,
                'with_storehouse_management' => $product->with_storehouse_management,
                'quantity'                   => $product->quantity,
                'is_out_of_stock'            => $product->isOutOfStock(),
                'price'                      => $product->price,
                'sale_price'                 => $product->front_sale_price,
                'original_price'             => $product->original_price,
                'image_with_sizes'           => $product->image_with_sizes,
                'display_price'              => format_price($product->price),
                'display_sale_price'         => format_price($product->front_sale_price),
                'sale_percentage'            => get_sale_percentage($product->price, $product->front_sale_price),
            ])
            ->setMessage(__(':number product(s) available', ['number' => ($product->with_storehouse_management && $product->quantity) ? $product->quantity : '> 10']));
    }

    /**
     * @param ReviewRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postCreateReview(ReviewRequest $request, BaseHttpResponse $response)
    {
        $exists = $this->reviewRepository->count([
            'customer_id' => auth('customer')->user()->getAuthIdentifier(),
            'product_id'  => $request->input('product_id'),
        ]);

        if ($exists > 0) {
            return $response
                ->setError()
                ->setMessage(__('You have reviewed this product already!'));
        }

        $request->merge(['customer_id' => auth('customer')->user()->getAuthIdentifier()]);
        $this->reviewRepository->createOrUpdate($request->input());

        return $response->setMessage(__('Added review successfully!'));
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function getDeleteReview($id, BaseHttpResponse $response)
    {
        $this->reviewRepository->deleteBy(['id' => $id]);

        return $response->setMessage(__('Deleted review successfully!'));
    }

    /**
     * @param string $slug
     * @param Request $request
     * @param GetProductService $getProductService
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|Response
     */
    public function getBrand($slug, Request $request, GetProductService $getProductService)
    {
        $slug = $this->slugRepository->getFirstBy([
            'key'            => $slug,
            'reference_type' => Brand::class,
            'prefix'         => SlugHelper::getPrefix(Brand::class),
        ]);

        if (!$slug) {
            abort(404);
        }

        $brand = $this->brandRepository->getFirstBy(['id' => $slug->reference_id], ['*'], ['slugable']);

        if (!$brand) {
            abort(404);
        }

        if ($brand->slugable->key !== $slug->key) {
            return redirect()->to($brand->url);
        }

        SeoHelper::setTitle($brand->name)->setDescription($brand->description);

        Theme::breadcrumb()->add(__('Home'), url('/'))->add($brand->name, $brand->url);

        $meta = new SeoOpenGraph;
        if ($brand->logo) {
            $meta->setImage(RvMedia::getImageUrl($brand->logo));
        }
        $meta->setDescription($brand->description);
        $meta->setUrl($brand->url);
        $meta->setTitle($brand->name);

        SeoHelper::setSeoOpenGraph($meta);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, BRAND_MODULE_SCREEN_NAME, $brand);

        $products = $getProductService->getProduct(
            $request,
            null,
            $brand->id,
            ['slugable', 'variations', 'productCollections', 'variationAttributeSwatchesForProductList', 'promotions']
        );

        return Theme::scope('ecommerce.brand', compact('brand', 'products'),
            'plugins/ecommerce::themes.brand')->render();
    }

    /**
     * @param Request $request
     * @param OrderInterface $orderRepository
     * @return Response
     */
    public function getOrderTracking(Request $request, OrderInterface $orderRepository)
    {
        $code = $request->input('order_id');

        SeoHelper::setTitle(__('Order tracking :code', ['code' => $code ? ' #' . $code : '']));

        Theme::breadcrumb()->add(__('Home'), url('/'))
            ->add(__('Order tracking :code', ['code' => $code ? ' #' . $code : '']), route('public.orders.tracking', $code));

        $orderId = get_order_id_from_order_code('#' . $code);

        $order = null;
        if ($orderId) {
            $order = $orderRepository
                ->getModel()
                ->where('ec_orders.id', $orderId)
                ->join('ec_order_addresses', 'ec_order_addresses.order_id', '=', 'ec_orders.id')
                ->where('ec_order_addresses.email', $request->input('email'))
                ->with(['address', 'payment', 'products'])
                ->select('ec_orders.*')
                ->first();
        }

        return Theme::scope('ecommerce.order-tracking', compact('order'),
            'plugins/ecommerce::themes.order-tracking')->render();
    }
}
