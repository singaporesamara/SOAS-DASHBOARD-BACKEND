<?php namespace App\Http\Controllers;
	
use Result;
use Users;
use Request;

class AuthController extends Controller
{
	public function signup()
    {
	    $email = Request::input('email');
	    $password = Request::input('password');
	    
	    $user = new Users;
	    $user->add($email, $password);
	    
		return Result::build()
				->setError($user->error)
				->setData([
					'message'	=> $user->message,
					'token'		=> $user->token
				])->asJson();
    }
    
    public function signin()
    {
	    $email = Request::input('email');
	    $password = Request::input('password');
	    
	    $user = new Users;
	    $user->login($email, $password);
	    
		return Result::build()
				->setError($user->error)
				->setData([
					'message'	=> $user->message,
					'token'		=> $user->token
				])->asJson();
    }
    
    public function forgot()
    {
	    $email = Request::input('email');
	    
	    $user = Users::where([
		    		'Email'	=> $email
	    		])->first();
	    		
	    if (!$user) {
		    return Result::build()
					->setError(true)
					->setData([
						'message'	=> Users::AUTH_ERROR_FORGOT_ERROR
					])->asJson();
	    }
	    
	    return Result::build()
					->setError(false)
					->setData([
						'message'	=> Users::AUTH_SUCCESS,
						'checkword'	=> $user->generateCheckword($email)
					])->asJson();
    }
    
    public function setPassword()
    {
	    $password = Request::input('password');
	    $checkword = Request::input('checkword');
	    
	    $user = Users::where([
		    		'checkword'	=> $checkword
	    		])->first();
	    		
	    if (!$user) {
		    return Result::build()
					->setError(true)
					->setData([
						'message'	=> Users::AUTH_ERROR_FORGOT_APPLY
					])->asJson();
	    } else {
		    $user->setNewPassword($password);
		    
			return Result::build()
					->setError(false)
					->setData([
						'message'	=> Users::AUTH_SUCCESS
					])->asJson();   
	    }
    }
}