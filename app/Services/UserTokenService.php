<?php

namespace App\Services;

use App\Model\UserToken;
use DateTime;
use Illuminate\Support\Facades\Hash;

/**
 * Class UserTokenService
 *
 * @package App\Services
 */
class UserTokenService {
	/**
	 * createToken
	 *
	 * @param int           $userId  The user's ID
	 * @param string|null   $token   The token. If null, a random string will be generated and encrypted.
	 * @param DateTime|null $expires The DateTime when the token expires. If null, a DateTime of +30 minutes will be generated.
	 *
	 * @return UserToken
	 */
	public function createToken(int $userId, DateTime $expires = null, string $token = null) {
		if (null === $expires) {
			$expires = new DateTime('+30 minutes');
		}
		if (null === $token) {
			$token = Hash::make(str_random(16));
		}

		$UserToken = new UserToken();
		$UserToken->userId = $userId;
		$UserToken->token = $token;
		$UserToken->expires = $expires->format('Y-m-d H:i:s');
		$UserToken->save();

		return $UserToken;
	}

	/**
	 * Get a UserToken by the user's ID and the token
	 *
	 * @param string $token
	 *
	 * @return UserToken
	 */
	public function getByToken(string $token) {
		return UserToken::where('token', '=', $token)->firstOrFail();
	}
}