<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Auth;

class UserHelper
{
    public static function getEffectiveUser()
    {
        $user = Auth::user();

        if ($user->is_admin && session('selected_user_id')) {
            return \App\Models\User::findOrFail(session('selected_user_id'));
        }

        return $user;
    }
}
