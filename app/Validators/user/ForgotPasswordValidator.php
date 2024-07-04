<?php

namespace App\Validators\User;
use App\Validators\Validator;

class ForgotPasswordValidator extends Validator
{
    /**
     * Rules for User registration
     *
     * @var array
     */
    protected $rules = [
        'email' => 'required|email|exists:users',
    ];

}
