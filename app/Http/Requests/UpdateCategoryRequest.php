<?php
namespace App\Http\Requests;
use Illuminate\Foundation\Http\FormRequest;
class UpdateCategoryRequest extends FormRequest
{
    public function authorize()
    {
        return auth()->check() && auth()->user()->role === 'admin';
    }
    public function rules()
    {
        $categoryId = $this->route('category') ? $this->route('category')->category_id : null;
        return [
            'category_name' => 'required|string|max:255|unique:categories,category_name,' . $categoryId . ',category_id',
            'gender_type'   => 'nullable|in:Men,Women,Unisex,Kids',
        ];
    }
    public function messages()
    {
        return [
            'category_name.required' => 'Category name is required',
            'gender_type.in'         => 'The selected gender type is invalid.',
        ];
    }
}
