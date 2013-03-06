<?php
/**
 * Ticket model for Zendesk.
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
class Ticket extends ZendeskAppModel {

	public $name = 'Ticket';

	public $useDbConfig = 'Zendesk';

	public function getList() {
		$this->request = array('uri' => array('path' => 'tickets.json'));
		return $this->find('all');
	}

	public function get($id) {
		$this->request = array('uri' => array('path' => $id.'.json'));
		return $this->find('all');
	}

	public function getRecent() {
		$this->request = array('uri' => array('path' => 'recent.json'));
		return $this->find('all');
	}

	public function getMany($ids = array()) {
		$this->request = array(
			'uri' => array(
				'path' => 'show_many.json?ids={' . implode(',', $ids) . '}'
			),
			'method' => 'POST'
		);
		return $this->find('all');
	}

	public function create($data) {
		$this->request = array(
			'uri' => array('path' => 'tickets.json'),
			'method' => 'POST',
			'body' => $data
			);
		return $this->save($data);
	}

	public function update($id, $data) {
		$this->request = array(
			'uri' => array('path' => $id.'.json'),
			'method' => 'PUT',
			'body' => $data
			);
		return $this->update($data);
	}

	public function updateMany($ids, $data) {

	}

	public function markAsSpam($id) {

	}

	public function delete($id) {

	}

	public function deleteMany($ids) {

	}

	public function listCollaborators($id) {

	}

	public function listIncidents() {

	}

}