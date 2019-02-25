<?php

namespace App\Exceptions;

use Exception;
use JsonSerializable;
use Throwable;

/**
 * Class PlaidRequestException
 *
 * @package App\Exceptions
 */
class PlaidRequestException extends Exception implements JsonSerializable {
	/**
	 * @var string|null
	 */
	private $displayMessage;
	/**
	 * @var string|null
	 */
	private $errorCode;
	/**
	 * @var string|null
	 */
	private $errorMessage;
	/**
	 * @var string|null
	 */
	private $errorType;
	/**
	 * @var string|null
	 */
	private $requestId;

	public function __construct(
		?string $displayMessage,
		?string $errorCode,
		?string $errorMessage,
		?string $errorType,
		?string $requestId,
		int $code,
		string $message = "",
		Throwable $previous = null
	) {
		parent::__construct($message, $code, $previous);
		$this->displayMessage = $displayMessage;
		$this->errorCode = $errorCode;
		$this->errorMessage = $errorMessage;
		$this->errorType = $errorType;
		$this->requestId = $requestId;
	}

	/**
	 * @return string|null
	 */
	public function getDisplayMessage(): ?string {
		return $this->displayMessage;
	}

	/**
	 * @return string|null
	 */
	public function getErrorCode(): ?string {
		return $this->errorCode;
	}

	/**
	 * @return string|null
	 */
	public function getErrorMessage(): ?string {
		return $this->errorMessage;
	}

	/**
	 * @return string|null
	 */
	public function getErrorType(): ?string {
		return $this->errorType;
	}

	/**
	 * @return string|null
	 */
	public function getRequestId(): ?string {
		return $this->requestId;
	}

	/**
	 * Specify data which should be serialized to JSON
	 *
	 * @link  https://php.net/manual/en/jsonserializable.jsonserialize.php
	 * @return mixed data which can be serialized by <b>json_encode</b>,
	 * which is a value of any type other than a resource.
	 * @since 5.4.0
	 */
	public function jsonSerialize() {
		return [
			'displayMessage' => $this->displayMessage,
			'errorCode' => $this->errorCode,
			'errorMessage' => $this->errorMessage,
			'errorType' => $this->errorType,
			'requestId' => $this->requestId,
			'code' => $this->code,
			'message' => $this->message,
		];
	}
}