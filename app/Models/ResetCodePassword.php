<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class ResetCodePassword extends Authenticatable
{
    
    public $table = 'password_reset';
    protected $guarded = ['id'];

   
}
