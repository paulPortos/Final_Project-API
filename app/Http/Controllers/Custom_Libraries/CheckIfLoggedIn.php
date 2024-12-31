<?php

namespace App\Http\Controllers\Custom_Libraries;

use Couchbase\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CheckIfLoggedIn {
    public function checkIfLoggedIn($email): bool
    {
        // Retrieve the user's ID from the users table
        try {
            Log::info("Checking if logged in");
            Log::info($email);
            $userId = DB::table('users')
                ->where('email', $email)
                ->value('id');
            Log::info($userId);
        } catch (QueryException $e) {
            Log::error('CheckIfLoggedIn::Class error = ', (array)$e);
        }

        // Check if the user's ID exists in the client_id column of the oauth_access_tokens table
        $exists = DB::table('oauth_access_tokens')
            ->where('user_id', $userId)
            ->exists();
            Log::info($exists);

        if ($exists) {
            return true;
        } else {
            return false;
        }
    }
}
