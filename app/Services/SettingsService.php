<?php

namespace App\Services;

use App\Model\Configuration;
use Illuminate\Http\Request;

/**
 * Class SettingsService
 *
 * @package App\Http\Services
 */
class SettingsService {
	/**
	 * Get configuration for a user
	 *
	 * @param int $userId
	 *
	 * @return Configuration
	 */
	public function getConfigurationForUser($userId) {
		return Configuration::where('user_id' , '=', $userId)->first();
	}

	/**
	 * Update configuration
	 *
	 * @param int     $userId
	 * @param Request $request
	 *
	 * @return bool|int
	 */
	public function updateConfiguration($userId, Request $request) {
		return $this->getConfigurationForUser($userId)->update($request->only(['bootswatch']));
	}
}