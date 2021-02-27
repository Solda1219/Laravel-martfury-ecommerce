<?php

namespace Botble\Ecommerce\Http\Controllers\Fronts;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Interfaces\ProductInterface;
use Cart;
use Illuminate\Routing\Controller;
use Response;
use SeoHelper;
use Theme;

class CompareController extends Controller
{
    /**
     * @var ProductInterface
     */
    protected $productRepository;

    /**
     * CompareController constructor.
     * @param ProductInterface $productRepository
     */
    public function __construct(ProductInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @return Response
     */
    public function index()
    {
        SeoHelper::setTitle(__('Compare'));

        Theme::breadcrumb()->add(__('Home'), url('/'))->add(__('Compare'), route('public.compare'));

        return Theme::scope('ecommerce.compare', [], 'plugins/ecommerce::themes.compare')->render();
    }

    /**
     * @param int $productId
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function store($productId, BaseHttpResponse $response)
    {
        $product = $this->productRepository->findOrFail($productId);


        $duplicates = Cart::instance('compare')->search(function ($cartItem) use ($productId) {
            return $cartItem->id == $productId;
        });

        if (!$duplicates->isEmpty()) {
            return $response
                ->setMessage(__(':product is already in your compare list!', ['product' => $product->name]))
                ->setError(true);
        }

        Cart::instance('compare')->add($productId, $product->name, 1, $product->front_sale_price)
            ->associate(Product::class);

        return $response
            ->setMessage(__('Added product :product to compare list successfully!', ['product' => $product->name]))
            ->setData(['count' => Cart::instance('compare')->count()]);
    }

    /**
     * @param int $productId
     * @param BaseHttpResponse $response
     * @return BaseHttpResponse
     */
    public function destroy($productId, BaseHttpResponse $response)
    {
        Cart::instance('compare')->search(function ($cartItem, $rowId) use ($productId) {
            if ($cartItem->id == $productId) {
                Cart::instance('compare')->remove($rowId);
                return true;
            }
            return false;
        });

        return $response
            ->setMessage(__('Removed product from compare list successfully!'))
            ->setData(['count' => Cart::instance('compare')->count()]);
    }
}
