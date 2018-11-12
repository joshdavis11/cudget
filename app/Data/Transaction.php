<?php

namespace App\Data;

use DateTimeImmutable;

/**
 * Class Transaction
 *
 * @package App\Data
 */
class Transaction {
	/**
	 * @var string
	 */
	private $accountId;
	/**
	 * @var float
	 */
	private $amount;
	/**
	 * @var DateTimeImmutable
	 */
	private $date;
	/**
	 * @var string
	 */
	private $currencyCode;
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $transactionId;

	/**
	 * Transaction constructor.
	 *
	 * @param string $accountId
	 * @param float  $amount
	 * @param string $date
	 * @param string $currencyCode
	 * @param string $name
	 * @param string $transactionId
	 */
	public function __construct(
		string $accountId,
		float $amount,
		DateTimeImmutable $date,
		string $currencyCode,
		string $name,
		string $transactionId
	) {
		$this->accountId = $accountId;
		$this->amount = $amount;
		$this->date = $date;
		$this->currencyCode = $currencyCode;
		$this->name = $name;
		$this->transactionId = $transactionId;
	}

	/**
	 * @return string
	 */
	public function getAccountId(): string {
		return $this->accountId;
	}

	/**
	 * @return float
	 */
	public function getAmount(): float {
		return $this->amount;
	}

	/**
	 * @return DateTimeImmutable
	 */
	public function getDate(): DateTimeImmutable {
		return $this->date;
	}

	/**
	 * @return string
	 */
	public function getCurrencyCode(): string {
		return $this->currencyCode;
	}

	/**
	 * @return string
	 */
	public function getName(): string {
		return $this->name;
	}

	/**
	 * @return string
	 */
	public function getTransactionId(): string {
		return $this->transactionId;
	}
}