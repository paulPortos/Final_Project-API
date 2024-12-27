<?php

namespace App\Http\Controllers\Custom_Libraries;

use Random\RandomException;

class NumberGenerator {

    protected $otp_length;
    public function __construct() {
        $this->otp_length = env('OTP_LENGTH');
    }

    /**
     * @throws RandomException
     */
    public function generateNumber(): string
    {
        return str_pad(random_int(1, 9999), $this->otp_length, '0', STR_PAD_LEFT);
    }
}
