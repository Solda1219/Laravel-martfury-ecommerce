<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ProductVersionRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'price'      => 'numeric|nullable',
            'start_date' => 'date|nullable|required_if:sale_type,1',
            'end_date'   => 'date|nullable|after:' . ($this->input('start_date') ?? now()->toDateTimeString()),
        ];
    }

    /**
     * @return array
     */
    public function messages()
    {
        return [
            'sale_price.max'         => trans('plugins/ecommerce::products.product_create_validate_sale_price_max'),
            'sale_price.required_if' => trans('plugins/ecommerce::products.product_create_validate_sale_price_required_if'),
            'end_date.after'         => trans('plugins/ecommerce::products.product_create_validate_end_date_after'),
            'start_date.required_if' => trans('plugins/ecommerce::products.product_create_validate_start_date_required_if'),
            'sale_price'             => trans('plugins/ecommerce::products.product_create_validate_sale_price'),
        ];
    }
}
