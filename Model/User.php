<?php
/**
 * User model for Zendesk.
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
class User extends ZendeskAppModel {

	/**
	 * Model name
	 * @var $name
	 */
	public $name = 'User';

	/**
	 * Database Configuration
	 * @var string $useDbConfig
	 */
	public $useDbConfig = 'Zendesk';

	/**
	 * Retrieve a list of all users
	 * @link http://developer.zendesk.com/documentation/rest_api/users.html#list-users
	 * @return array
	 */
	public function getList() {
		$this->request = array('uri' => array('path' => 'users.json'));

		return $this->find('all');
	}

	/**
	 * Retrieve a single user by id
	 * @link http://developer.zendesk.com/documentation/rest_api/users.html#show-user
	 * @param int $id
	 * @return array
	 */
	public function get($id) {
		if (!is_int($id)) {
			throw new CakeException(__('User ID must be an integer. %s was given', gettype($id)));
		}

		$this->request = array('uri' => array('path' => $id.'.json'));

		return $this->find('all');
	}

	/**
	 * Create a new user
	 * @link http://developer.zendesk.com/documentation/rest_api/users.html#create-user
	 * @param array $data
	 * @return array
	 */
	public function create(array $data) {
		$this->request = array(
			'uri' => array('path' => 'users.json'),
			'method' => 'POST',
			'body' => array('user' => $data)
		);

		return $this->save($data);
	}

	/**
	 * Create several users (batch request)
	 * @link http://developer.zendesk.com/documentation/rest_api/users.html#create-many-users
	 * @param array $data
	 * @return array
	 */
	public function createMany(array $data) {
		$this->request = array(
			'uri' => array(
				'path' => 'create_many.json'
			),
			'method' => 'POST',
			'body' => array('users' => $data) // users must be plural here
			);

		return $this->update($data);
	}

	/**
	 * Update a specific user by id
	 * @link http://developer.zendesk.com/documentation/rest_api/users.html#update-user
	 * @param int $id
	 * @param array $data
	 * @return array
	 */
	public function updateUser($id, array $data) {
		if (!is_int($id)) {
			throw new CakeException(__('User ID must be an integer. %s was given', gettype($id)));
		}
		$this->request = array(
			'uri' => array('path' => $id . '.json'),
			'method' => 'PUT',
			'body' => array('user' => $data)
			);

		return $this->update($data);
	}

	/**
	 * Suspend a specific user by id
	 * @param int $id
	 * @return array
	 */
	public function suspend($id) {
		return $this->updateUser(array('suspended' => true));
	}

	/**
	 * Delete a single user by id
	 * @link http://developer.zendesk.com/documentation/rest_api/users.html#delete-user
	 * @param int $id
	 * @return array
	 */
	public function delete($id) {
		if (!is_int($id)) {
			throw new CakeException(__('User ID must be an integer. %s was given', gettype($id)));
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
	 * @link http://developer.zendesk.com/documentation/rest_api/users.html#set-a-user's-password
	 */
	public function setPassword($id, $password) {

	}

	/**
	 * @link http://developer.zendesk.com/documentation/rest_api/users.html#change-a-user's-password
	 */
	public function changePassword($id, $password) {

	}


}