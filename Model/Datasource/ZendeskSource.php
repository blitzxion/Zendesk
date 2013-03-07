<?php
/**
 * ZendeskSource
 *
 * PHP 5
 *
 * Copyright (c) Particle Ventures, LLC. (http://www.ufn.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Particle Ventures, LLC. (http://www.ufn.com)
 * @link          https://github.com/unitedfloristnetwork/Zendesk Cakephp Zendesk Plugin
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 * 
 *
 * Datasource for Zendesk API
 * Set up to use this datasource in your project's database.php file.
 * Example:
 * public $Zendesk = array(
 *		'datasource' => 'Zendesk.ZendeskSource',
 *		'apiUser' => 'Your Zendesk email address',
 *		'apiKey' => 'Your Zendesk Secret Key',
 *		'host' => 'subdomain.zendesk.com',
 *		'port' => '443',
 *		'timeout' => '20'
 *		);
 *
 *
 */
App::uses('DataSource', 'Model/Datasource');
App::uses('HttpSocket', 'Network/Http');
class ZendeskSource extends DataSource {

	/**
	 * Description string for this Data Source.
	 *
	 * @var string
	 */
	public $description = 'Zendesk Datasource';

	/**
	 * HttpSocket Object
	 *
	 * @var object Http
	 */
	public $Http = null;

	/**
	 * Zendesk Api Version
	 *
	 * @var string ApiVersion
	 */
	protected $ApiVersion = 'v2';
	
	/**
	 * Time the last request took
	 *
	 * @var integer
	 */
	public $took = null;

	/**
	 * Request count.
	 *
	 * @var integer
	 */
	protected $_requestCnt = 0;

	/**
	 * Total duration of all request.
	 *
	 * @var integer
	 */
	protected $_requestTime = null;

	/**
	 * Log of request executed by this DataSource
	 *
	 * @var array
	 */
	protected $_requestLog = array();

	/**
	 * Maximum number of items in request log
	 *
	 * This is to prevent query log taking over too much memory.
	 *
	 * @var integer Maximum number of request in the request log.
	 */
	protected $_requestLogMax = 200;

	/**
	 * Request log limit per entry in bytes
	 *
	 * @var integer Request log limit per entry in bytes
	 */
	protected $_requestLogLimitBytes = 256;

	/**
	 * Holds the configuration
	 *
	 * @var array
	 */
	public $config = array();

	/**
	 * Configuration base
	 *
	 * @var array
	 */
	public $_baseConfig = array(
		'host' => 'api.zendesk.com',
		'apiUser' => '',
		'apiKey' => '',
		'port' => 443,
		'timeout' => 20
	);

	/**
	 * Default Constructor
	 * Configure load and configure HttpSocket
	 *
	 * @param array $config options
	 */
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->Http =& new HttpSocket(array('timeout' => $this->config['timeout']));
		
		$this->Http->configAuth('Basic', $this->config['apiUser'].'/token', $this->config['apiKey']);
		$this->Http->request['uri']['host'] = $this->config['host'];
		$this->Http->request['uri']['port'] = $this->config['port'];
		
