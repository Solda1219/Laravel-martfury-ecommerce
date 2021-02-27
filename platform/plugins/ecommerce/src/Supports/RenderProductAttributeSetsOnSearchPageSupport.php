<?php

namespace Botble\Ecommerce\Supports;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeSetRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Throwable;

class RenderProductAttributeSetsOnSearchPageSupport
{
    /**
     * @var ProductAttributeSetRepository
     */
    protected $productAttributeSetRepository;

    /**
     * RenderProductAttributeSetsOnSearchPageSupport constructor.
     * @param ProductAttributeSetInterface $productAttributeSetRepository
     */
    public function __construct(ProductAttributeSetInterface $productAttributeSetRepository)
    {
        $this->productAttributeSetRepository = $productAttributeSetRepository;
    }

    /**
     * @param array $params
     * @return string
     * @throws Throwable
     */
    public function render(array $params = [])
    {
        $params = array_merge([
            'view' => 'plugins/ecommerce::themes.attributes.attributes-filter-renderer',
        ], $params);

        $attributeSets = $this->productAttributeSetRepository
            ->advancedGet([
                'condition' => [
                    'status'        => BaseStatusEnum::PUBLISHED,
                    'is_searchable' => 1,
                ],
                'order_by'  => [
                    'order' => 'ASC',
                ],
                'with'      => [
                    'attributes',
                ],
            ]);

        return view($params['view'], compact('attributeSets'))->render();
    }
}
