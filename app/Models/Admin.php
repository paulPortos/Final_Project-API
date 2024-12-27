<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Passport\HasApiTokens;

class Admin extends Model
{
    use HasApiTokens;
    protected $table = 'admins';

    protected $fillable = [
        'username',
        'password',
    ];

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
