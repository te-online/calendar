<?php

namespace OCA\Calendar\Controller;

use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\JSONResponse;
use OCP\AppFramework\Http;
use OCP\AppFramework\Http\DataResponse;
use OCP\IConfig;
use OCP\IRequest;
use OCP\IUserSession;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

class RestController extends ApiController {
	/** @var IDBConnection */
	protected $connection;

	/**
	 * constructor of the controller
	 * @param string $appName the name of the app
	 * @param IRequest $request an instance of the request
	 * @param activityMapper $activityMapper mapper for activities
	 * @param IDBConnection $connection
	 */
	function __construct($appName,
								IRequest $request,
								IDBConnection $connection
								) {
		parent::__construct($appName, $request);
		$this->connection = $connection;
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $eventId
	 * @return DataResponse
	 */
	function getActivityForEvent($eventId) {
		$query = $this->connection->getQueryBuilder();
		$query->select( '*' )->from( 'activity' )->where( 'subjectparams LIKE "%' . $eventId . '%"' );
		$result = $query->execute();
		$limit = 0;
		$data = [];
		while ($activity = $result->fetch()) {
			if($limit < 1) {
				$data[] = $activity;
			}
			$limit++;
		}
		return new DataResponse(['data' => $data && count($data) > 0 ? $data[0] : null]);
	}
}
