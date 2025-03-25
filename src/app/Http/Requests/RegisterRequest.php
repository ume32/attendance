<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * 認可の設定（今回は誰でもOK）
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * バリデーションルール定義
     */
    public function rules(): array
    {
        return [
            'name' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8'],
            'password_confirmation' => ['required', 'same:password', 'min:8'],
        ];
    }

    /**
     * 日本語のバリデーションエラーメッセージ
     */
    public function messages(): array
    {
        return [
            'name.required' => 'お名前を入力してください',
            'email.required' => 'メールアドレスを入力してください',
            'email.email' => 'メールアドレスの形式で入力してください',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上で入力してください',
            'password_confirmation.required' => '確認用パスワードを入力してください',
            'password_confirmation.same' => 'パスワードと一致しません',
            'password_confirmation.min' => 'パスワードは8文字以上で入力してください',
        ];
    }
}
