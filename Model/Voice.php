<?php
/**
 * Voice model for Zendesk.
 *
 *
 * PHP 5
 *
 * Copyright (c) UFN, LLC. (http://www.ufn.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) UFN, LLC. (http://www.ufn.com)
 * @link          https://github.com/unitedfloristnetwork/Zendesk Cakephp Zendesk Plugin
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('ZendeskAppModel', 'Zendesk.Model');
class Voice extends ZendeskAppModel {

	public $name = 'Voice';

	public $useDbConfig = 'Zendesk';

	/**
	 * Getting Statistics for Current Queue Activity
	 * @link http://developer.zendesk.com/documentation/rest_api/voice.html#getting-statistics-for-current-queue-activity
	 * @return array
	 */
	public function getCurrentQueueActivity() {
		$this->useTable = false;
		$this->request = array('uri' => array('path' => 'channels/voice/stats/current_queue_activity.json'));
		return $this->find('all');
	}

	/**
	 * Getting Statistics for Historical Queue Activity
	 * @link http://developer.zendesk.com/documentation/rest_api/voice.html#getting-statistics-for-historical-queue-activity
	 * @return array
	 */
	public function getHistoricalQueueActivity() {
		$this->useTable = false;
		$this->request = array('uri' => array('path' => 'channels/voice/stats/historical_queue_activity.json'));
		return $this->find('all');
	}

	/**
	 * Getting Statistics for Agents Activity
	 * @link http://developer.zendesk.com/documentation/rest_api/voice.html#getting-statistics-for-agents-activity
	 * @return array
	 */
	public function getAgentsActivity() {
		$this->useTable = false;
		$this->request = array('uri' => array('path' => 'channels/voice/stats/agents_activity.json'));
		return $this->find('all');
	}
}