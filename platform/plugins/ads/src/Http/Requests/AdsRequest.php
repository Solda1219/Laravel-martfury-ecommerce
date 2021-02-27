<?php

namespace Botble\Ads\Http\Requests;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Support\Http\Requests\Request;
use Illuminate\Validation\Rule;

class AdsRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name'     => 'required',
            'key'      => 'required|max:120|unique:ads,key,' . $this->route('ads'),
            'location' => 'required',
            'status'   => Rule::in(BaseStatusEnum::values()),
        ];
    }
}
