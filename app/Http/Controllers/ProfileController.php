<?php namespace App\Http\Controllers;
	
use Profiles;
use Result;
use Users;
use Request;
use BankAccounts;

class ProfileController extends Controller
{
	public function registrationForm()
    {
	    $user = Users::getByToken(Request::header('Authorization'));
	    
	    if (!$user->error) {
		    $profile = new Profiles;
			$profile->UserID = $user->id;
			$profile->CoName = Request::input('CoName');
			$profile->CoUEN = Request::input('CoUEN');
			$profile->BlkHseNo = Request::input('BlkHseNo');
			$profile->StreetName = Request::input('StreetName');
			$profile->Storey = Request::input('Storey');
			$profile->UnitNo = Request::input('UnitNo');
			$profile->BuildingName = Request::input('BuildingName');
			$profile->PostalCode = Request::input('PostalCode');
			$profile->AuthorisedOfficer = Request::input('AuthorisedOfficer');
			$profile->ClientMobile = Request::input('ClientMobile');
			$profile->ForeignAddress1 = Request::input('ForeignAddress1');
			$profile->ForeignAddress2 = Request::input('ForeignAddress2');
			$profile->save();
			
			$account = new BankAccounts;
			$account->UserID = $user->id;
			$account->BankName = Request::input('BankName');
			$account->BranchName = Request::input('BranchName');
			$account->AccountNumber = Request::input('AccountNumber');
			$account->AccountHolderName = Request::input('AccountHolderName');
			$account->save();
			
			return Result::build()
					->setError(false)
					->setData([
						'message'	=> Profiles::CREATION_SUCCESS
					])->asJson();
		} else {
			return Result::build()
					->setError(true)
					->setData([
						'message'	=> Profiles::USER_NOT_FOUND
					])->asJson();
		}
    }
    
    public function profile()
    {
	    $user = Users::getByToken(Request::header('Authorization'));
	    
	    if (!$user->error) {
		    $profile = $user->getProfile();
	    }
	    
	    return Result::build()
				->setError($user->error)
				->setData([
					'message'	=> $user->message,
					'profile'	=> $user->profile
				])->asJson();
	    
    }
}