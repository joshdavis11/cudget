<?php

namespace App\Utilities;

use App\Data\Account;
use App\Data\Institution;
use App\Data\Transaction;
use app\Exceptions\PlaidAccessTokenException;
use App\Exceptions\PlaidRequestException;
use App\Http\Responses\PlaidAccessResponse;
use DateTimeImmutable;
use Unirest\Exception;
use Unirest\Method;
use Unirest\Request;
use Unirest\Response;

/**
 * Class Plaid
 *
 * @package App\Utilities
 */
class Plaid {
	private $clientId;
	private $publicKey;
	private $secret;
	private $url;

	/**
	 * Plaid constructor.
	 */
	public function __construct() {
		$this->clientId = env('PLAID_CLIENT_ID');
		$this->publicKey = env('PLAID_PUBLIC_KEY');

		switch(env('PLAID_ENVIRONMENT', 'sandbox')) {
			case 'sandbox':
				$this->secret = env('PLAID_SECRET_SANDBOX');
				$this->url = 'https://sandbox.plaid.com';
				break;
			case 'development':
				$this->secret = env('PLAID_SECRET_DEVELOPMENT');
				$this->url = 'https://development.plaid.com';
				break;
			case 'production':
				$this->secret = env('PLAID_SECRET_PRODUCTION');
				$this->url = 'https://production.plaid.com';
				break;
		}
		Request::jsonOpts(true);
	}

	/**
	 * makeRequest
	 *
	 * @param string $method
	 * @param string $endpoint
	 * @param array  $data
	 * @param array  $headers
	 *
	 * @return Response
	 * @throws Exception
	 * @throws PlaidRequestException
	 */
	private function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): Response {
		$Response = Request::send($method, $this->url . $endpoint, json_encode($data), $this->mergeHeaders($headers));
		if ($Response->code >= 300 || $Response->code < 200) {
			throw new PlaidRequestException(
				$Response->body['display_message'],
				$Response->body['error_code'],
				$Response->body['error_message'],
				$Response->body['error_type'],
				$Response->body['request_id'],
				$Response->code
			);
		}

		return $Response;
	}

	/**
	 * mergeAuthData
	 *
	 * @param array $data
	 *
	 * @return array
	 */
	private function mergeAuthData(array $data = []): array {
		return array_merge([
			'client_id' => $this->clientId,
			'secret' => $this->secret,
		], $data);
	}

	/**
	 * getHeaders
	 *
	 * @param array $headers
	 *
	 * @return array
	 */
	private function mergeHeaders(array $headers): array {
		return array_merge([
			'Content-Type' => 'application/json',
		], $headers);
	}

	/**
	 * Get an access token from a public token
	 *
	 * @param string $publicToken
	 *
	 * @return PlaidAccessResponse
	 * @throws PlaidAccessTokenException
	 * @throws PlaidRequestException
	 * @throws Exception
	 */
	public function getAccessToken(string $publicToken): PlaidAccessResponse {
		$Response = $this->makeRequest(Method::POST, '/item/public_token/exchange', $this->mergeAuthData([
			'public_token' => $publicToken,
		]));

		if($Response->code !== 200 || empty($Response->body['access_token'])) {
			throw new PlaidAccessTokenException($Response, 'Error attempting to exchange a public_token for an access_token');
		}

        return new PlaidAccessResponse($Response->body['access_token'], $Response->body['item_id']);
	}

	/**
	 * Get a public_token from an access_token
	 *
	 * @param string $accessToken
	 *
	 * @return string
	 * @throws PlaidRequestException
	 * @throws Exception
	 */
	public function getPublicToken(string $accessToken): string {
		$Response = $this->makeRequest(Method::POST, '/item/public_token/create', $this->mergeAuthData([
			'access_token' => $accessToken,
		]));

		return $Response->body['public_token'];
	}

	/**
	 * getInstitutions
	 *
	 * @return Response
	 * @throws Exception
	 * @throws PlaidRequestException
	 */
	public function getInstitutions(): Response {
		return $this->makeRequest(Method::POST,
			'/institutions/get',
			$this->mergeAuthData()
		);
	}

	/**
	 * Get an institution by ID
	 *
	 * @param string $institutionId
	 *
	 * @return Institution
	 * @throws Exception
	 * @throws PlaidRequestException
	 */
	public function getInstitutionById(string $institutionId): Institution {
		$Response = $this->makeRequest(Method::POST,
			'/institutions/get_by_id',
			[
				'public_key' => $this->publicKey,
				'institution_id' => $institutionId,
			]
		);

		return new Institution($Response->body);
	}

	/**
	 * getAuth
	 *
	 * @param string $accessToken
	 *
	 * @return Account[]
	 * @throws Exception
	 * @throws PlaidRequestException
	 */
	public function getAuth(string $accessToken): array {
		$Response = $this->makeRequest(Method::POST,
			'/auth/get',
			$this->mergeAuthData([
				'access_token' => $accessToken,
			])
		);

		$Accounts = [];
		foreach ($Response->body['accounts'] ?? [] as $accountData) {
			$Accounts[] = new Account(
				$accountData['account_id'],
				$accountData['mask'],
				$accountData['name'] ?: $accountData['official_name'],
				$accountData['type'],
				$accountData['subtype']
			);
		}

		return $Accounts;
	}

	/**
	 * getTransactions
	 *
	 * @param string $accessToken
	 * @param string $startDate
	 * @param string $endDate
	 * @param array  $accountIds
	 *
	 * @return Transaction[]
	 * @throws Exception
	 * @throws PlaidRequestException
	 */
	public function getTransactions(string $accessToken, string $startDate, string $endDate, array $accountIds = []): array {
		$offset = 0;
		$count = 500;
		$transactions = [];
		$prev = null;
		do {
			$Response = $this->makeRequest(Method::POST,
				'/transactions/get',
				$this->mergeAuthData([
					'access_token' => $accessToken,
					'start_date' => $startDate,
					'end_date' => $endDate,
					'options' => [
						'account_ids' => $accountIds,
						'count' => $count,
						'offset' => $offset,
					],
				])
			);

			foreach ($Response->body['transactions'] as $transaction) {
				if ($transaction['pending'] || empty($transaction['transaction_id']) || array_key_exists($transaction['transaction_id'], $transactions)) {
					continue;
				}
				$transactions[$transaction['transaction_id']] = new Transaction(
					$transaction['account_id'],
					-$transaction['amount'], //Plaid does it backwards. Negative means income, positive means expense
					new DateTimeImmutable($transaction['date']),
					$transaction['iso_currency_code'],
					$transaction['name'],
					$transaction['transaction_id']
				);
			}
			$offset += $count;
		} while($offset < $Response->body['total_transactions']);

		return $transactions;
	}
}