<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class BaseFormRequest extends FormRequest {
	/**
	 * authorize
	 *
	 * @return bool
	 */
	abstract public function authorize(): bool;

	/**
	 * rules
	 *
	 * @return array
	 */
	abstract public function rules(): array;
}