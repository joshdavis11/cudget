<?php

namespace App\Model;

use Eloquence\Behaviours\CamelCasing;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\Validator;

/**
 * Class BaseModel
 *
 * @package App\Model
 */
class BaseModel extends Model {
	use CamelCasing;

	/**
	 * @var array
	 */
	static protected $rules = [];
	/**
	 * @var MessageBag
	 */
	static private $errors;

	/**
	 * Validate
	 *
	 * @param array $data
	 *
	 * @return bool
	 */
	static public function validate(array $data) {
		// make a new validator object
		$Validator = self::getValidator($data);

		if ($Validator->fails()) {
			self::$errors = $Validator->errors();
			return false;
		}

		return true;
	}

	/**
	 * Get the Validator
	 *
	 * @param array $data
	 *
	 * @return Validator
	 */
	static private function getValidator(array $data) {
		return ValidatorFacade::make($data, static::$rules);
	}

	/**
	 * errors
	 *
	 * @return MessageBag
	 */
	static public function errors() {
		return self::$errors;
	}
}