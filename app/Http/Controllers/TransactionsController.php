<?php namespace App\Http\Controllers;
	
use Users;
use GuzzleHttp\Client;
use Request;
use Result;
use Profiles;

class TransactionsController extends Controller
{
	public function create()
	{
		$userFrom = Users::getByToken(Request::header('Authorization'));
		
		if (!$userFrom->error) {
			
			$userTo = Users::getByEmailOrUEN(Request::input('tokenTo'));
			
			if (!isset($userTo->error) || $userTo->error) {
				return Result::build()
						->setError(true)
						->setData([
							'errors'	=> [
								'tokenTo'	=> Profiles::USER_NOT_FOUND
							]
						])->asJson();
			}
			
			if ($userFrom->WalletToken == $userTo->WalletToken) {
				return Result::build()
						->setError(true)
						->setData([
							'errors'	=> [
								'tokenTo'	=> 'You can not send money yourself'
							]
						])->asJson();
			}
			
			$client = new \GuzzleHttp\Client(['base_uri' => env('WALLET_URL')]);
		    $response = $client->request('POST', '/transaction/create', [
			    'form_params'	=> [
			    	'wallet_from'	=> $userFrom->WalletToken,
			    	'wallet_to' 	=> $userTo->WalletToken,
			    	'amount' 		=> Request::input('amount'),
			    	'purpose' 		=> Request::input('purpose'),
			    	'description' 	=> Request::input('description')
			    ]
		    ]);
		    $res = json_decode($response->getBody());
		    
		    return Result::build()
						->setError($res->error)
						->setData([
							'errors'	=> $res->errors
						])->asJson();
		    
		} else {
			return Result::build()
					->setError(true)
					->setData([
						'message'	=> Profiles::USER_NOT_FOUND
					])->asJson();
		}
	}
	
	public function topup()
	{
		$user = Users::getByToken(Request::header('Authorization'));
		
		if (!$user->error) {
			
			$client = new \GuzzleHttp\Client(['base_uri' => env('WALLET_URL')]);
		    $response = $client->request('POST', '/transaction/topup', [
			    'form_params'	=> [
				    'token'			=> $user->WalletToken,
			    	'card_number'	=> Request::input('card_number'),
			    	'card_expire' 	=> Request::input('card_expire'),
			    	'card_cvc' 		=> Request::input('card_cvc'),
			    	'amount' 		=> Request::input('amount')
			    ]
		    ]);
		    $res = json_decode($response->getBody());
		    
		    return Result::build()
						->setError($res->error)
						->setData([
							'message'	=> $res->message
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