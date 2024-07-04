<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TaskBoardController;
use App\Http\Controllers\Api\TaskController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {

    //user routes
    Route::post('/signup', [AuthController::class, 'signUp']);
    Route::post('/signin', [AuthController::class, 'SignIn']);
    Route::post('/forgotpassword', [AuthController::class, 'forgotPassword']);
    Route::post('validatecode', [AuthController::class, 'validateCode']);
    Route::post('update-user-password', [AuthController::class, 'updateUserPassword']);

    Route::middleware('auth:api')->group(function () {

        //task board routes
        Route::get('/getmyprofile', [AuthController::class, 'getMyProfile']);
        Route::post('update-user-profile', [AuthController::class, 'updateUserProfile']);

        Route::post('createtaskboard', [TaskBoardController::class, 'createTaskBoard']);
        Route::get('/get-taskboard', [TaskBoardController::class, 'getTaskBoard']);

        Route::get('find-board-by-id/{board_id}', [TaskBoardController::class, 'findBoardById']);
        Route::put('update-board/{board_id}', [TaskBoardController::class, 'updateBoard']);
        Route::delete('delete-board/{board_id}',[TaskBoardController::class, 'deleteBoard']);

        //tasks routes
        Route::post('create-tasks', [TaskController::class, 'createTask']);
        Route::get('/get-all-tasks', [TaskController::class, 'getAllTasks']);
        Route::get('find-task-by-id/{task_id}', [TaskController::class, 'findTaskById']);

        Route::delete('delete-task/{task_id}',[TaskController::class, 'deleteTask']);
        Route::put('update-task/{task_id}', [TaskController::class, 'updateTask']);

        Route::get('/sign-out', [AuthController::class, 'SignOut']);


    });
    


  
});

