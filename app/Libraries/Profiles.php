<?php namespace App\Libraries;
	
use Request;

use Illuminate\Database\Eloquent\Model as Model;

class Profiles extends Model
{
    protected $table = 'profiles';
    
    const CREATION_SUCCESS = 'Success';
    const USER_NOT_FOUND = 'User with this token not found';
}