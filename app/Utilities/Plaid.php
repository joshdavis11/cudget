<?php

namespace App\Utilities;

use App\Data\Transaction;
use app\Exceptions\PlaidAccessTokenException;
use App\Http\Responses\PlaidAccessResponse;
use DateTimeImmutable;
use Illuminate\Support\Debug\Dumper;
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
	 * @throws \Unirest\Exception
	 */
	private function makeRequest(string $method, string $endpoint, array $data = [], array $headers = []): Response {
		return Request::send($method, $this->url . $endpoint, json_encode($data), $this->mergeHeaders($headers));
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
	 * @throws \Unirest\Exception
	 * @throws PlaidAccessTokenException
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
	 * getInstitutions
	 *
	 * @return Response
	 * @throws \Unirest\Exception
	 */
	public function getInstitutions(): Response {
		return $this->makeRequest(Method::POST,
			'/institutions/get',
			$this->mergeAuthData()
		);
	}

	/**
	 * getAuth
	 *
	 * @param string $accessToken
	 *
	 * @return Response
	 * @throws \Unirest\Exception
	 */
	public function getAuth(string $accessToken): Response {
		return $this->makeRequest(Method::POST,
			'/auth/get',
			$this->mergeAuthData([
				'access_token' => $accessToken,
			])
		);
	}

	/**
	 * getTransactions
	 *
	 * @param string $accessToken
	 * @param string $startDate
	 * @param string $endDate
	 *
	 * @return Transaction[]
	 * @throws \Unirest\Exception
	 */
	public function getTransactions(string $accessToken, string $startDate, string $endDate): array {
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
						//account_ids => '1,2,3'
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
					$transaction['amount'],
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