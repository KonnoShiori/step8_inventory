<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Arr;

class SaleRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'product_id' => 'required | integer',
            'purchase_quantity' => 'required | integer | min:1',
        ];
    }

    /**
     * 項目名
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'product_id' => '商品情報',
            'purchase_quantity' => '購入数',
        ];
    }

    /**
     * エラーメッセージ
     *
     * @return array
     */
    public function messages()
    {
        return [
            'product_id.required' => ':attributeを選択してください',
            'purchase_quantity.required' => ':attributeを選択してください',
            'product_id.integer' => ':attributeが正しくありません',
            'purchase_quantity.integer' => ':attributeは整数で入力してください',
            'purchase_quantity.min' => ':attributeは:min以上を入力してください',
        ];
    }

    /**
     * バリデーション失敗時
     * @param Validator $validator
     * @return void
     * @throw HttpResponseException
     */
    protected function failedValidation(Validator $validator)
    {
        $response = array(
            'result' => 'fail',
            'message' => '入力値が不正のため購入処理ができません。',
            'errors' =>  $validator->errors()->toArray()
        );

        throw new HttpResponseException(response()->json($response,422));
    }
}
