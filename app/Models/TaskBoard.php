<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class TaskBoard extends Authenticatable
{
    
    public $table = 'task_boards';
    protected $guarded = ['id'];

    public function tasks()
    {
        return $this->hasMany(Task::class, 'taskboard');
    }
   
}
