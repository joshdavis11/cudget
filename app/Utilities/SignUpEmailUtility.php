<?php

namespace App\Utilities;

use App\Mail\Signup;
use App\Model\User;
use App\Model\UserToken;
use Illuminate\Mail\Mailer;
use Illuminate\Routing\UrlGenerator;

/**
 * Class SignupEmailUtility
 *
 * @package App\Utilities
 */
class SignUpEmailUtility {
	/**
	 * @var Mailer
	 */
	private $Mailer;
	/**
	 * @var UrlGenerator
	 */
	private $UrlGenerator;

	/**
	 * SignupEmailUtility constructor.
	 *
	 * @param Mailer       $Mailer
	 * @param UrlGenerator $UrlGenerator
	 */
	public function __construct(Mailer $Mailer, UrlGenerator $UrlGenerator) {
		$this->Mailer = $Mailer;
		$this->UrlGenerator = $UrlGenerator;
	}

	/**
	 * Send the email
	 *
	 * @param User      $User
	 * @param UserToken $UserToken
	 *
	 * @return void
	 */
	public function send(User $User, UserToken $UserToken) {
		$this->Mailer
			->to($User->email)
			->send(new Signup($UserToken, $this->UrlGenerator));
	}
}