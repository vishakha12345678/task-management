<?php
namespace App\Services;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Token;
use Laravel\Passport\RefreshToken;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use App\Mail\SendCodeResetPassword;
use App\Models\ResetCodePassword;
use Auth;



class AuthService
{
    public function signup($data)
    {
        try {
            $user = User::create([
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
                'device_type'=> $data['device_type'] ?? '',
                'device_token' => $data['device_token'] ?? '',
                'name' => $data['name'],
               
            ]);
    
            $UserData = GetUserProfile($user->id);
            $datas['userdata'] = $UserData;    
            return ['status' => true, 'message' => trans('messages.register'), 'data' => $datas];
        }catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return ['status' => false, 'message' =>  trans('messages.something_went_wrong')];
        }
    }

    public function signin($data)
    {
        try {
            $checkEmail = User::where('email', $data['email'])->first();
            if (isset($checkEmail)) {
                if (Hash::check($data['password'], $checkEmail->password)) {
                    User::where('id', $checkEmail->id)->update(["device_type" => $data['device_type'], "device_token" => $data['device_token'] ?? $checkEmail->device_token,'login_type' => 'email']);
                    $UserData = GetUserProfile($checkEmail->id);
                    $datas['userdata'] = $UserData;
                    $datas['userdata']['token'] = $datas['userdata']->createToken('MyApp')->accessToken;
                   
                    return ['status' => true, 'message' =>  trans('messages.login_success'), 'data' => $datas];
                } else {
                    return ['status' => false, 'message' => trans('messages.invalid_credentials')];
                } 
            } else {
                return ['status' => false, 'message' => trans('messages.invalid_credentials')];
            }
        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return ['status' => false, 'message' =>  trans('messages.something_went_wrong')];
        }
    }

    public function forgotPassword($data)
    {
        try {

            // Delete all old code that user send before.
            ResetCodePassword::where('email', $data['email'])->delete();
            // Generating random code
            $data['code'] = mt_rand(100000, 999999);
            // Creating a new code
            $codeData = ResetCodePassword::create($data);
            // Send email to user
            Mail::to($data['email'])->send(new SendCodeResetPassword($codeData->code));

            return ['status' => true, 'message' =>  trans('messages.forgot_pass_email_msg'), 'data' => null];
            
        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function validateCode($data)
    {
        try {

            // find the code
            $passwordReset = ResetCodePassword::firstWhere('code', $data['code']);

            // check if code is expired: the time is one hour
            if ($passwordReset->created_at->addHour() < now()) {
                return ['status' => false, 'message' => trans('messages.code_is_expire')];
            }

           
            return ['status' => true, 'message' => trans('messages.code_is_valid'),'data' => []];
            
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function updatePassword($data)
    {
        try {

            \Log::info("Dd");

             // find the code
            $passwordReset = ResetCodePassword::firstWhere('code', $data['code']);

            // check if code is expired: the time is one hour
            if ($passwordReset->created_at->addHour() < now()) {
                return ['status' => false, 'message' => trans('messages.code_is_expire')];
            }

            // find user's email 
            $user = User::firstWhere('email', $passwordReset->email);

            // update user password
            $user->update(['password' => Hash::make($data['password'])]);

            return ['status' => true, 'message' =>  trans('messages.password_updated')];
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function getMyProfile($data, $userID)
    {
        try {
            $datas['userdata'] = GetUserProfile($userID);
            return ['status' => true, 'message' => trans('messages.user_profile_data'), 'data' => $datas];
        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function updateUserProfile($data, $userId)
    {
        $updatedUser = [];
        try {

            $updateData = [
                'name' => $data['name'],
                'mobile' => $data['mobile'],
                'address' => $data['address'],
            ];

            if (isset($data['profile_image']) && !empty($data['profile_image'])) {

                $file = $data['profile_image'];
                $filename1 = $file->getClientOriginalName();

                $file->move(public_path().'/upload/profile/',$filename1);

                $filePath = url('/').'/upload/profile/' .$filename1;
                $updateData['profile_image'] = $filePath;
            }
            User::find($userId)->update($updateData);

            $updatedUser['userdata'] = GetUserProfile($userId);

            return [
                'status' => true,
                'data' => $updatedUser,
                'message' => trans('messages.profile_update'),
            ];
        } catch (\Throwable $th) {
            \Log::error($th->getMessage() . " " . $th->getFile() . " " . $th->getLine());
            return ['status' => false, 'message' => trans('messages.something_went_wrong')];
        }
    }

    public function UserAppLogout($data)
    {
        try {
            $getUserID = auth('api')->user()->id;
            $user = Auth::user()->token();
            $tokens =  $user->pluck('id');
            $token = Token::whereIn('id', $tokens)
                ->update(['revoked' => true]);
            $refreshtoken = RefreshToken::whereIn('access_token_id', $tokens)->update(['revoked' => true]);
            User::where('id', $getUserID)->update(['device_token' => Null]);
            return ['status' => true, 'message' => trans('messages.logged_out'), 'data' => null];
        } catch (\Throwable $e) {
            \Log::error($e->getMessage() . " " . $e->getFile() . " " . $e->getLine());
            return ['status' => false, 'message' =>  trans('messages.something_went_wrong')];
        }
    }

}
