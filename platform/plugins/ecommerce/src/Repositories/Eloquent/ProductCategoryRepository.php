<?php

namespace Botble\Ecommerce\Repositories\Eloquent;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Interfaces\ProductCategoryInterface;
use Botble\Support\Repositories\Eloquent\RepositoriesAbstract;

class ProductCategoryRepository extends RepositoriesAbstract implements ProductCategoryInterface
{

    /**
     * {@inheritDoc}
     */
    public function getCategories(array $param)
    {
        $param = array_merge([
            'active'      => true,
            'order_by'    => 'desc',
            'is_child'    => null,
            'is_featured' => null,
            'num'         => null,
        ], $param);

        $data = $this->model->select('ec_product_categories.*');

        if ($param['active']) {
            $data = $data->where('ec_product_categories.status', BaseStatusEnum::PUBLISHED);
        }

        if ($param['is_child'] !== null) {
            if ($param['is_child'] === true) {
                $data = $data->where('ec_product_categories.parent_id', '<>', 0);
            } else {
                $data = $data->where('ec_product_categories.parent_id', 0);
            }
        }

        if ($param['is_featured']) {
            $data = $data->where('ec_product_categories.is_featured', $param['is_featured']);
        }

        $data = $data->orderBy('ec_product_categories.order', $param['order_by']);

        if ($param['num'] !== null) {
            $data = $data->limit($param['num']);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getDataSiteMap()
    {
        $data = $this->model
            ->where('ec_product_categories.status', BaseStatusEnum::PUBLISHED)
            ->select('ec_product_categories.*')
            ->orderBy('ec_product_categories.created_at', 'desc');

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getFeaturedCategories($limit)
    {
        $data = $this->model
            ->where([
                'ec_product_categories.status'      => BaseStatusEnum::PUBLISHED,
                'ec_product_categories.is_featured' => 1,
            ])
            ->select([
                'ec_product_categories.id',
                'ec_product_categories.name',
                'ec_product_categories.icon',
            ])
            ->orderBy('ec_product_categories.order', 'asc')
            ->select('ec_product_categories.*')
            ->limit($limit);

        return $this->applyBeforeExecuteQuery($data)->get();
    }

    /**
     * {@inheritDoc}
     */
    public function getAllCategories($active = true)
    {
        $data = $this->model->select('ec_product_categories.*');
        if ($active) {
            $data = $data->where(['ec_product_categories.status' => BaseStatusEnum::PUBLISHED]);
        }

        return $this->applyBeforeExecuteQuery($data)->get();
    }
}
