<?php

namespace App\Validators\User;
use App\Validators\Validator;

class RegisterValidator extends Validator
{
    /**
     * Rules for User registration
     *
     * @var array
     */
    protected $rules = [
        'email' => 'required|string|email|max:100',
        'password' => 'required|string|min:8',
       
    ];

}
