<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Users\SignUpUserRequest;
use App\Services\UserService;
use App\Services\UserTokenService;
use App\Utilities\SignUpEmailUtility;
use DateTimeImmutable;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

/**
 * Class PreLoginController
 *
 * @package App\Http\Controllers\Auth
 */
class PreLoginController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| Login Controller
	|--------------------------------------------------------------------------
	|
	| This controller handles authenticating users for the application and
	| redirecting them to your home screen. The controller uses a trait
	| to conveniently provide its functionality to your applications.
	|
	*/

	use AuthenticatesUsers {
		credentials as credentialsTrait;
	}
	/**
	 * Where to redirect users after login.
	 *
	 * @var string
	 */
	protected $redirectTo = '/home';

	/**
	 * Default username is email
	 *
	 * @var string
	 */
	protected $username = 'email';

	/**
	 * Create a new controller instance.
	 */
	public function __construct() {
		$this->middleware('guest')->except('logout');
	}

	/**
	 * Handle a login request to the application.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function postLogin(Request $request) {
		$this->username = filter_var($request->input('username'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
		$request->merge([$this->username => $request->input('username')]);

		return $this->login($request);
	}

	/**
	 * Get the login username to be used by the controller.
	 *
	 * @return string
	 */
	public function username() {
		return $this->username;
    }

	/**
	 * Get the needed authorization credentials from the request.
	 *
	 * @param  \Illuminate\Http\Request $request
	 *
	 * @return array
	 */
	protected function credentials(Request $request) {
		$data = $this->credentialsTrait($request);
		return array_add($data, 'email_verified', true);
	}

	/**
	 * Is the user authenticated?
	 *
	 * @return Response
	 */
    public function isAuthenticated(): Response {
		return new Response(['authenticated' => Auth::check()]);
	}

	/**
	 * Sign Up
	 *
	 * @param SignUpUserRequest  $Request
	 * @param UserService        $UserService
	 * @param UserTokenService   $UserTokenService
	 * @param SignUpEmailUtility $SignUpEmailUtility
	 *
	 * @return Response
	 */
	public function signup(SignUpUserRequest $Request, UserService $UserService, UserTokenService $UserTokenService, SignUpEmailUtility $SignUpEmailUtility): Response {
    	$Request->merge(['admin' => false]);
		$User = $UserService->createUser($Request);

		$UserToken = $UserTokenService->createToken($User->id, new DateTimeImmutable('+1 day'));

		//Send new email
		$SignUpEmailUtility->send($User, $UserToken);

		return new Response('Created', Response::HTTP_CREATED, ['Location' => '/api/v1/users/' . $User->id]);
	}
}
