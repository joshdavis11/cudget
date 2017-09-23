<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserTokenService;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;

class ResetPasswordController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset requests
	| and uses a simple trait to include this behavior. You're free to
	| explore this trait and override any methods you wish to tweak.
	|
	*/

	use ResetsPasswords;
	/**
	 * Where to redirect users after resetting their password.
	 *
	 * @var string
	 */
	protected $redirectTo = '/home';

	/**
	 * Display the password reset view for the given token.
	 *
	 * If no token is present, display the link request form.
	 *
	 * @param Request         $request
	 * @param string          $token
	 * @param UserTokenService $UserTokenService
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function showResetPasswordForm(Request $request, string $token, UserTokenService $UserTokenService) {
		$UserToken = $UserTokenService->getByToken($token);
		return view('passwords.reset')->with([
			'token' => $token,
			'email' => $UserToken->user()->getResults()->email,
			'year' => date('Y'),
			'csrf' => csrf_token(),
		]);
	}

	/**
	 * Reset the given user's password.
	 *
	 * @param Request          $request
	 * @param UserTokenService $UserTokenService
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function reset(Request $request, UserTokenService $UserTokenService) {
		$this->validate($request, $this->rules(), $this->validationErrorMessages());
		$UserToken = $UserTokenService->getByToken($request->get('token'));
		$this->resetPassword($UserToken->user()->getResults(), $request->get('password'));

		return $this->sendResetResponse(Password::PASSWORD_RESET);
	}
}
