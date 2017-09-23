<?php

namespace App\Services;

use App\Model\UserToken;
use DateTimeImmutable;
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
	 * @param int                    $userId  The user's ID
	 * @param string                 $type    The type of token
	 * @param DateTimeImmutable|null $expires The DateTimeImmutable when the token expires. If null, a DateTimeImmutable of +30 minutes will be generated.
	 * @param string|null            $token   The token. If null, a random string will be generated and encrypted.
	 *
	 * @return UserToken
	 */
	public function createToken(int $userId, string $type, DateTimeImmutable $expires = null, string $token = null) {
		if (null === $expires) {
			$expires = new DateTimeImmutable('+30 minutes');
		}
		if (null === $token) {
			$token = str_random(64);
		}
		if (strlen($token) > 191) {
			$token = substr($token, 0, 191);
		}
		$UserToken = new UserToken();
		$UserToken->userId = $userId;
		$UserToken->token = $token;
		$UserToken->expires = $expires->format('Y-m-d H:i:s');
		$UserToken->type = $type;
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