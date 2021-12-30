<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends AddProductRequest
{
    public function prepareForValidation()
    {
        $deletedFiles = json_decode($this->deleted_files);

        return $this->merge([
            'deleted_files' => $deletedFiles
        ]);
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return array_merge(parent::rules(),
            [
                'is_remain_img' => 'required',
                'deleted_files' => 'nullable|array',
                'deleted_files.*' => 'exists:product_images,id',
                'image' => 'required_if:is_remain_img,0'
            ]
        );
    }
}
