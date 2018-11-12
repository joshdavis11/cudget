<?php

namespace App\Http\Responses;

/**
 * Class PlaidAccessResponse
 *
 * @package App\Http\Responses
 */
class PlaidAccessResponse {
	/**
	 * @var string
	 */
	private $accessToken;
	/**
	 * @var string
	 */
	private $itemId;

	/**
	 * PlaidAccessResponse constructor.
	 *
	 * @param string $accessToken
	 * @param string $itemId
	 */
	public function __construct(string $accessToken, string $itemId) {
		$this->accessToken = $accessToken;
		$this->itemId = $itemId;
	}

	/**
	 * @return string
	 */
	public function getAccessToken(): string {
		return $this->accessToken;
	}

	/**
	 * @return string
	 */
	public function getItemId(): string {
		return $this->itemId;
	}
}