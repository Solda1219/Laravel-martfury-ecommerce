<?php

namespace Botble\Ecommerce\Http\Controllers;

use Assets;
use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Forms\ProductForm;
use Botble\Ecommerce\Http\Requests\CreateProductWhenCreatingOrderRequest;
use Botble\Ecommerce\Http\Requests\ProductRequest;
use Botble\Ecommerce\Http\Requests\ProductUpdateOrderByRequest;
use Botble\Ecommerce\Http\Requests\ProductVersionRequest;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Eloquent\ProductVariationRepository;
use Botble\Ecommerce\Repositories\Interfaces\BrandInterface;
use Botble\Ecommerce\Repositories\Interfaces\GroupedProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductCollectionInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use Botble\Ecommerce\Services\Products\CreateProductVariationsService;
use Botble\Ecommerce\Services\Products\StoreAttributesOfProductService;
use Botble\Ecommerce\Services\Products\StoreProductService;
use Botble\Ecommerce\Services\StoreProductTagService;
use Botble\Ecommerce\Tables\ProductTable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use RvMedia;
use Throwable;

class ProductController extends BaseController
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
     * @var ProductCollectionInterface
     */
    protected $productCollectionRepository;

    /**
     * @var BrandInterface
     */
    protected $brandRepository;

    /**
     * @var ProductAttributeInterface
     */
    protected $productAttributeRepository;

    /**
     * ProductController constructor.
     * @param ProductInterface $productRepository
     * @param ProductCategoryInterface $productCategoryRepository
     * @param ProductCollectionInterface $productCollectionRepository
     * @param BrandInterface $brandRepository
     * @param ProductAttributeInterface $productAttributeRepository
     */
    public function __construct(
        ProductInterface $productRepository,
        ProductCategoryInterface $productCategoryRepository,
        ProductCollectionInterface $productCollectionRepository,
        BrandInterface $brandRepository,
        ProductAttributeInterface $productAttributeRepository
    ) {
        $this->productRepository = $productRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productCollectionRepository = $productCollectionRepository;
        $this->brandRepository = $brandRepository;
        $this->productAttributeRepository = $productAttributeRepository;
    }

    /**
     * @param ProductTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(ProductTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/ecommerce::products.name'));

        Assets::addScripts(['bootstrap-editable'])
            ->addStyles(['bootstrap-editable']);

        return $dataTable->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/ecommerce::products.create'));

        Assets::addStyles(['datetimepicker'])
            ->addScripts([
                'moment',
                'datetimepicker',
                'jquery-ui',
                'input-mask',
                'blockui',
            ])
            ->addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/bootstrap-confirmation/bootstrap-confirmation.min.js',
                'vendor/core/plugins/ecommerce/js/edit-product.js',
            ]);

        return $formBuilder->create(ProductForm::class)->renderForm();
    }

    /**
     * @param ProductRequest $request
     * @param StoreProductService $service
     * @param BaseHttpResponse $response
     * @param ProductVariationInterface $variationRepository
     * @param ProductVariationItemInterface $productVariationItemRepository
     * @param GroupedProductInterface $groupedProductRepository
     * @param StoreAttributesOfProductService $storeAttributesOfProductService
     * @param StoreProductTagService $storeProductTagService
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function store(
        ProductRequest $request,
        StoreProductService $service,
        BaseHttpResponse $response,
        ProductVariationInterface $variationRepository,
        ProductVariationItemInterface $productVariationItemRepository,
        GroupedProductInterface $groupedProductRepository,
        StoreAttributesOfProductService $storeAttributesOfProductService,
        StoreProductTagService $storeProductTagService
    ) {
        $product = $this->productRepository->getModel();

        $product = $service->execute($request, $product);
        $storeProductTagService->execute($request, $product);

        $addedAttributes = $request->input('added_attributes', []);

        if ($request->input('is_added_attributes') == 1 && $addedAttributes) {
            $storeAttributesOfProductService->execute($product, array_keys($addedAttributes));

            $variation = $variationRepository->create([
                'configurable_product_id' => $product->id,
            ]);

            foreach ($addedAttributes as $attribute) {
                $productVariationItemRepository->createOrUpdate([
                    'attribute_id' => $attribute,
                    'variation_id' => $variation->id,
                ]);
            }

            $variation = $variation->toArray();

            $variation['variation_default_id'] = $variation['id'];

            $variation['sku'] = $product->sku ?? time();
            foreach ($addedAttributes as $attributeId) {
                $attribute = $this->productAttributeRepository->findById($attributeId);
                if ($attribute) {
                    $variation['sku'] .= '-' . $attribute->slug;
                }
            }

            $this->postSaveAllVersions([$variation['id'] => $variation], $variationRepository, $product->id, $response);
        }

        if ($request->has('grouped_products')) {
            $groupedProductRepository->createGroupedProducts($product->id, array_map(function ($item) {
                return [
                    'id'  => $item,
                    'qty' => 1,
                ];
            }, array_filter(explode(',', $request->input('grouped_products', '')))));
        }

        return $response
            ->setPreviousUrl(route('products.index'))
            ->setNextUrl(route('products.edit', $product->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param array $versionInRequest
     * @param ProductVariationInterface $productVariation
     * @param int $id
     * @param BaseHttpResponse $response
     * @param bool $isUpdateProduct
     * @return BaseHttpResponse
     */
    public function postSaveAllVersions(
        $versionInRequest,
        ProductVariationInterface $productVariation,
        $id,
        BaseHttpResponse $response,
        $isUpdateProduct = true
    ) {
        $product = $this->productRepository->findOrFail($id);

        foreach ($versionInRequest as $variationId => $version) {

            $variation = $productVariation->findById($variationId);

            if (!$variation->product_id || $isUpdateProduct) {
                $isNew = false;
                $productRelatedToVariation = $this->productRepository->findById($variation->product_id);

                if (!$productRelatedToVariation) {
                    $productRelatedToVariation = $this->productRepository->getModel();
                    $isNew = true;
                }

                $productRelatedToVariation->fill($version);

                $productRelatedToVariation->name = $product->name;
                $productRelatedToVariation->status = $product->status;
                $productRelatedToVariation->category_id = $product->category_id;
                $productRelatedToVariation->brand_id = $product->brand_id;
                $productRelatedToVariation->is_variation = 1;

                $productRelatedToVariation->sku = Arr::get($version, 'sku');
                if (!$productRelatedToVariation->sku) {
                    $productRelatedToVariation->sku = $product->sku ?? time();
                    if (isset($version['attribute_sets']) && is_array($version['attribute_sets'])) {
                        foreach ($version['attribute_sets'] as $attributeId) {
                            $attribute = $this->productAttributeRepository->findById($attributeId);
                            if ($attribute) {
                                $productRelatedToVariation->sku .= '-' . $attribute->slug;
                            }
                        }
                    }
                }
                $productRelatedToVariation->price = Arr::get($version, 'price', $product->price);
                $productRelatedToVariation->sale_price = Arr::get($version, 'sale_price', $product->sale_price);
                $productRelatedToVariation->description = Arr::get($version, 'description');
                $productRelatedToVariation->images = json_encode(array_values(array_filter(Arr::get($version, 'images', []))));

                $productRelatedToVariation->length = Arr::get($version, 'length', $product->length);
                $productRelatedToVariation->wide = Arr::get($version, 'wide', $product->wide);
                $productRelatedToVariation->height = Arr::get($version, 'height', $product->height);
                $productRelatedToVariation->weight = Arr::get($version, 'weight', $product->weight);

                $productRelatedToVariation->with_storehouse_management = Arr::get($version,
                    'with_storehouse_management', 0);
                $productRelatedToVariation->quantity = Arr::get($version, 'quantity', $product->quantity);
                $productRelatedToVariation->allow_checkout_when_out_of_stock = Arr::get($version,
                    'allow_checkout_when_out_of_stock', 0);

                $productRelatedToVariation->sale_type = (int)Arr::get($version, 'sale_type', $product->sale_type);

                if ($productRelatedToVariation->sale_type == 0) {
                    $productRelatedToVariation->start_date = null;
                    $productRelatedToVariation->end_date = null;
                } else {
                    $productRelatedToVariation->start_date = Arr::get($version, 'start_date', $product->start_date);
                    $productRelatedToVariation->end_date = Arr::get($version, 'end_date', $product->end_date);
                }

                $productRelatedToVariation = $this->productRepository->createOrUpdate($productRelatedToVariation);

                if ($isNew) {
                    event(new CreatedContentEvent(PRODUCT_MODULE_SCREEN_NAME, request(), $productRelatedToVariation));
                } else {
                    event(new UpdatedContentEvent(PRODUCT_MODULE_SCREEN_NAME, request(), $productRelatedToVariation));
                }

                $variation->product_id = $productRelatedToVariation->id;
            }

            $variation->is_default = Arr::get($version, 'variation_default_id', 0) == $variation->id;

            $productVariation->createOrUpdate($variation);

            if (isset($version['attribute_sets']) && is_array($version['attribute_sets'])) {
                $variation->productAttributes()->sync($version['attribute_sets']);
            }
        }

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        $product = $this->productRepository->findOrFail($id);

        if ($product->is_variation) {
            abort(404);
        }

        page_title()->setTitle(trans('plugins/ecommerce::products.edit', ['name' => $product->name]));

        Assets::addStyles(['datetimepicker'])
            ->addScripts([
                'moment',
                'datetimepicker',
                'jquery-ui',
                'input-mask',
                'blockui',
            ])
            ->addStylesDirectly(['vendor/core/plugins/ecommerce/css/ecommerce.css'])
            ->addScriptsDirectly([
                'vendor/core/plugins/ecommerce/libraries/bootstrap-confirmation/bootstrap-confirmation.min.js',
                'vendor/core/plugins/ecommerce/js/edit-product.js',
            ]);

        return $formBuilder
            ->create(ProductForm::class, ['model' => $product])
            ->renderForm();
    }

    /**
     * @param int $id
     * @param ProductRequest $request
     * @param StoreProductService $service
     * @param GroupedProductInterface $groupedProductRepository
     * @param BaseHttpResponse $response
     * @param ProductVariationInterface $variationRepository
     * @param ProductVariationItemInterface $productVariationItemRepository
     * @param StoreProductTagService $storeProductTagService
     * @return BaseHttpResponse|JsonResponse|RedirectResponse
     */
    public function update(
        $id,
        ProductRequest $request,
        StoreProductService $service,
        GroupedProductInterface $groupedProductRepository,
        BaseHttpResponse $response,
        ProductVariationInterface $variationRepository,
        ProductVariationItemInterface $productVariationItemRepository,
        StoreProductTagService $storeProductTagService
    ) {
        $product = $this->productRepository->findOrFail($id);

        $product = $service->execute($request, $product);
        $storeProductTagService->execute($request, $product);

        $variationRepository
            ->getModel()
            ->where('configurable_product_id', $product->id)
            ->update(['is_default' => 0]);

        $defaultVariation = $variationRepository->findById($request->input('variation_default_id'));
        if ($defaultVariation) {
            $defaultVariation->is_default = true;
            $defaultVariation->save();
        }

        $addedAttributes = $request->input('added_attributes', []);

        if ($request->input('is_added_attributes') == 1 && $addedAttributes) {
            $result = $variationRepository->getVariationByAttributesOrCreate($id, $addedAttributes);

            /**
             * @var Collection $variation
             */
            $variation = $result['variation'];

            foreach ($addedAttributes as $attribute) {
                $productVariationItemRepository->createOrUpdate([
                    'attribute_id' => $attribute,
                    'variation_id' => $variation->id,
                ]);
            }

            $variation = $variation->toArray();
            $variation['variation_default_id'] = $variation['id'];

            $product->productAttributeSets()->sync(array_keys($addedAttributes));

            $variation['sku'] = $product->sku ?? time();
            foreach (array_keys($addedAttributes) as $attributeId) {
                $attribute = $this->productAttributeRepository->findById($attributeId);
                if ($attribute) {
                    $variation['sku'] .= '-' . $attribute->slug;
                }
            }

            $this->postSaveAllVersions([$variation['id'] => $variation], $variationRepository, $product->id, $response);
        } elseif ($product->variations()->count() === 0) {
            $product->productAttributeSets()->detach();
            $product->productAttributes()->detach();
        }

        if ($request->has('grouped_products')) {
            $groupedProductRepository->createGroupedProducts($product->id, array_map(function ($item) {
                return [
                    'id'  => $item,
                    'qty' => 1,
                ];
            }, array_filter(explode(',', $request->input('grouped_products', '')))));
        }

        return $response
            ->setPreviousUrl(route('products.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param ProductVariationInterface $variationRepository
     * @param BaseHttpResponse $response
     * @param StoreAttributesOfProductService $storeAttributesOfProductService
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function postAddAttributeToProduct(
        $id,
        Request $request,
        ProductVariationInterface $variationRepository,
        BaseHttpResponse $response,
        StoreAttributesOfProductService $storeAttributesOfProductService
    ) {
        $product = $this->productRepository->findOrFail($id);

        $addedAttributes = $request->input('added_attributes', []);
        if ($addedAttributes) {
            foreach ($addedAttributes as $key => $addedAttribute) {
                if (empty($addedAttribute)) {
                    unset($addedAttributes[$key]);
                }
            }

            if (!empty($addedAttributes)) {
                $result = $variationRepository->getVariationByAttributesOrCreate($id, $addedAttributes);

                $addedAttributeSets = $request->input('added_attribute_sets', []);
                foreach ($addedAttributeSets as $key => $addedAttributeSet) {
                    if (empty($addedAttributeSet)) {
                        unset($addedAttributeSets[$key]);
                    }
                }

                $storeAttributesOfProductService->execute($product, $addedAttributeSets);

                $variation = $result['variation']->toArray();
                $variation['variation_default_id'] = $variation['id'];

                $this->postSaveAllVersions([$variation['id'] => $variation], $variationRepository, $id, $response);
            }
        }

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($id, Request $request, BaseHttpResponse $response)
    {
        $product = $this->productRepository->findOrFail($id);

        try {
            $this->productRepository->deleteBy(['id' => $id]);
            event(new DeletedContentEvent(PRODUCT_MODULE_SCREEN_NAME, $request, $product));
            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        } catch (Exception $exception) {
            return $response
                ->setError()
                ->setMessage($exception->getMessage());
        }
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function deletes(Request $request, BaseHttpResponse $response)
    {
        $ids = $request->input('ids');
        if (empty($ids)) {
            return $response
                ->setError()
                ->setMessage(trans('core/base::notices.no_select'));
        }

        foreach ($ids as $id) {
            $product = $this->productRepository->findOrFail($id);
            $this->productRepository->delete($product);
            event(new DeletedContentEvent(PRODUCT_MODULE_SCREEN_NAME, $request, $product));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }

    /**
     * @param ProductVariationInterface $productVariation
     * @param ProductVariationItemInterface $productVariationItem
     * @param int $variationId
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function postDeleteVersion(
        ProductVariationInterface $productVariation,
        ProductVariationItemInterface $productVariationItem,
        $variationId,
        BaseHttpResponse $response
    ) {
        $variation = $productVariation->findOrFail($variationId);

        $productVariationItem->deleteBy(['variation_id' => $variationId]);

        $productRelatedToVariation = $this->productRepository->findById($variation->product_id);
        if ($productRelatedToVariation) {
            event(new DeletedContentEvent(PRODUCT_MODULE_SCREEN_NAME, request(), $productRelatedToVariation));
        }
        $this->productRepository->deleteBy(['id' => $variation->product_id]);

        $result = $productVariation->delete($variation);

        if ($variation->is_default) {
            $latestVariation = $productVariation->getFirstBy(['configurable_product_id' => $variation->configurable_product_id]);
            $originProduct = $this->productRepository->findById($variation->configurable_product_id);
            if ($latestVariation) {
                $latestVariation->is_default = 1;
                $productVariation->createOrUpdate($latestVariation);
                if ($originProduct && $latestVariation->product->id) {
                    $originProduct->sku = $latestVariation->product->sku;
                    $originProduct->price = $latestVariation->product->price;
                    $originProduct->length = $latestVariation->product->length;
                    $originProduct->wide = $latestVariation->product->wide;
                    $originProduct->height = $latestVariation->product->height;
                    $originProduct->weight = $latestVariation->product->weight;

                    $originProduct->with_storehouse_management = $latestVariation->product->with_storehouse_management;
                    $originProduct->quantity = $latestVariation->product->quantity;
                    $originProduct->allow_checkout_when_out_of_stock = $latestVariation->product->allow_checkout_when_out_of_stock;

                    $originProduct->sale_price = $latestVariation->product->sale_price;
                    $originProduct->sale_type = $latestVariation->product->sale_type;
                    $originProduct->start_date = $latestVariation->product->start_date;
                    $originProduct->end_date = $latestVariation->product->end_date;
                    $this->productRepository->createOrUpdate($originProduct);
                }
            } else {
                $originProduct->productAttributeSets()->detach();
                $originProduct->productAttributes()->detach();
            }
        }

        if ($result) {
            return $response->setMessage(trans('core/base::notices.delete_success_message'));
        }

        return $response
            ->setError()
            ->setMessage(trans('core/base::notices.delete_error_message'));
    }

    /**
     * @param ProductVersionRequest $request
     * @param ProductVariationRepository|ProductVariationInterface $productVariation
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postAddVersion(
        ProductVersionRequest $request,
        ProductVariationInterface $productVariation,
        $id,
        BaseHttpResponse $response
    ) {
        $addedAttributes = $request->input('attribute_sets', []);

        if ($addedAttributes && !empty($addedAttributes) && is_array($addedAttributes)) {
            $result = $productVariation->getVariationByAttributesOrCreate($id, $addedAttributes);
            if (!$result['created']) {
                return $response
                    ->setError()
                    ->setMessage(trans('plugins/ecommerce::products.form.variation_existed'));
            }

            $this->postSaveAllVersions([$result['variation']->id => $request->input()], $productVariation, $id, $response);

            return $response->setMessage(trans('plugins/ecommerce::products.form.added_variation_success'));
        }

        return $response
            ->setError()
            ->setMessage(trans('plugins/ecommerce::products.form.no_attributes_selected'));
    }

    /**
     * @param int $id
     * @param ProductVariationInterface $productVariation
     * @param BaseHttpResponse $response
     * @param ProductAttributeSetInterface $productAttributeSetRepository
     * @param ProductAttributeInterface $productAttributeRepository
     * @param ProductVariationItemInterface $productVariationItemRepository
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getVersionForm(
        $id,
        ProductVariationInterface $productVariation,
        BaseHttpResponse $response,
        ProductAttributeSetInterface $productAttributeSetRepository,
        ProductAttributeInterface $productAttributeRepository,
        ProductVariationItemInterface $productVariationItemRepository
    ) {
        $variation = $productVariation->findOrFail($id);
        $product = $this->productRepository->findOrFail($variation->product_id);

        $productAttributeSets = $productAttributeSetRepository->getAllWithSelected($variation->configurable_product_id);
        $productAttributes = $productAttributeRepository->getAllWithSelected($variation->configurable_product_id);

        $productVariationsInfo = $productVariationItemRepository->getVariationsInfo([$id]);

        $originalProduct = $product;

        return $response
            ->setData(
                view('plugins/ecommerce::products.partials.product-variation-form', compact(
                    'productAttributeSets',
                    'productAttributes',
                    'product',
                    'productVariationsInfo',
                    'originalProduct'
                ))->render()
            );
    }

    /**
     * @param Request $request
     * @param ProductVariationRepository|ProductVariationInterface $productVariation
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function postUpdateVersion(
        ProductVersionRequest $request,
        ProductVariationInterface $productVariation,
        $id,
        BaseHttpResponse $response
    ) {
        $variation = $productVariation->findOrFail($id);

        $addedAttributes = $request->input('attribute_sets', []);

        if ($addedAttributes && !empty($addedAttributes) && is_array($addedAttributes)) {

            $result = $productVariation->getVariationByAttributesOrCreate($variation->configurable_product_id,
                $addedAttributes);

            if (!$result['created'] && $result['variation']->id !== $variation->id) {
                return $response
                    ->setError()
                    ->setMessage(trans('plugins/ecommerce::products.form.variation_existed'));
            }

            if ($variation->is_default) {
                $request->merge([
                    'variation_default_id' => $variation->id,
                ]);
            }

            $this->postSaveAllVersions([$variation->id => $request->input()], $productVariation, $variation->product_id, $response);

            $productVariation->deleteBy(['product_id' => null]);

            return $response->setMessage(trans('plugins/ecommerce::products.form.updated_variation_success'));
        }

        return $response
            ->setError()
            ->setMessage(trans('plugins/ecommerce::products.form.no_attributes_selected'));
    }

    /**
     * @param CreateProductVariationsService $service
     * @param ProductVariationInterface $productVariation
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postGenerateAllVersions(
        CreateProductVariationsService $service,
        ProductVariationInterface $productVariation,
        $id,
        BaseHttpResponse $response
    ) {
        $product = $this->productRepository->findOrFail($id);

        $variations = $service->execute($product);

        $variationInfo = [];

        foreach ($variations as $variation) {
            /**
             * @var Collection $variation
             */
            $data = $variation->toArray();
            if ((int)$variation->is_default === 1) {
                $data['variation_default_id'] = $variation->id;
            }
            $variationInfo[$variation->id] = $data;
        }

        $this->postSaveAllVersions($variationInfo, $productVariation, $id, $response, false);

        return $response->setMessage(trans('plugins/ecommerce::products.form.created_all_variation_success'));
    }

    /**
     * @param Request $request
     * @param StoreAttributesOfProductService $service
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Exception
     */
    public function postStoreRelatedAttributes(
        Request $request,
        StoreAttributesOfProductService $service,
        $id,
        BaseHttpResponse $response
    ) {
        $product = $this->productRepository->findOrFail($id);

        $attributeSets = $request->input('attribute_sets', []);

        $service->execute($product, $attributeSets);

        return $response->setMessage(trans('plugins/ecommerce::products.form.updated_product_attributes_success'));
    }

    /**
     * @param int $id
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getListProductForSearch($id, Request $request, BaseHttpResponse $response)
    {
        $availableProducts = $this->productRepository
            ->advancedGet([
                'condition' => [
                    'status' => BaseStatusEnum::PUBLISHED,
                    ['is_variation', '<>', 1],
                    ['id', '<>', $id],
                    ['name', 'LIKE', '%' . $request->input('keyword') . '%'],
                ],
                'select'    => [
                    'id',
                    'name',
                    'images',
                ],
                'paginate'  => [
                    'per_page'      => 5,
                    'type'          => 'simplePaginate',
                    'current_paged' => (int)$request->input('page', 1),
                ],
            ]);

        $includeVariation = $request->input('include_variation', 0);

        return $response->setData(
            view('plugins/ecommerce::products.partials.panel-search-data', compact(
                'availableProducts',
                'includeVariation'
            ))->render()
        );
    }

    /**
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     * @throws Throwable
     */
    public function getRelationBoxes($id, BaseHttpResponse $response)
    {
        $product = null;
        if ($id) {
            $product = $this->productRepository->findById($id);
        }

        return $response->setData(view('plugins/ecommerce::products.partials.extras', compact('product'))->render());
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getListProductForSelect(Request $request, BaseHttpResponse $response)
    {
        $availableProducts = $this->productRepository
            ->getModel()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->where('is_variation', '<>', 1)
            ->where('name', 'LIKE', '%' . $request->input('keyword') . '%')
            ->select([
                'ec_products.*',
            ])
            ->distinct('ec_products.id');

        $includeVariation = $request->input('include_variation', 0);
        if ($includeVariation) {
            /**
             * @var Builder $availableProducts
             */
            $availableProducts = $availableProducts
                ->join('ec_product_variations', 'ec_product_variations.configurable_product_id', '=', 'ec_products.id')
                ->join('ec_product_variation_items', 'ec_product_variation_items.variation_id', '=',
                    'ec_product_variations.id');
        }
        $availableProducts = $availableProducts->simplePaginate(5);

        if ($includeVariation) {
            foreach ($availableProducts as &$availableProduct) {
                $availableProduct->image_url = RvMedia::getImageUrl(Arr::first($availableProduct->images) ?? null,
                    'thumb', false, RvMedia::getDefaultImage());
                $availableProduct->price = $availableProduct->front_sale_price;
                foreach ($availableProduct->variations as &$variation) {
                    $variation->price = $variation->product->front_sale_price;
                    foreach ($variation->variationItems as &$variationItem) {
                        $variationItem->attribute_title = $variationItem->attribute->title;
                    }
                }
            }
        }

        return $response->setData($availableProducts);
    }

    /**
     * @param CreateProductWhenCreatingOrderRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postCreateProductWhenCreatingOrder(
        CreateProductWhenCreatingOrderRequest $request,
        BaseHttpResponse $response
    ) {
        $product = $this->productRepository->createOrUpdate($request->input());

        event(new CreatedContentEvent(PRODUCT_MODULE_SCREEN_NAME, $request, $product));

        $product->image_url = RvMedia::getImageUrl(Arr::first($product->images) ?? null, 'thumb', false,
            RvMedia::getDefaultImage());
        $product->price = $product->front_sale_price;
        $product->select_qty = 1;

        return $response
            ->setData($product)
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param Request $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function getAllProductAndVariations(Request $request, BaseHttpResponse $response)
    {
        $availableProducts = $this->productRepository
            ->getModel()
            ->where('status', BaseStatusEnum::PUBLISHED)
            ->where('is_variation', '<>', 1)
            ->where('name', 'LIKE', '%' . $request->input('keyword') . '%')
            ->select([
                'ec_products.*',
            ])
            ->distinct('ec_products.id')
            ->leftJoin('ec_product_variations', 'ec_product_variations.configurable_product_id', '=', 'ec_products.id')
            ->leftJoin('ec_product_variation_items', 'ec_product_variation_items.variation_id', '=',
                'ec_product_variations.id')
            ->simplePaginate(5);

        foreach ($availableProducts as &$availableProduct) {
            /**
             * @var Product $availableProduct
             */
            $availableProduct->image_url = RvMedia::getImageUrl(Arr::first($availableProduct->images) ?? null, 'thumb',
                false, RvMedia::getDefaultImage());
            $availableProduct->price = $availableProduct->front_sale_price;
            $availableProduct->is_out_of_stock = $availableProduct->isOutOfStock();

            foreach ($availableProduct->variations as &$variation) {
                $variation->price = $variation->product->front_sale_price;
                $variation->is_out_of_stock = $variation->product->isOutOfStock();
                $variation->quantity = $variation->product->quantity;
                foreach ($variation->variationItems as &$variationItem) {
                    $variationItem->attribute_title = $variationItem->attribute->title;
                }
            }
        }

        return $response->setData($availableProducts);
    }

    /**
     * @param ProductUpdateOrderByRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function postUpdateOrderBy(ProductUpdateOrderByRequest $request, BaseHttpResponse $response)
    {
        $product = $this->productRepository->findOrFail($request->input('pk'));
        $product->order = $request->input('value', 0);
        $this->productRepository->createOrUpdate($product);

        return $response->setMessage(trans('core/base::notices.update_success_message'));
    }
}
