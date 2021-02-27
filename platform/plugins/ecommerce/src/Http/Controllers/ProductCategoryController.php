<?php

namespace Botble\Ecommerce\Http\Controllers;

use Botble\Base\Events\CreatedContentEvent;
use Botble\Base\Events\DeletedContentEvent;
use Botble\Base\Events\UpdatedContentEvent;
use Botble\Base\Forms\FormBuilder;
use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Forms\ProductCategoryForm;
use Botble\Ecommerce\Http\Requests\ProductCategoryRequest;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Ecommerce\Tables\ProductCategoryTable;
use Exception;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Throwable;

class ProductCategoryController extends BaseController
{
    /**
     * @var ProductCategoryInterface
     */
    protected $productCategoryRepository;

    /**
     * ProductCategoryController constructor.
     * @param ProductCategoryInterface $productCategoryRepository
     */
    public function __construct(ProductCategoryInterface $productCategoryRepository)
    {
        $this->productCategoryRepository = $productCategoryRepository;
    }

    /**
     * @param ProductCategoryTable $dataTable
     * @return Factory|View
     * @throws Throwable
     */
    public function index(ProductCategoryTable $dataTable)
    {
        page_title()->setTitle(trans('plugins/ecommerce::product-categories.name'));

        return $dataTable->renderTable();
    }

    /**
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function create(FormBuilder $formBuilder)
    {
        page_title()->setTitle(trans('plugins/ecommerce::product-categories.create'));

        return $formBuilder->create(ProductCategoryForm::class)->renderForm();
    }

    /**
     * @param ProductCategoryRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store(ProductCategoryRequest $request, BaseHttpResponse $response)
    {
        $productCategory = $this->productCategoryRepository->createOrUpdate($request->input());
        event(new CreatedContentEvent(PRODUCT_CATEGORY_MODULE_SCREEN_NAME, $request, $productCategory));

        return $response
            ->setPreviousUrl(route('product-categories.index'))
            ->setNextUrl(route('product-categories.edit', $productCategory->id))
            ->setMessage(trans('core/base::notices.create_success_message'));
    }

    /**
     * @param int $id
     * @param FormBuilder $formBuilder
     * @return string
     */
    public function edit($id, FormBuilder $formBuilder)
    {
        $category = $this->productCategoryRepository->findOrFail($id);

        page_title()->setTitle(trans('plugins/ecommerce::product-categories.edit') . ' "' . $category->name . '"');

        return $formBuilder->create(ProductCategoryForm::class, ['model' => $category])->renderForm();
    }

    /**
     * @param int $id
     * @param ProductCategoryRequest $request
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function update($id, ProductCategoryRequest $request, BaseHttpResponse $response)
    {
        $productCategory = $this->productCategoryRepository->findOrFail($id);
        $productCategory->fill($request->input());

        $this->productCategoryRepository->createOrUpdate($productCategory);
        event(new UpdatedContentEvent(PRODUCT_CATEGORY_MODULE_SCREEN_NAME, $request, $productCategory));

        return $response
            ->setPreviousUrl(route('product-categories.index'))
            ->setMessage(trans('core/base::notices.update_success_message'));
    }

    /**
     * @param Request $request
     * @param int $id
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy(Request $request, $id, BaseHttpResponse $response)
    {
        try {
            $productCategory = $this->productCategoryRepository->findOrFail($id);

            $this->productCategoryRepository->delete($productCategory);
            event(new DeletedContentEvent(PRODUCT_CATEGORY_MODULE_SCREEN_NAME, $request, $productCategory));
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
     * @throws Exception
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
            $productCategory = $this->productCategoryRepository->findOrFail($id);
            $this->productCategoryRepository->delete($productCategory);
            event(new DeletedContentEvent(PRODUCT_CATEGORY_MODULE_SCREEN_NAME, $request, $productCategory));
        }

        return $response->setMessage(trans('core/base::notices.delete_success_message'));
    }
}
