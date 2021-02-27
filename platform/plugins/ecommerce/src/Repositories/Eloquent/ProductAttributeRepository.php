<?php

namespace Botble\Ecommerce\Repositories\Eloquent;

use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;
use Illuminate\Support\Facades\DB;

class ProductAttributeRepository extends RepositoriesAbstract implements ProductAttributeInterface
{
    /**
     * {@inheritDoc}
     */
    public function getAllWithSelected($productId)
    {
        $data = $this->model
            ->leftJoin(DB::raw('
                (
                    SELECT ec_product_with_attribute.*
                    FROM ec_product_with_attribute
                    WHERE ec_product_with_attribute.product_id = ' . esc_sql($productId) . '
                ) AS PAR
            '), 'ec_product_attributes.id', '=', 'PAR.attribute_id')
            ->distinct()
            ->select([
                'ec_product_attributes.*',
                'PAR.product_id AS is_selected',
            ])
            ->orderBy('ec_product_attributes.order', 'ASC')
            ->get();

        $this->resetModel();

        return $data;
    }
}
