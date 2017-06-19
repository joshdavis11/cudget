<?php

namespace App\Http\Utilities;

/**
 * Class Password
 *
 * @package App\Http\Utilities
 */
class Password {
	/**
	 * Encrypt a string
	 *
	 * @param string $pureString
	 * @param string $iv
	 * @param string $encryptionMethod
	 * @param string $encryptionKey
	 *
	 * @return string
	 */
	static public function encrypt($pureString, $iv, $encryptionMethod = null, $encryptionKey = null) {
		if (null === $encryptionMethod) {
			$encryptionMethod = env('ENCRYPTION_METHOD');
		}
		if (null === $encryptionKey) {
			$encryptionKey = env('ENCRYPTION_KEY');
		}
		return openssl_encrypt($pureString, $encryptionMethod, $encryptionKey, false, $iv);
	}

	/**
	 * Encrypt a string
	 *
	 * @param string $encryptedString
	 * @param string $iv
	 * @param string $encryptionMethod
	 * @param string $encryptionKey
	 *
	 * @return string
	 */
	static public function decrypt($encryptedString, $iv, $encryptionMethod = null, $encryptionKey = null) {
		if (null === $encryptionMethod) {
			$encryptionMethod = env('ENCRYPTION_METHOD');
		}
		if (null === $encryptionKey) {
			$encryptionKey = env('ENCRYPTION_KEY');
		}
		return openssl_decrypt($encryptedString, $encryptionMethod, $encryptionKey, false, $iv);
	}

	/**
	 * Get an IV string
	 *
	 * @return string
	 */
	static public function getIV() {
		return substr(bin2hex(openssl_random_pseudo_bytes(16)), 0, 16);
	}
}