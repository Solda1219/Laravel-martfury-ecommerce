<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class ShippingRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'title'       => 'required|max:255',
            'state'       => 'required',
            'city'        => 'required',
            'amount'      => 'required|min:0|numeric',
            'currency_id' => 'required|min:1|integer',
        ];
    }
}
