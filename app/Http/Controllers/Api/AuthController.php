<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\AuthService;
use Illuminate\Support\Facades\Hash;
use App\Validators\user\RegisterValidator;
use App\Validators\user\LoginValidator;
use App\Validators\user\ForgotPasswordValidator;
use App\Validators\user\ProfileValidator;

use App\Traits\ApiResponse;



class AuthController extends Controller
{
    use ApiResponse;
    protected $service;

    //defining the services
    public function __construct(AuthService $service)
    {
        $this->service = $service;
    }


    public function signUp(Request $request,RegisterValidator $registerValidator)
    {
       
        try {
            $request = $request->all();

            if (!$registerValidator->with($request)->passes()) {
                return $this->failResponse([
                    "message" => $registerValidator->getErrors()[0],
                ], 200);
            }
            $checkEmail=checkUserEmail($request['email']);

            //checking if email already exist
            if($checkEmail){
                return $this->failResponse([
                        "message" => trans('messages.email_exsist'),
                        ], 200);
            }else{
                $getdata = $this->service->signup($request);
                if ($getdata['status'] == 1) {
                    return $this->successResponse([
                        "message" => $getdata['message'] ,
                        "data" => $getdata['data']
                       
                    ]);
                }
                
            }

           
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function SignIn(Request $request,LoginValidator $loginvalidator)
    {
        try {
            $request = $request->all();
            if (!$loginvalidator->with($request)->passes()) {
                return $this->failResponse([
                    "message" => $loginvalidator->getErrors()[0],
                ], 200);
            }
            $getdata = $this->service->signin($request);
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


    public function forgotPassword(Request $request,ForgotPasswordValidator $forgotpasswordvalidator)
    {
        try {
            $request = $request->all();
            if (!$forgotpasswordvalidator->with($request)->passes()) {
                return $this->failResponse([
                    "message" => $forgotpasswordvalidator->getErrors()[0],
                ], 200);
            }
            $getdata = $this->service->forgotPassword($request);
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

    public function validateCode(Request $request)
    {
        try {

            //checking if code is correct
            $validatedData = $request->validate([
                'code' => 'required|string|exists:password_reset',
            ]);
        
            $request = $request->all();
           
            $getdata = $this->service->validateCode($request);
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

        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
        }
    }

    public function updateUserPassword(Request $request)
    {
        try {
            // Validate the request
            $validatedData = $request->validate([
                'code' => 'required|string|exists:password_reset,code', // Ensure the table name is correct
                'password' => 'required|string|min:6|confirmed',
            ]);

            // Call the service with validated data
            $updatePassword = $this->service->updatePassword($validatedData);

            if ($updatePassword['status'] == true) {
                return $this->successResponse([
                    "message" => $updatePassword['message'],
                    "data" => []
                ]);
            } else {
                return $this->failResponse([
                    "message" => $updatePassword['message'],
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

    public function getMyProfile(Request $request)
    {
        try {
            $getdata = $this->service->getMyProfile($request, auth()->user()->id);
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

    public function updateUserProfile(Request $request,ProfileValidator $profilevalidator)
    {
        try {
            $request = $request->all();
            if (!$profilevalidator->with($request)->passes()) {
                return $this->failResponse([
                    "message" => $profilevalidator->getErrors()[0],
                ], 200);
            }

            $updateProfile = $this->service->updateUserProfile($request, auth()->user()->id);

            if ($updateProfile['status'] == true) {
                return $this->successResponse([
                    "message" => $updateProfile['message'] ,
                    "data" => $updateProfile['data']
                   
                ]);
            } else {
                return $this->failResponse([
                    "message" => $updateProfile['message'],
                    ], 200);
            }
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
        }
    }

    public function SignOut(Request $request)
    {
        try {
            $request = $request->all();
            $getdata = $this->service->UserAppLogout($request);
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
}
