<?php

namespace App\Http\Requests;

use App\Models\User\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UserSettingsRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * @return bool
     */
    public function authorize()
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     * @return array
     */
    public function rules()
    {
        return Setting::defaultSettings()->mapWithKeys(function ($value, $key) {
            return [$key => $value['rule']];
        })->toArray();
    }
}
