<?php

namespace App\Http\Controllers\Custom_Libraries;

use Illuminate\Support\Facades\Log;

class NameFormater
{
    public function capitalizeName($first_name) {
        try {
            return ucwords(strtolower($first_name));
        } catch (\Exception $e) {
            Log::error("NameFormater::Class", (array)$e->getMessage());
        }
    }
}
