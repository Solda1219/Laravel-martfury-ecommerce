<?php

namespace Botble\Ecommerce\Repositories\Eloquent;

use Botble\Ecommerce\Repositories\Interfaces\DiscountInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Database\Query\Builder;

class DiscountRepository extends RepositoriesAbstract implements DiscountInterface
{
    /**
     * {@inheritDoc}
     */
    public function getAvailablePromotions()
    {
        return $this->model
            ->where('type', 'promotion')
            ->where('start_date', '<=', now())
            ->where(function ($query) {
                /**
                 * @var Builder $query
                 */
                return $query
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where(function ($query) {
                /**
                 * @var Builder $query
                 */
                return $query
                    ->whereIn('target', ['all-orders', 'amount-minimum-order'])
                    ->orWhere(function ($sub) {
                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->whereIn('target', ['customer', 'group-products', 'specific-product', 'product-variant'])
                            ->where('product_quantity', '>', 1);
                    });
            })
            ->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getProductPriceBasedOnPromotion(array $productIds = [], array $productCollections = [])
    {
        return $this->model
            ->where('type', 'promotion')
            ->where('start_date', '<=', now())
            ->leftJoin('ec_discount_products', 'ec_discounts.id', '=', 'ec_discount_products.discount_id')
            ->leftJoin('ec_discount_product_collections', 'ec_discounts.id', '=',
                'ec_discount_product_collections.discount_id')
            ->leftJoin('ec_discount_customers', 'ec_discounts.id', '=', 'ec_discount_customers.discount_id')
            ->where(function ($query) use ($productIds, $productCollections) {
                /**
                 * @var Builder $query
                 */
                return $query
                    ->where(function ($sub) use ($productIds) {
                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->where(function ($subSub) {
                                /**
                                 * @var Builder $subSub
                                 */
                                return $subSub
                                    ->where('target', 'specific-product')
                                    ->orWhere('target', 'product-variant');
                            })
                            ->whereIn('ec_discount_products.product_id', $productIds);
                    })
                    ->orWhere(function ($sub) use ($productCollections) {
                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->where('target', 'group-products')
                            ->whereIn('ec_discount_product_collections.product_collection_id', $productCollections);
                    })
                    ->orWhere(function ($sub) {
                        $customerId = auth('customer')->check() ? auth('customer')->user()->id : -1;

                        /**
                         * @var Builder $sub
                         */
                        return $sub
                            ->where('target', 'customer')
                            ->where('ec_discount_customers.customer_id', $customerId);
                    });
            })
            ->where(function ($query) {
                /**
                 * @var Builder $query
                 */
                return $query
                    ->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            })
            ->where('product_quantity', 1)
            ->select('ec_discounts.*')
            ->first();
    }
}
