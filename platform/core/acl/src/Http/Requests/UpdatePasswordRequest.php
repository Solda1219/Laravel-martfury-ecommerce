<?php

namespace Botble\ACL\Http\Requests;

use Illuminate\Support\Facades\Auth;
use Botble\Support\Http\Requests\Request;

class UpdatePasswordRequest extends Request
{

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'password'              => 'required|min:6|max:60',
            'password_confirmation' => 'same:password',
        ];

        if (Auth::user()->isSuperUser()) {
            return $rules;
        }

        return ['old_password' => 'required|min:6|max:60'] + $rules;
    }
}
