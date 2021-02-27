<?php

namespace Botble\Ecommerce\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SaveCheckoutInformationRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     *
     */
    public function rules()
    {
        $rules = [];

        $rules['address.address_id'] = 'required_without:address.name';
        if (!$this->has('address.address_id') || $this->input('address.address_id') === 'new') {
            $rules['address.name'] = 'required|min:3|max:120';
            $rules['address.phone'] = 'required|numeric';
            $rules['address.email'] = 'required|email';
            $rules['address.state'] = 'required';
            $rules['address.city'] = 'required';
            $rules['address.address'] = 'required|string';
        }

        if ($this->input('create_account') == 1) {
            $rules['password'] = 'required|min:6';
            $rules['password_confirmation'] = 'required|same:password';
            $rules['address.email'] = 'required|max:60|min:6|email|unique:ec_customers,email';
            $rules['address.name'] = 'required|min:3|max:120';
        }

        return $rules;
    }

    /**
     * @return array
     *
     */
    public function messages()
    {
        $messages = [
            'address.name.required'    => trans('plugins/ecommerce::order.address_name_required'),
            'address.phone.required'   => trans('plugins/ecommerce::order.address_phone_required'),
            'address.email.required'   => trans('plugins/ecommerce::order.address_email_required'),
            'address.email.unique'     => trans('plugins/ecommerce::order.address_email_unique'),
            'address.state.required'   => trans('plugins/ecommerce::order.address_state_required'),
            'address.city.required'    => trans('plugins/ecommerce::order.address_city_required'),
            'address.address.required' => trans('plugins/ecommerce::order.address_address_required'),
        ];

        return array_merge(parent::messages(), $messages);
    }
}
