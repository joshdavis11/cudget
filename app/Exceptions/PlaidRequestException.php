<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Class PlaidRequestException
 *
 * @package App\Exceptions
 */
class PlaidRequestException extends Exception {
	/**
	 * @var string
	 */
	private $displayMessage;
	/**
	 * @var string
	 */
	private $errorCode;
	/**
	 * @var string
	 */
	private $errorMessage;
	/**
	 * @var string
	 */
	private $errorType;
	/**
	 * @var string
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
	 * @return string
	 */
	public function getDisplayMessage(): string {
		return $this->displayMessage;
	}

	/**
	 * @return string
	 */
	public function getErrorCode(): string {
		return $this->errorCode;
	}

	/**
	 * @return string
	 */
	public function getErrorMessage(): string {
		return $this->errorMessage;
	}

	/**
	 * @return string
	 */
	public function getErrorType(): string {
		return $this->errorType;
	}

	/**
	 * @return string
	 */
	public function getRequestId(): string {
		return $this->requestId;
	}
}