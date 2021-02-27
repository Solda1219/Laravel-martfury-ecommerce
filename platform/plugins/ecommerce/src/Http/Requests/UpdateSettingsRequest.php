<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class UpdateSettingsRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'store_name'    => 'required',
            'store_address' => 'required',
            'store_phone'   => 'required',
            'store_state'   => 'required',
            'store_city'    => 'required',
        ];
    }
}
