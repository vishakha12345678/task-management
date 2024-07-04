<?php


use App\Models\TaskBoard;
use App\Models\Task;

use Illuminate\Http\Request;



if (!function_exists('GetBoardData')) {
    function GetBoardData($boardid)
    {
        return TaskBoard::where('id', $boardid)->first();
    }
}

if (!function_exists('GetTaskData')) {
    function GetTaskData($taskid)
    {
        return Task::where('id', $taskid)->first();
    }
}