		if ($this->config['port'] == 443) { // Only Https is currently allowed but check anyway.
			$this->Http->request['uri']['scheme'] = 'https';
		} else {
			$this->Http->request['uri']['scheme'] = 'http';
		}
	}

	/**
	 * Checks if the source is connected.
	 *
	 * @return true
	 */
	public function isConnected() {
		return true;
	}

	/**
	 * List Sources as required by Cake
	 * @param mixed $data
	 * @return null
	 */
	public function listSources($data = null) {
        return null;
    }

	/**
	 * Change the method to post and past to request function
	 *
	 * @param Model $model
	 * @param array $fields
	 * @param array $values
	 */
	public function create(\Model $model, $fields = null, $values = null) {
		$model->request = array_merge(array('method' => 'POST'), $model->request);
		return $this->request($model);
	}

	/**
	 * Change the method to get and past to request function
	 *
	 * @param Model $model
	 * @param array $queryData
	 */
	public function read(\Model $model, $queryData = array(), $recursive = null) {
		$model->request = array_merge(array('method' => 'GET'), $model->request);
		return $this->request($model);
	}

	/**
	 * Change the method to put and past to request function
	 *
	 * @param Model $model
	 * @param array $fields
	 * @param array $values
	 */
	public function update(\Model $model, $fields = null, $values = null, $conditions = null) {
		$model->request = array_merge(array('method' => 'PUT'), $model->request);
		return $this->request($model);
	}

	/**
	 * Change the method to delete and past to request function
	 *
	 * @param Model $model
	 * @param mixed $id
	 */
	public function delete(\Model $model, $id = null) {
		$model->request = array_merge(array('method' => 'DELETE'), $model->request);
		return $this->request($model);
	}

	/**
	 * Query function as required by cake for save and update requests
	 * @param string $request
	 * @param array $params
	 * @param Model $model
	 * @return array
	 */
	public function query($request, $params, \Model $model) {
		return $this->$request($model, $params);
	}

	/**
	 * Sends request to Zendesk and returns the response
	 *
	 * @param Model $model
	 * @return mixed array Or false
	 */
	public function request(\Model $model) {
		$request = $this->__configureRequest($model); // Configure the request
		$log = isset($request['log']) ? $request['log'] : Configure::read('debug') > 1;

		$timerStart = microtime(true); // Start measuring time for query

		$response = $this->Http->request($request, null, array('header' => array('Content-Type' => 'application/json')));

		if ($log) { // Log the request and time it took
			$this->took = round(microtime(true) - $timerStart, 3) * 1000;
			$this->logRequest($request, $response);
		}

		$model->response = json_decode($response->body, true); // Decode the Json response

		// Check response status code for success or failure
		if (substr($this->Http->response['status']['code'], 0, 1) != 2) {
			if (is_object($model) && method_exists($model, 'onError')) {
				$model->onError();
			}
			return false;
		}

		return $model->response;
	}

	/**
	 * Configure the Http Request
	 * @param Model $model
	 * @return array
	 */
	private function __configureRequest(\Model $model) {
		$request = $model->request;

		$this->Http->request['uri']['path'] = '/api/' . $this->ApiVersion . '/' . $model->useTable;

		if ($request['uri']['path'] == $model->useTable.'.json') {
			$this->Http->request['uri']['path'] .= '.json';
		} else {
			 $this->Http->request['uri']['path'] .= '/' . $request['uri']['path'];
		}
		$this->Http->request['method'] = $request['method'];
		
		if (isset($request['body'])) {
			$this->Http->request['body'] = $request['body'];
		}
		return $this->Http->request;
	}

    /**
	 * Log given HTTP request.
	 *
	 * @copyright Copyright (c) Takashi Nojima
	 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
	 * @link https://github.com/nojimage/CakePHP-ReST-DataSource-Plugin/blob/master/Model/Datasource/RestSource.php
	 * @param array $request request data
	 * @param array $response response data
	 * @return void
	 */
	public function logRequest($request, $response) {
		$this->_requestCnt++;
		$this->_requestTime += $this->took;
		$separator = '[**snip**]';
		$maxlength = $this->_requestLogLimitBytes - strlen($separator);

		$requestUri = $this->Http->url($request['uri']);
		$requestBody = $this->Http->request['body'];
		$responseBody = $this->Http->response['body'];
		if (strlen($requestBody) > $this->_requestLogLimitBytes) {
			$requestBody = substr_replace($requestBody, $separator, $maxlength / 2, strlen($requestBody) - $maxlength);
		}
		if (strlen($responseBody) > $this->_requestLogLimitBytes) {
			$responseBody = substr_replace($responseBody, $separator, $maxlength / 2, strlen($responseBody) - $maxlength);
		}
		$this->_requestLog[] = array(
			'request_method' => $this->Http->request['method'],
			'request_uri' => $requestUri,
			'request_body' => h($requestBody),
			'response_code' => $this->Http->response['status']['code'],
			'response_type' => $this->Http->response['header']['Content-Type'],
			'response_size' => strlen($this->Http->response['body']),
			'response_body' => h($responseBody),
			'query' => $this->Http->request['method'] . ' ' . $requestUri,
			'params' => '',
			'error' => '',
			'affected' => '',
			'numRows' => strlen($this->Http->response['body']),
			'took' => $this->took
		);
		if (count($this->_requestLog) > $this->_requestLogMax) {
			array_pop($this->_requestLog);
		}
	}

	/**
	 * Get the request log as an array.
	 *
	 * @copyright Copyright (c) Takashi Nojima
	 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
	 * @link https://github.com/nojimage/CakePHP-ReST-DataSource-Plugin/blob/master/Model/Datasource/RestSource.php
	 * @param boolean $sorted Get the request sorted by time taken, defaults to false.
	 * @param boolean $clear If True the existing log will cleared.
	 * @return array Array of queries run as an array
	 */
	public function getLog($sorted = false, $clear = true) {
		if ($sorted) {
			$log = sortByKey($this->_requestLog, 'took', 'desc', SORT_NUMERIC);
		} else {
			$log = $this->_requestLog;
		}
		if ($clear) {
			$this->_requestLog = array();
		}
		return array('log' => $log, 'count' => $this->_requestCnt, 'time' => $this->_requestTime);
	}
}