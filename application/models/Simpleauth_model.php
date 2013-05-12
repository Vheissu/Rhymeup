<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Simpleauth_model extends CI_Model {

	/**
	 * Get Group Name
	 *
	 * Gets a group name
	 *
	 * @param	int
	 * @return	mixed (string on success, FALSE on fail)
	 */
	public function get_group_name($group_id)
	{
		$this->db->select('group_name');
		$this->db->where('id', $group_id);
		$role = $this->db->get('groups');

		return ($role->num_rows == 1) ? $role->row('group_name') : FALSE;
	}

	/**
	 * Insert Group
	 *
	 * Inserts a new group
	 *
	 * @param	string $group_name
	 * @param   string $group_slug (optional) 
	 * @return	mixed (int on success, FALSE on fail)
	 */
	public function insert_group($group_name = '', $group_slug = '')
	{
		$this->db->set('group_name', $group_name);

		// If no group slug supplied, create one
		if ($group_slug == '') {
			$group_slug = url_title($group_name, 'underscore');
		}

		$this->db->set('group_slug', $group_slug);

		return ($this->db->insert('groups')) ? $this->db->insert_id() : FALSE;
	}

	/**
	 * Get Users
	 *
	 * Returns all users, allows for pagination
	 *
	 * @param	int $limit  - The amount of results to limit to
	 * @param	int $offset - Pagination offset
	 * @return	object
	 */
	public function get_users($limit = 10, $offset = 0)
	{
		$fields = 'users.id, users.username, users.email, users.password, users.group_id, groups.group_name, groups.group_slug';

		// If we don't have a return all users value, use the limit and offset values
		if ($limit != '*')
		{
			$this->db->limit($limit, $offset);
		}

		$this->db->select($fields);
		$this->db->join('groups', 'users.group_id = groups.id');
		return $this->db->get('users');
	}

	/**
	 * Count All Users
	 *
	 * Returns a count of all users
	 *
	 * @return	int
	 */
	public function count_all_users()
	{
		return $this->db->count_all('users');
	}

	/**
	 * Get User
	 *
	 * Returns all user information based on username
	 *
	 * @param	string
	 * @return	mixed
	 */
	public function get_user($username)
	{
		return $this->_get_user($username);
	}

    /**
     * Get User By Email
     *
     * Returns all user information based on email
     *
     * @param $email
     * @return mixed
     */
    public function get_user_by_email($email)
    {
        return $this->_get_user($email, 'email');
    }

	/**
	 * Get User By Id
	 *
	 * Returns all user information based on user id
	 *
	 * @param	int $id - User ID
	 * @return	mixed
	 */
	public function get_user_by_id($id)
	{
		return $this->_get_user($id, 'id');
	}

	/**
	 * Get User Password Reset
	 *
	 * Returns member information for password reset
	 *
	 * @param	int
	 * @param	string
	 * @return	mixed
	 */
	public function get_user_password_reset($id = '', $passkey = '')
	{
		$this->db->where('id', $id);
		$this->db->where('auth_code', $passkey);

		$user = $this->db->get('users');

		// If the user was found, return the user object
		return ($user->num_rows() == 1) ? $user : FALSE;
	}

	/**
	 * Get User
	 *
	 * Returns all information about any one member
	 *
	 * @param	string $needle   - The value to query by
	 * @param	string $haystack - The field to query by
	 * @return	mixed
	 */
	protected function _get_user($needle, $haystack = 'username')
	{
		$this->db->where($haystack, $needle);

		$user = $this->db->get('users');
		
		return ($user->num_rows() == 1) ? $user : FALSE;
	}

	/**
	 * Insert User
	 *
	 * Inserts a user
	 *
	 * @param	array $user_data
	 * @return	mixed (INT on success, BOOL on fail)
	 */
	public function insert_user($user_data)
	{
		if (isset($user_data['password']))
		{
			$user_data['password'] = $this->generate_password($user_data['password']);
		}

		return ($this->db->insert('users', $user_data)) ? $this->db->insert_id() : FALSE;
	}

	/**
	 * Update User
	 *
	 * Updates a user
	 *
	 * @param	array $user_data
	 * @return	bool
	 */
	public function update_user($user_data)
	{
		$this->db->where('id', $user_data['id']);

		if (isset($user_data['password']))
		{
			$user_data['password'] = $this->generate_password($user_data['password']);
		}

		return ( ! $this->db->update('users', $user_data)) ? FALSE : TRUE;
	}

	/**
	 * Delete User
	 *
	 * Deletes a user
	 *
	 * @param	integer $user_id
	 * @return	bool
	 */
	public function delete_user($user_id)
	{
		$this->db->where('id', $user_id);

		$this->db->delete('users');

		return ($this->db->affected_rows() > 0) ? TRUE : FALSE;
	}

	/**
	 * Generate Password
	 *
	 * @param	string	password
	 * @return	string
	 */
	public function generate_password($password)
	{
		// Return sha256 encrypted password
		return hash_hmac('sha256', $password, NULL);
	}

}