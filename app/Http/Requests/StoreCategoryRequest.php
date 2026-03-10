<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class StoreCategoryRequest extends FormRequest
{
    public function authorize() { return auth()->check() && auth()->user()->role === 'admin'; }
    public function rules()
    {
        return [
            'category_name' => 'required|string|max:255|unique:categories',
            'gender_type' => 'nullable|in:Men,Women,Unisex,Kids',
        ];
    }
}