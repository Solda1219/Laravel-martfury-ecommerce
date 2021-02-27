<?php

namespace Theme\Martfury\Http\Requests;

use Botble\Support\Http\Requests\Request;

class SendDownloadAppLinksRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
        ];
    }
}
