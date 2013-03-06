<?php
/**
 * Problem model for Zendesk.
 *
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
 */
App::uses('ZendeskAppModel', 'Zendesk.Model');
class Problem extends ZendeskAppModel {

	public $name = 'Problem';

	public $useDbConfig = 'Zendesk';

	public function getList() {
		$this->request = array('uri' => array('path' => 'problems.json'));
		return $this->find('all');
	}

}