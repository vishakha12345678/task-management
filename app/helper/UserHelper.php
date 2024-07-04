<?php


use App\Models\User;
use Illuminate\Http\Request;


if (!function_exists('checkUserEmail')) {
	function checkUserEmail($email){
		$checkemail=User::where('email',$email)->where('login_type','email')->first();
        if($checkemail){
            return $checkemail;
       }else{
            return 0;
       }
	}
}

if (!function_exists('GetUserProfile')) {
    function GetUserProfile($userId)
    {
        return User::where('id', $userId)->first();
    }
}