<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TaskBoardService;
use Illuminate\Support\Facades\Hash;
use App\Traits\ApiResponse;



class TaskBoardController extends Controller
{
    use ApiResponse;
    protected $taskboardService;

    //defining the services
    public function __construct(TaskBoardService $taskboardService)
    {
        $this->taskboardService = $taskboardService;
    }

    public function createTaskBoard(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'title' => 'required|string|max:50', // Ensure the title is correct
                
            ]);

            $request = $request->all();
            // Call the service with validated data
            $createtaskboard = $this->taskboardService->createTaskBoard($request,auth()->user()->id);

            if ($createtaskboard['status'] == true) {
                return $this->successResponse([
                    "message" => $createtaskboard['message'],
                    "data" => $createtaskboard['data'],
                ]);
            } else {
                return $this->failResponse([
                    "message" => $createtaskboard['message'],
                ], 200);
            }
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation_failed'),
                'errors' => $e->errors()
            ], 422);

        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return response()->json([
                'status' => false,
                'message' => trans('messages.something_went_wrong')
            ], 500);
        }
    }

    public function getTaskBoard(Request $request)
    {
        try {
            $getdata = $this->taskboardService->getTaskBoard($request, auth()->user()->id);
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

    public function findBoardById($board_id, Request $request)
    {
        try {

            $getdata = $this->taskboardService->findBoardById($board_id, $request->all(),auth()->user()->id);
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
    public function updateBoard($board_id, Request $request)
    {
        try {

            // Validate the request
            $validatedData = $request->validate([
                'title' => 'required|string|max:50', // Ensure the title is correct
                
            ]);
            $getdata = $this->taskboardService->updateBoard($board_id, $request->all(),auth()->user()->id);
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

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation failed: ' . json_encode($e->errors()));
            return response()->json([
                'status' => false,
                'message' => trans('messages.validation_failed'),
                'errors' => $e->errors()
            ], 422);

        }catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
        }
    }

    public function deleteBoard($board_id, Request $request)
    {
        try {

            $getdata = $this->taskboardService->deleteBoard($board_id, $request->all(),auth()->user()->id);
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