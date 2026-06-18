<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Isay true karein
    }

    public function rules(): array
    {
        return [
            'productCategory'    => 'required|exists:categories,category_id',
            'productBrand'       => 'required|exists:brands,brand_id',
            'productName'        => 'required|string|max:255',
            'productDescription' => 'required|string',
            'price'              => 'required|numeric|min:0|regex:/^\d+(\.\d{1,2})?$/',
            'productStatus'      => 'required|in:active,inactive',
            'colors'             => 'nullable|array',
            'colors.*'           => 'exists:colors,color_id',
            'size'               => 'nullable|array',
            'size.*'             => 'exists:sizes,size_id',
            'productPic'         => 'nullable|array',
            'productPic.*'       => 'image|mimes:jpeg,png,jpg,webp|max:10240'
        ];
    }
}