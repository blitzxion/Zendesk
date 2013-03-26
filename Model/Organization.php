<?php
/**
 * Organization model for Zendesk.
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
class Organization extends ZendeskAppModel {

	public $name = 'Organization';

	public $useDbConfig = 'Zendesk';

	/**
	 * Retrieve a list of all organizations
	 * @link http://developer.zendesk.com/documentation/rest_api/organizations.html#listing-organizations
	 * @return array
	 */
	public function getList() {
		$this->request = array('uri' => array('path' => 'organizations.json'));

		return $this->find('all');
	}

	/**
	 * Retrieve a single organization by id
	 * @link http://developer.zendesk.com/documentation/rest_api/organizations.html#getting-organizations
	 * @param int $id
	 * @return array
	 */
	public function get($id) {
		if (!is_int($id)) {
			throw new CakeException(__('Org ID must be an integer. %s was given', gettype($id)));
		}

		$this->request = array('uri' => array('path' => $id.'.json'));

		return $this->find('all');
	}

	/**
	 * Create a new organization
	 * @link http://developer.zendesk.com/documentation/rest_api/organizations.html#creating-organizations
	 * @param array $data
	 * @return array
	 */
	public function create(array $data) {
		$this->request = array(
			'uri' => array('path' => 'organizations.json'),
			'method' => 'POST',
			'body' => array('organization' => $data)
		);

		return $this->save($data);
	}

	/**
	 * Create several organizations (batch request)
	 * @link http://developer.zendesk.com/documentation/rest_api/organizations.html#create-many-organizations
	 * @param array $data
	 * @return array
	 */
	public function createMany(array $data) {
		$this->request = array(
			'uri' => array(
				'path' => 'create_many.json'
			),
			'method' => 'POST',
			'body' => array('organizations' => $data) // users must be plural here
			);

		return $this->update($data);
	}

	/**
	 * Update a specific organization by id
	 * @link http://developer.zendesk.com/documentation/rest_api/organizations.html#updating-organizations
	 * @param int $id
	 * @param array $data
	 * @return array
	 */
	public function updateOrganization($id, array $data) {
		if (!is_int($id)) {
			throw new CakeException(__('Org ID must be an integer. %s was given', gettype($id)));
		}
		$this->request = array(
			'uri' => array('path' => $id . '.json'),
			'method' => 'PUT',
			'body' => array('organization' => $data)
			);

		return $this->update($data);
	}

	/**
	 * Delete a single organization by id
	 * @link http://developer.zendesk.com/documentation/rest_api/organizations.html#deleting-organizations
	 * @param int $id
	 * @return array
	 */
	public function deleteOrganization($id) {
		if (!is_int($id)) {
			throw new CakeException(__('Org ID must be an integer. %s was given', gettype($id)));
		}

		$this->request = array(
			'uri' => array(
				'path' => $id . '.json'
			),
			'method' => 'DELETE'
		);

		return $this->delete($id);
	}
}