<?php

namespace App\Actions\Fortify;

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Laravel\Fortify\Contracts\CreatesNewUsers;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CreateNewUser implements CreatesNewUsers
{
    /**
     * ユーザー作成処理（Fortifyから呼ばれる）
     */
    public function create(array $input): User
    {
        // ✅ RegisterRequest のルールとメッセージを使ってバリデーション
        $validator = Validator::make(
            $input,
            (new RegisterRequest())->rules(),
            (new RegisterRequest())->messages()
        );

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        // ✅ ユーザー登録
        return User::create([
            'name' => $input['name'],
            'email' => $input['email'],
            'password' => Hash::make($input['password']),
        ]);
    }
}
