<?php

namespace Botble\Ecommerce\Repositories\Eloquent;

use Botble\Ecommerce\Repositories\Interfaces\ProductVariationItemInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class ProductVariationItemRepository extends RepositoriesAbstract implements ProductVariationItemInterface
{
    /**
     * {@inheritDoc}
     */
    public function getVariationsInfo(array $versionIds)
    {
        $result = $this->model
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=',
                'ec_product_attributes.attribute_set_id')
            ->distinct()
            ->whereIn('ec_product_variation_items.variation_id', $versionIds)
            ->select([
                'ec_product_variation_items.variation_id',
                'ec_product_attributes.*',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
            ])
            ->get();

        $this->resetModel();

        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function getProductAttributes($productId)
    {
        $result = $this->model
            ->join('ec_product_attributes', 'ec_product_attributes.id', '=', 'ec_product_variation_items.attribute_id')
            ->join('ec_product_attribute_sets', 'ec_product_attribute_sets.id', '=',
                'ec_product_attributes.attribute_set_id')
            ->join('ec_product_variations', 'ec_product_variations.id', '=', 'ec_product_variation_items.variation_id')
            ->distinct()
            ->where('ec_product_variations.product_id', $productId)
            ->select([
                'ec_product_attributes.*',
                'ec_product_attribute_sets.title as attribute_set_title',
                'ec_product_attribute_sets.slug as attribute_set_slug',
            ])
            ->get();

        $this->resetModel();

        return $result;
    }
}
