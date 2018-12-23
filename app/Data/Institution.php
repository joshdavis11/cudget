<?php

namespace App\Data;

use JsonSerializable;

/**
 * Class Institution
 *
 * @package App\Data
 */
class Institution implements JsonSerializable {
	/**
	 * @var array
	 */
	private $data;

	public function __construct(array $data) {
		$this->data = $data;
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
			'name' => $this->getName(),
		];
	}

	/**
	 * getName
	 *
	 * @return string
	 */
	public function getName(): string {
		return $this->data['institution']['name'];
	}
}