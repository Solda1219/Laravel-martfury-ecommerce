<?php

namespace Botble\Ecommerce\Services\Products;

use Botble\Ecommerce\Models\Product;
use Botble\Ecommerce\Repositories\Eloquent\ProductAttributeRepository;
use Botble\Ecommerce\Repositories\Eloquent\ProductVariationRepository;
use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Ecommerce\Repositories\Interfaces\ProductVariationInterface;
use Exception;

class StoreAttributesOfProductService
{
    /**
     * @var ProductAttributeRepository
     */
    protected $productAttributeRepository;

    /**
     * @var ProductVariationRepository
     */
    protected $productVariationRepository;

    /**
     * StoreAttributesOfProductService constructor.
     * @param ProductAttributeInterface $productAttributeRepository
     * @param ProductVariationInterface $productVariationRepository =
     */
    public function __construct(
        ProductAttributeInterface $productAttributeRepository,
        ProductVariationInterface $productVariationRepository
    ) {
        $this->productAttributeRepository = $productAttributeRepository;

        $this->productVariationRepository = $productVariationRepository;
    }

    /**
     * @param Product $product
     * @param array $attributeSets
     * @return Product
     * @throws Exception
     */
    public function execute(Product $product, array $attributeSets)
    {
        $product->productAttributeSets()->sync($attributeSets);

        $attributes = $this->productAttributeRepository->getModel()
            ->whereIn('attribute_set_id', $attributeSets)
            ->pluck('id')
            ->all();

        $product->productAttributes()->sync($this->getSelectedAttributes($product, $attributes));

        $this->productVariationRepository->correctVariationItems($product->id, $attributes);

        return $product;
    }

    /**
     * @param Product $product
     * @param array $attributes
     * @return array
     */
    protected function getSelectedAttributes(Product $product, array $attributes)
    {
        $attributeSets = $product->productAttributeSets()
            ->select('attribute_set_id')
            ->pluck('attribute_set_id')
            ->toArray();

        $allRelatedAttributeBySet = $this->productAttributeRepository
            ->allBy([
                ['attribute_set_id', 'IN', $attributeSets],
            ], [], ['id'])
            ->pluck('id')
            ->toArray();

        $newAttributes = [];

        foreach ($attributes as $item) {
            if (in_array($item, $allRelatedAttributeBySet)) {
                $newAttributes[] = $item;
            }
        }

        return $newAttributes;
    }
}
