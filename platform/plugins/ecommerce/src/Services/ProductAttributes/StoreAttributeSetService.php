<?php

namespace Botble\Ecommerce\Services\ProductAttributes;

use Botble\Ecommerce\Models\ProductAttributeSet;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeSetRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeSetInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class StoreAttributeSetService
{
    /**
     * @var ProductAttributeSetRepository
     */
    protected $productAttributeSetRepository;

    /**
     * @var ProductAttributeRepository
     */
    protected $productAttributeRepository;

    /**
     * StoreAttributeSetService constructor.
     * @param ProductAttributeSetInterface $productAttributeSet
     * @param ProductAttributeInterface $productAttribute
     */
    public function __construct(
        ProductAttributeSetInterface $productAttributeSet,
        ProductAttributeInterface $productAttribute
    ) {
        $this->productAttributeSetRepository = $productAttributeSet;
        $this->productAttributeRepository = $productAttribute;
    }

    /**
     * @param Request $request
     * @param ProductAttributeSet $productAttributeSet
     * @return ProductAttributeSet|false|Model
     */
    public function execute(Request $request, ProductAttributeSet $productAttributeSet)
    {
        $data = $request->input();

        $productAttributeSet->fill($data);

        $productAttributeSet = $this->productAttributeSetRepository->createOrUpdate($productAttributeSet);

        $attributes = json_decode($request->get('attributes', '[]'), true) ?: [];
        $deletedAttributes = json_decode($request->get('deleted_attributes', '[]'), true) ?: [];

        $this->deleteAttributes($productAttributeSet->id, $deletedAttributes);
        $this->storeAttributes($productAttributeSet->id, $attributes);

        return $productAttributeSet;
    }

    /**
     * @param int $productAttributeSetId
     * @param array $attributeIds
     * @throws Exception
     */
    protected function deleteAttributes($productAttributeSetId, array $attributeIds)
    {
        foreach ($attributeIds as $id) {
            $this->productAttributeRepository
                ->deleteBy([
                    'id'               => $id,
                    'attribute_set_id' => $productAttributeSetId,
                ]);
        }
    }

    /**
     * @param int $productAttributeSetId
     * @param array $attributes
     */
    protected function storeAttributes($productAttributeSetId, array $attributes)
    {
        foreach ($attributes as $item) {
            if (isset($item['id'])) {
                $attribute = $this->productAttributeRepository->findById($item['id']);
                if (!$attribute) {
                    $item['attribute_set_id'] = $productAttributeSetId;
                    $this->productAttributeRepository->create($item);
                } else {
                    $attribute->fill($item);
                    $this->productAttributeRepository->createOrUpdate($attribute);
                }
            }
        }
    }
}
