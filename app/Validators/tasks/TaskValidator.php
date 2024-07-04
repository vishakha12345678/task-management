<?php

namespace App\Validators\tasks;
use App\Validators\Validator;

class TaskValidator extends Validator
{
    /**
     * Rules for task creation
     *
     * @var array
     */
    protected $rules = [
        'taskboard' => 'required',
        'assigned_to' => 'required',
        'project' => 'required|string|max:100',
        'summary' => 'required|string|max:500',
        'status' => 'required',
        'reporter' => 'required|string|max:50',
       
    ];

}
