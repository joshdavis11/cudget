<?php

namespace App\Data;

class Account {
	/**
	 * @var string
	 */
	private $accountId;
	/**
	 * @var string
	 */
	private $mask;
	/**
	 * @var string
	 */
	private $name;
	/**
	 * @var string
	 */
	private $type;
	/**
	 * @var string
	 */
	private $subtype;

	/**
	 * Account constructor.
	 *
	 * @param string $accountId
	 * @param string $mask
	 * @param string $name
	 * @param string $type
	 * @param string $subtype
	 */
	public function __construct(
		string $accountId,
		string $mask,
		string $name,
		string $type,
		string $subtype
	) {
		$this->accountId = $accountId;
		$this->mask = $mask;
		$this->name = $name;
		$this->type = $type;
		$this->subtype = $subtype;
	}

	/**
	 * @return string
	 */
	public function getAccountId(): string {
		return $this->accountId;
	}

	/**
	 * @return string
	 */
	public function getMask(): string {
		return $this->mask;
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
	public function getType(): string {
		return $this->type;
	}

	/**
	 * @return string
	 */
	public function getSubtype(): string {
		return $this->subtype;
	}
}