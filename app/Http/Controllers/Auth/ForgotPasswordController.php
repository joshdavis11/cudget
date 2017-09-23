<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Model\UserToken;
use App\Services\UserService;
use App\Services\UserTokenService;
use DateTimeImmutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Password;

/**
 * Class ForgotPasswordController
 *
 * @package App\Http\Controllers\Auth
 */
class ForgotPasswordController extends Controller {
	/*
	|--------------------------------------------------------------------------
	| Password Reset Controller
	|--------------------------------------------------------------------------
	|
	| This controller is responsible for handling password reset emails and
	| includes a trait which assists in sending these notifications from
	| your application to your users. Feel free to explore this trait.
	|
	*/

	use SendsPasswordResetEmails;

	/**
	 * Send a reset link to the given user.
	 *
	 * @param Request $request
	 *
	 * @return Response
	 * @throws ModelNotFoundException
	 */
	public function sendResetLinkEmail(Request $request, UserService $UserService, UserTokenService $UserTokenService) {
		$this->validate($request, ['email' => 'required|email']);

		// We will send the password reset link to this user. Once we have attempted
		// to send the link, we will examine the response then see the message we
		// need to show to the user. Finally, we'll send out a proper response.
		$this->sendResetLink($UserService, $UserTokenService, $request->get('email'));

		return new Response('Sent');
	}

	/**
	 * Send a password reset link to a user.
	 *
	 * @param UserService      $UserService
	 * @param UserTokenService $UserTokenService
	 * @param string           $email
	 *
	 * @return void
	 * @throws ModelNotFoundException
	 */
	private function sendResetLink(UserService $UserService, UserTokenService $UserTokenService, string $email) {
		$User = $UserService->getUserByEmail($email);

		//Create new token
		$UserToken = $UserTokenService->createToken($User->id, UserToken::TYPE_PASSWORD_RESET, new DateTimeImmutable('+1 hour'));

		// Once we have the reset token, we are ready to send the message out to this
		// user with a link to reset their password. We will then redirect back to
		// the current URI having nothing set in the session to indicate errors.
		$User->sendPasswordResetNotification($UserToken->token);
	}
}
