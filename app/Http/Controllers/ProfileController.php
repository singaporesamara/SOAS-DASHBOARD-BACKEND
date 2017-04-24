<?php namespace App\Http\Controllers;
	
use Profiles;
use Result;
use Users;
use Request;
use BankAccounts;
use GuzzleHttp\Client;

class ProfileController extends Controller
{
	public function registrationForm()
    {
	    $user = Users::getByToken(Request::header('Authorization'));
	    
	    if (!$user->error) {
		    $client = new \GuzzleHttp\Client(['base_uri' => env('WALLET_URL')]);
		    $response = $client->request('POST', '/wallet/create');
		    
		    $user->WalletToken = $response->getBody();
		    $user->save();
		    
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
	    
	    $balance = [];
	    if (!$user->error) {
		    $profile = $user->getProfile();
		    $balance = $user->getBalance();
	    }
	    
	    return Result::build()
				->setError($user->error)
				->setData([
					'message'	=> $user->message,
					'profile'	=> [
						"id"				=> $user->profile->id,
					    "UserID"			=> $user->profile->UserID,
					    "CoName"			=> $user->profile->CoName,
					    "CoUEN"				=> $user->profile->CoUEN,
						"BlkHseNo"			=> $user->profile->BlkHseNo,
					    "StreetName"		=> $user->profile->StreetName,
					    "Storey"			=> $user->profile->Storey,
						"UnitNo"			=> $user->profile->UnitNo,
					    "BuildingName"		=> $user->profile->BuildingName,
					    "PostalCode"		=> $user->profile->PostalCode,
						"AuthorisedOfficer"	=> $user->profile->AuthorisedOfficer,
					    "ClientMobile"		=> $user->profile->ClientMobile,
					    "ClientEmail"		=> $user->profile->ClientEmail,
						"ForeignAddress1"	=> $user->profile->ForeignAddress1,
					    "ForeignAddress2"	=> $user->profile->ForeignAddress2,
					    "CreatedBy"			=> $user->profile->CreatedBy,
						"created_at"		=> (string)$user->profile->created_at,
					    "UpdatedBy"			=> $user->profile->UpdatedBy,
					    "updated_at"		=> (string)$user->profile->updated_at,
						"Remarks"			=> $user->profile->Remarks,
					    "Email"				=> $user->profile->Email,
					    "Registered"		=> $user->profile->Registered,
					    "balance"			=> [
						    "account"		=> $balance['account'],
						    "eWallet"		=> $balance['eWallet']
					    ]
					]
				])->asJson();
	    
    }
    
    public function news()
    {
	    $user = Users::getByToken(Request::header('Authorization'));
	    
	    if (!$user->error) {
		    
		    return Result::build()
					->setError(false)
					->setData([
						'news'	=> $user->getNews()
					])->asJson();
		    
		} else {
			return Result::build()
					->setError(true)
					->setData([
						'message'	=> Profiles::USER_NOT_FOUND
					])->asJson();
		}
    }
    
    public function check()
    {
	    $user = Users::getByToken(Request::header('Authorization'));
	    
	    if (!$user->error) {
		    
		    $str = Request::input('search');
		    
		    $res = Users::where('users.Email', 'like', '%'.$str.'%')->first();
		    		
		    if (!$res) {
			    
			    $res = Profiles::where('profiles.CoUEN', 'like', '%'.$str.'%')
			    		->leftJoin('users', 'users.id', '=', 'profiles.UserID')
						->select(
							'users.token as token'
						)->first();
						
				if (!$res) {
					return Result::build()
						->setError(true)
						->setData([
							'message'	=> Users::USER_NOT_FOUND
						])->asJson();
				}
		    }
		    
		    return Result::build()
					->setError(false)
					->setData([
						'token'	=> $res->token
					])->asJson();
		    
		} else {
			return Result::build()
					->setError(true)
					->setData([
						'message'	=> Profiles::USER_NOT_FOUND
					])->asJson();
		}
	}
}