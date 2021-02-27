<?php

namespace Botble\Ecommerce\Supports;

class EcommerceHelper
{
    /**
     * @return bool
     */
    public function isCartEnabled(): bool
    {
        return get_ecommerce_setting('shopping_cart_enabled', '1') == '1';
    }

    /**
     * @return bool
     */
    public function isTaxEnabled(): bool
    {
        return get_ecommerce_setting('ecommerce_tax_enabled', '1') == '1';
    }

    /**
     * @return bool
     */
    public function isReviewEnabled(): bool
    {
        return get_ecommerce_setting('review_enabled', '1') == '1';
    }

    /**
     * @return bool
     */
    public function isQuickBuyButtonEnabled(): bool
    {
        return get_ecommerce_setting('enable_quick_buy_button', '1') == '1';
    }

    /**
     * @return string
     */
    public function getQuickBuyButtonTarget(): string
    {
        return get_ecommerce_setting('quick_buy_target_page', 'checkout');
    }

    /**
     * @return bool
     */
    public function isZipCodeEnabled(): bool
    {
        return get_ecommerce_setting('zip_code_enabled', '0') == '1';
    }
}
