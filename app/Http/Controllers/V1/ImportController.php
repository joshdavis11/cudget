<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Services\ImportService;
use Dotenv\Exception\InvalidFileException;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Class ImportController
 *
 * @package App\Http\Controllers
 */
class ImportController extends Controller {
	/**
	 * @var ImportService
	 */
	protected $ImportService;

	/**
	 * ImportController constructor.
	 *
	 * @param ImportService $ImportService
	 */
	public function __construct(ImportService $ImportService) {
		$this->ImportService = $ImportService;
	}

	/**
	 * import
	 *
	 * @param Request $request
	 *
	 * @return Response
	 */
	public function import(ImportService $ImportService, Request $request) {
		try {
			$Budget = $ImportService->import($request);
		} catch (InvalidFileException $exception) {
			return new Response('Invalid file', Response::HTTP_BAD_REQUEST);
		}
		$headers = [];
		if ($Budget->id) {
			$headers['Location'] = '/budgets/' . $Budget->id;
		}
		return new Response('Imported!', Response::HTTP_OK, $headers);
	}
}