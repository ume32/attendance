<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            // 管理者用のルートだったら admin ログインにリダイレクト
            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            // それ以外は一般ユーザーのログインへ
            return route('login');
        }
    }
}
