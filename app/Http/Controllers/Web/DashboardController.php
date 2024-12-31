<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Dashboard;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function totalUsersCount() {
        $totalUsers = User::count();

        return response()->json($totalUsers);
    }

    public function activeUsersCount() {
        $count = DB::table('oauth_access_tokens')
            ->where('name', 'UserAccessToken')
            ->count();

        return response()->json([
            'user_access_token_count' => $count,
        ]);
    }
}
