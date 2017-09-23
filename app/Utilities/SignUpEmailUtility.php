<?php

namespace App\Utilities;

use App\Model\User;
use App\Model\UserToken;
use App\Notifications\SignUp as SignUpNotification;

/**
 * Class SignupEmailUtility
 *
 * @package App\Utilities
 */
class SignUpEmailUtility {
	/**
	 * Send the email
	 *
	 * @param User      $User
	 * @param UserToken $UserToken
	 *
	 * @return void
	 */
	public function send(User $User, UserToken $UserToken) {
		$User->notify(new SignUpNotification($UserToken));
	}
}