<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ExhibitionRequest extends FormRequest
{
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
        return [
            'name' => 'required',
            'description' => 'required|max:255',
            'image' => 'required|image|mimes:jpeg,jpg,png',
            'categories' => 'required',
            'condition' => 'required',
            'price' => 'required|integer|min:0'
        ];
    }

    public function messages()
    {
        return[
            'name.required' => '商品名を入力してください',
            'description.required' => '商品説明を入力してください',
            'description.max' => '商品説明を255文字以内で入力してください',
            'image.required' => '商品画像をアップロードしてください',
            'image.image' => '「.png」または「.jpeg」形式の画像ファイルを指定してください',
            'image.mimes' => '「.png」または「.jpeg」形式の画像ファイルを指定してください',
            'categories.required' => '商品カテゴリーを選択してください',
            'condition.required' => '商品の状態を選択してください',
            'price.required' => '商品価格を入力してください',
            'price.integer' => '商品価格は半角整数で入力してください',
            'price.min' => '商品価格は０円以上で入力してください'
        ];
    }
}
;