<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;
use Laravel\Passport\RefreshToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\Task;
use App\Models\TaskBoard;


class TaskService
{

    public function createTask($data,$userId)
    {
        try {

            //condition to check if taskboard and assigned user exist or not
            $checktaskboard = GetBoardData($data['taskboard']);
            if(!$checktaskboard){
                return [
                    'status' => false,
                    'data' => [],
                    'message' => trans('messages.board_not_Found'),
                ];
            }
            $checkassigned_to =  GetUserProfile($data['assigned_to']);
            if(!$checkassigned_to){
                return [
                    'status' => false,
                    'data' => [],
                    'message' => trans('messages.user_not_found'),
                ];
            }
            $createtask = [
                'taskboard' => $data['taskboard'],
                'assigned_to' => $data['assigned_to'],
                'project' =>  $data['project'],
                'summary' =>  $data['summary'],
                'description' =>  $data['description'],
                'label' =>  $data['label'],
                'status' =>  $data['status'],
                'reporter' =>  $data['reporter'],
            ];

            $task = Task::create($createtask);
            $datas = GetTaskData($task->id); 
            
            return [
                'status' => true,
                'data' => $datas,
                'message' => trans('messages.task_created'),
            ];
            
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function getAllTask($data, $userId)
    {
        try {
            //showing tasks list
            if($userId){
                $taskboardIds = TaskBoard::where('user_id', $userId)->pluck('id');

                $tasks = Task::whereIn('taskboard', $taskboardIds)->paginate(10);
                
                $datas['taskdata'] = $tasks;
                return ['status' => true, 'message' => trans('messages.task_fetch'), 'data' => $datas];
            }else{
                return [
                    'status' => false,
                    'message' => trans('messages.user_not_found'),
                ];
            }
            
        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function findTaskById($task_id, $data,$userID)
    {
        try {
            
            $datas['taskdata'] = GetTaskData($task_id); 
            if($datas['taskdata']){
                return ['status' => true, 'message' => trans('messages.task_fetch'), 'data' => $datas];
            }else{
                return ['status' => false, 'message' => trans('messages.task_not_Found'), 'data' => $datas];
            }

        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function updateTask($task_id,$data, $userId)
    {
        try {

            //checking if task exist
            $datas = GetTaskData($task_id); 
            if($datas){
                $updateData = [
                    'taskboard' => $data['taskboard'],
                    'assigned_to' => $data['assigned_to'],
                    'project' =>  $data['project'],
                    'summary' =>  $data['summary'],
                    'description' =>  $data['description'],
                    'label' =>  $data['label'],
                    'status' =>  $data['status'],
                    'reporter' =>  $data['reporter'],
                ];
    
                //updating task
                Task::find($task_id)->update($updateData);
                $datas = GetTaskData($task_id); 
            
                return [
                    'status' => true,
                    'data' => $datas,
                    'message' => trans('messages.task_updated'),
                ];
            }else{
                return ['status' => false, 'message' => trans('messages.task_not_Found'), 'data' => $datas];
            }
           
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function deleteTask($task_id,$data, $userId)
    {
        try {

              //checking if task exist
            $datas = GetTaskData($task_id); 
            if($datas){
                //delete task
                Task::find($task_id)->delete();
                return [
                    'status' => true,
                    'data' => [],
                    'message' => trans('messages.task_deleted'),
                ];
            }else{
                return ['status' => false, 'message' => trans('messages.task_not_Found'), 'data' => []];
            }
           
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }
}