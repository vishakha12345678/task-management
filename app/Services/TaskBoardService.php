<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;
use Laravel\Passport\RefreshToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Models\TaskBoard;

class TaskBoardService
{

    public function createTaskBoard($data,$userId)
    {
        try {

            //creating board 
            if($userId){
                $createboard = [
                    'title' => $data['title'],
                    'description' => $data['description'],
                    'user_id' => $userId,
                ];

                $board = TaskBoard::create($createboard);
                $UserData = GetUserProfile($userId);
                $datas = GetBoardData($board->id); 
                $datas->user_name =  $UserData->name;
            
    
                return [
                    'status' => true,
                    'data' => $datas,
                    'message' => trans('messages.task_board_created'),
                ];
            }else{
                return [
                    'status' => false,
                    'message' => trans('messages.user_not_found'),
                ];
            }
            
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }


    public function getTaskBoard($data, $userId)
    {
        try {
            if($userId){
                $datas['boarddata'] = TaskBoard::where('user_id',$userId)->paginate(10);
                return ['status' => true, 'message' => trans('messages.task_board_data'), 'data' => $datas];
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

    public function findBoardById($board_id, $data,$userID)
    {
        try {
            
            $datas['boarddata'] = GetBoardData($board_id); 
            if($datas['boarddata']){
                return ['status' => true, 'message' => trans('messages.task_board_data'), 'data' => $datas];
            }else{
                return ['status' => false, 'message' => trans('messages.board_not_Found'), 'data' => $datas];
            }

        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function updateBoard($board_id,$data, $userId)
    {
        try {

            //checking if board exist
            $datas = GetBoardData($board_id); 
            if($datas){
                $updateData = [
                    'title' => $data['title'],
                    'description' => $data['description'],
                ];
    
                //update board
                TaskBoard::find($board_id)->update($updateData);
                $UserData = GetUserProfile($userId);
                $datas = GetBoardData($board_id); 
                $datas->user_name =  $UserData->name;
    
                return [
                    'status' => true,
                    'data' => $datas,
                    'message' => trans('messages.board_updated'),
                ];
            }else{
                return ['status' => false, 'message' => trans('messages.board_not_Found'), 'data' => $datas];
            }
           
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function deleteBoard($board_id,$data, $userId)
    {
        try {


            $datas = GetBoardData($board_id); 
            if($datas){
                //delete board
                TaskBoard::find($board_id)->delete();
                return [
                    'status' => true,
                    'data' => [],
                    'message' => trans('messages.board_deleted'),
                ];
            }else{
                return ['status' => false, 'message' => trans('messages.board_not_Found'), 'data' => []];
            }
           
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }
}