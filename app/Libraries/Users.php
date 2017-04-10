<?php namespace App\Libraries;

use Illuminate\Database\Eloquent\Model as Model;

use Mail;

class Users extends Model
{
    protected $table = 'users';
    
    var $error;
    var $message;
    var $profile;
    
    const AUTH_ERROR_EMAIL_EXIST = 'Sorry, this email has already been registered in the system';
    const AUTH_ERROR_FIELD_EMPTY = 'Sorry, email or password should not be empty';
    
    const AUTH_SUCCESS = 'Success';
    
    const AUTH_ERROR_LOGIN_FAIL = 'Incorrect email and password';
    
    const AUTH_ERROR_FORGOT_ERROR = 'User with this email not found';
    const AUTH_ERROR_FORGOT_APPLY = 'User with this email and checkword not found';
    
    const TOKEN_MUST_FILL = 'Token should not be empty';
    const TOKEN_NOT_FOUND = 'User with this token not found';
    
    public function __construct()
    {
        $this->error = false;
        $this->message = '';
        $this->profile = [
	        'Registered'	=> false
        ];
    }
    
    public function add($email, $password)
    {
	    if (strlen($email) == 0 || strlen($password) == 0) {
		    $this->error = true;
		    $this->message = static::AUTH_ERROR_FIELD_EMPTY;
		    return $this;
	    }
	    
	    $user = static::where(['Email'	=> $email])->first();
	    if ($user) {
		    $this->error = true;
		    $this->message = static::AUTH_ERROR_EMAIL_EXIST;
		    return $this;
	    } 
	    
	    $this->Email = $email;
	    $this->Password = md5($password);
	    $this->Designation = 'Director';
	    $this->Status = 'Active';
	    $this->token = md5(rand(100000, 999999));
	    $this->save();
	    
	    $this->message = static::AUTH_SUCCESS;
	    
	    return $this;
    }
    
    public function login($email, $password)
    {
	    if (strlen($email) == 0 || strlen($password) == 0) {
		    $this->error = true;
		    $this->message = static::AUTH_ERROR_FIELD_EMPTY;
		    return $this;
	    }
	    
	    $user = static::where([
	    			'Email' 	=> $email,
	    			'Password'	=> md5($password)
	    		])->first();
	    		
	    if (!$user) {
		    $this->error = true;
		    $this->message = static::AUTH_ERROR_LOGIN_FAIL;
		    return $this;
	    }
	    
	    $this->message = static::AUTH_SUCCESS;
	    $this->token = $user->token;
	    
	    return $this;
    }
    
    public function generateCheckword($email)
    {
	    $this->checkword = md5(time());
	    $this->save();
	    
	    Mail::send('emails.forgotPassword', ['checkword' => $this->checkword], function ($message) use ($email) {
	        $message->to($email)->subject('Set new password');;
	    });
	    
	    return $this->checkword;
    }
    
    public function setNewPassword($password)
    {
	    $this->checkword = '';
	    $this->Password = md5($password);
	    $this->token = md5(rand(100000, 999999));
	    $this->save();
    }
    
    public static function getByToken($token)
    {
	    $user = new static;
	    
	    if (!empty($token)) {
		    $user_1 = static::where([
	    				'token'	=> $token
					])->first();
			if ($user_1) {
				$user = $user_1;
				$user->error = false;
				$user->message = static::AUTH_SUCCESS;
			} else {
				$user->error = true;
				$user->message = static::TOKEN_NOT_FOUND;
			}
	    } else {
		    $user->error = true;
		    $user->message = static::TOKEN_MUST_FILL;
	    }
	    
	    return $user;
    }
    
    public function getProfile()
    {
	    $this->profile = Profiles::where([
		    				'UserID'	=> $this->id
	    				])->first();
	    if ($this->profile) {
	    	$this->profile->Email = $this->Email;
	    	$this->profile->Registered = true;
	    } else {
		    $this->profile = [
			    'Email'			=> $this->Email,
			    'Registered'	=> false
		    ];
	    }
	    				
	    return $this;
    }
}