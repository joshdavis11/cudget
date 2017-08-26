<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\UserService;
use App\Services\UserTokenService;
use App\Utilities\SignUpEmailUtility;
use DateTimeImmutable;
use Illuminate\Http\Request;

/**
 * Class ActivationController
 *
 * @package App\Http\Controllers\Auth
 */
class ActivationController extends Controller {
	/**
	 * Activate an email address
	 *
	 * @param UserService $UserService
	 * @param string      $token
	 *
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function activate(UserService $UserService, UserTokenService $UserTokenService, string $token) {
		$UserToken = $UserTokenService->getByToken($token);

		if ($UserToken->expires < date('Y-m-d H:i:s')) {
			return view('expired', ['token' => $token]);
		}

		$UserToken->delete();
		$User = $UserService->getUser($UserToken->userId);
		$User->emailVerified = true;
		$User->save();

		return view('activated');
	}

	/**
	 * Get a new token
	 *
	 * @param string             $token
	 * @param UserService        $UserService
	 * @param UserTokenService   $UserTokenService
	 * @param SignUpEmailUtility $SignUpEmailUtility
	 * @param Request            $Request
	 *
	 * @return \Illuminate\Http\RedirectResponse
	 */
	public function new(string $token, UserService $UserService, UserTokenService $UserTokenService, SignUpEmailUtility $SignUpEmailUtility, Request $Request) {
		$UserToken = $UserTokenService->getByToken($token);
		$User = $UserService->getUser($UserToken->userId);
		$UserToken->delete();

		//Create new token
		$UserToken = $UserTokenService->createToken($User->id, new DateTimeImmutable('+1 day'));

		//Send new email
		$SignUpEmailUtility->send($User, $UserToken);

		//Set flash info
		$Request->session()->flash('info', trans('auth.emailVerification'));

		return redirect()->route('login');
	}
}