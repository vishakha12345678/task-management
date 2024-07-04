<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TaskService;
use Illuminate\Support\Facades\Hash;
use App\Traits\ApiResponse;
use App\Validators\tasks\TaskValidator;



class TaskController extends Controller
{
    use ApiResponse;
    protected $taskservice;

    //defining the services
    public function __construct(TaskService $taskservice)
    {
        $this->taskservice = $taskservice;
    }

    public function createTask(Request $request,TaskValidator $taskvalidator)
    {
        try {
            $request = $request->all();

            if (!$taskvalidator->with($request)->passes()) {
                return $this->failResponse([
                    "message" => $taskvalidator->getErrors()[0],
                ], 200);
            }
            // Call the service with validated data
            $createtask= $this->taskservice->createTask($request,auth()->user()->id);

            if ($createtask['status'] == true) {
                return $this->successResponse([
                    "message" => $createtask['message'],
                    "data" => $createtask['data'],
                ]);
            } else {
                return $this->failResponse([
                    "message" => $createtask['message'],
                ], 200);
            }
        }catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return response()->json([
                'status' => false,
                'message' => trans('messages.something_went_wrong')
            ], 500);
        }
    }

    public function getAllTasks(Request $request)
    {
        try {
            $getdata = $this->taskservice->getAllTask($request, auth()->user()->id);
            if ($getdata['status'] == 1) {
                return $this->successResponse([
                    "message" => $getdata['message'] ,
                    "data" => $getdata['data']
                   
                ]);
            } else {
                return $this->failResponse([
                    "message" => $getdata['message'],
                    ], 200);
            }
            //code...
        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
        }
    }

    public function findTaskById($task_id, Request $request)
    {
        try {

            $getdata = $this->taskservice->findTaskById($task_id, $request->all(),auth()->user()->id);
            if ($getdata['status'] == 1) {
                return $this->successResponse([
                    "message" => $getdata['message'] ,
                    "data" => $getdata['data']
                   
                ]);
            } else {
                return $this->failResponse([
                    "message" => $getdata['message'],
                    ], 200);
            }

        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
        }
    }

    public function updateTask($task_id, Request $request,TaskValidator $taskvalidator)
    {
        try {

            $request = $request->all();

            if (!$taskvalidator->with($request)->passes()) {
                return $this->failResponse([
                    "message" => $taskvalidator->getErrors()[0],
                ], 200);
            }
            
            $getdata = $this->taskservice->updateTask($task_id, $request,auth()->user()->id);
            if ($getdata['status'] == 1) {
                return $this->successResponse([
                    "message" => $getdata['message'] ,
                    "data" => $getdata['data']
                   
                ]);
            } else {
                return $this->failResponse([
                    "message" => $getdata['message'],
                    ], 200);
            }

        }catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
        }
    }
    public function deleteTask($task_id, Request $request)
    {
        try {

            $getdata = $this->taskservice->deleteTask($task_id, $request->all(),auth()->user()->id);
            if ($getdata['status'] == 1) {
                return $this->successResponse([
                    "message" => $getdata['message'] ,
                    "data" => $getdata['data']
                   
                ]);
            } else {
                return $this->failResponse([
                    "message" => $getdata['message'],
                    ], 200);
            }
        }catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
        }
    }
   
}