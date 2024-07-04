<?php

namespace App\Validators\User;
use App\Validators\Validator;

class ProfileValidator extends Validator
{
    /**
     * Rules for User registration
     *
     * @var array
     */
    protected $rules = [
        'profile' => 'nullable|image|mimes:jpeg,bmp,png,jpg,svg',
        'mobile' => 'nullable|min:3|max:15',
        'name'=>'nullable|string|min:4|max:20'
       
    ];

}
