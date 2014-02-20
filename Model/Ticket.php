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

	// Added by RTS
	public $_schema = array(
		'subject' => array('type' => 'string'), // Required
		'comment' => array('type' => 'text'), // Required
	);

	/**
	 * Model name
	 * @var $name
	 */
	public $name = 'Ticket';

	/**
	 * Database Configuration
	 * @var string $useDbConfig
	 */
	public $useDbConfig = 'Zendesk';

	/**
	 * Retrieve a list of all tickets
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#listing-tickets
	 * @return array
	 */
	public function getList() {
		$this->request = array('uri' => array('path' => 'tickets.json'));

		return $this->find('all');
	}

	/**
	 * Retrieve a single ticket by id
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#getting-tickets
	 * @param int $id
	 * @return array
	 */
	public function get($id) {
		if (!is_int($id)) {
			throw new CakeException(__('Ticket ID must be an integer. %s was given', gettype($id)));
		}

		$this->request = array('uri' => array('path' => $id.'.json'));

		return $this->find('all');
	}

	/**
	 * Retrieve a the most recent tickets
	 * @return array
	 */
	public function getRecent() {
		$this->request = array('uri' => array('path' => 'recent.json'));

		return $this->find('all');
	}

	/**
	 * Retrieve a collection of multiple tickets by id
	 * @example $this->Ticket->getMany(array(1,2,3,4));
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#show-multiple-tickets
	 * @param array $ids
	 * @return array
	 */
	public function getMany(array $ids) {
		$this->request = array(
			'uri' => array(
				'path' => 'show_many.json?ids={' . implode(',', $ids) . '}'
			),
			'method' => 'POST'
		);

		return $this->find('all');
	}

	/**
	 * Create a new ticket
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#creating-tickets
	 * @param array $data
	 * @return array
	 */
	public function create($data = array(), $filterKey = false) {
		$this->request = array(
			'uri' => array('path' => 'tickets.json'),
			'method' => 'POST',
			'body' => array('ticket' => $data)
		);

		return $this->save($data);
	}

	/**
	 * Update a specific ticket by id
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#updating-tickets
	 * @param int $id
	 * @param array $data
	 * @return array
	 */
	public function updateTicket($id, $data) {
		if (!is_int($id)) {
			throw new CakeException(__('Ticket ID must be an integer. %s was given', gettype($id)));
		}
		$this->request = array(
			'uri' => array('path' => $id . '.json'),
			'method' => 'PUT',
			'body' => array('ticket' => $data)
			);

		return $this->update($data);
	}

	/**
	 * Update several tickets by id (batch request)
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#bulk-updating-tickets
	 * @param array $ids
	 * @param array $data
	 * @return array
	 */
	public function updateMany(array $ids, array $data) {
		$this->request = array(
			'uri' => array(
				'path' => 'update_many.json?ids={' . implode(',', $ids) . '}'
			),
			'method' => 'PUT',
			'body' => array('ticket' => $data)
			);

		return $this->update($data);
	}

	/**
	 * Mark a ticket as Spam and suspend the user
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#mark-a-ticket-as-spam-and-suspend-the-requester
	 * @param int $id
	 * @return array
	 */
	public function markAsSpam($id) {
		if (!is_int($id)) {
			throw new CakeException(__('Ticket ID must be an integer. %s was given', gettype($id)));
		}

		$this->request = array(
			'uri' => array('path' => $id . '/mark_as_spam.json'),
			'method' => 'PUT'
			);

		return $this->update($data);
	}

	/**
	 * Delete a single ticket by id
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#deleting-tickets
	 * @param int $id
	 * @return array
	 */
	public function delete($id = NULL, $cascade = true) {
		if (!is_int($id)) {
			throw new CakeException(__('Ticket ID must be an integer. %s was given', gettype($id)));
		}

		$this->request = array(
			'uri' => array(
				'path' => $id . '.json'
			),
			'method' => 'DELETE'
		);

		return $this->delete($id);
	}

	/**
	 * Bulk Deleting Tickets
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#bulk-deleting-tickets
	 * @param array $ids
	 * @return array
	 */
	public function deleteMany(array $ids) {
		$this->request = array(
			'uri' => array(
				'path' => 'destroy_many.json?ids={' . implode(',', $ids) . '}'
			),
			'method' => 'DELETE'
		);

		return $this->delete($ids);
	}

	/**
	 * List Collaborators for a Ticket
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#list-collaborators-for-a-ticket
	 * @param int $id
	 * @return array
	 */
	public function listCollaborators($id) {
		if (!is_int($id)) {
			throw new CakeException(__('Ticket ID must be an integer. %s was given', gettype($id)));
		}

		$this->request = array(
			'uri' => array(
				'path' => $id . '/collaborators.json'
			),
			'method' => 'GET'
		);

		return $this->find('all');
	}

	/**
	 * Listing Ticket Incidents
	 * @link http://developer.zendesk.com/documentation/rest_api/tickets.html#listing-ticket-incidents
	 * @param int $id
	 * @return array
	 */
	public function listIncidents($id) {
		if (!is_int($id)) {
			throw new CakeException(__('Ticket ID must be an integer. %s was given', gettype($id)));
		}

		$this->request = array(
			'uri' => array(
				'path' => $id . '/incidents.json'
			),
			'method' => 'GET'
		);

		return $this->find('all');
	}
}