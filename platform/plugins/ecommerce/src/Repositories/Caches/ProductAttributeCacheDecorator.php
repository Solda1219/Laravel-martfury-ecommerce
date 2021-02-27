<?php

namespace Botble\Ecommerce\Repositories\Caches;

use Botble\Ecommerce\Repositories\Interfaces\ProductAttributeInterface;
use Botble\Support\Repositories\Caches\CacheAbstractDecorator;

class ProductAttributeCacheDecorator extends CacheAbstractDecorator implements ProductAttributeInterface
{
    /**
     * {@inheritDoc}
     */
    public function getAllWithSelected($productId)
    {
        return $this->getDataIfExistCache(__FUNCTION__, func_get_args());
    }
}
