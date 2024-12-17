<?php

namespace App\Http\Controllers\Authentication;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ErrorHandlerAuth
{
    // Return only the first error for the highest priority field
    public function errorHierarchy($priorityFields, $errorFields) {
        foreach ($priorityFields  as $field) {
            if (isset($errorFields[$field])) {
                return response()->json(['error' => $errorFields[$field][0]], 422);
            }
        }
        return response()->json(['error' => 'Unknown error'], 500);
    }

    public function ifCredentialExist($credentials) {
        if (isset($credentials['email'])) {
            //
        }
    }
}
