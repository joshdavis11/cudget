<?php

namespace app\Exceptions;

use Exception;
use Throwable;
use Unirest\Response;

/**
 * Class PlaidAccessTokenException
 *
 * @package app\Exceptions
 */
class PlaidAccessTokenException extends Exception {
	/**
	 * @var Response
	 */
	private $Response;

	/**
	 * PlaidAccessTokenException constructor.
	 *
	 * @param Response       $Response
	 * @param string         $message
	 * @param int            $code
	 * @param Throwable|null $previous
	 */
	public function __construct(Response $Response, string $message = "", int $code = 0, Throwable $previous = null) {
		parent::__construct($message, $code, $previous);
		$this->Response = $Response;
	}

	/**
	 * @return Response
	 */
	public function getResponse(): Response {
		return $this->Response;
	}
}