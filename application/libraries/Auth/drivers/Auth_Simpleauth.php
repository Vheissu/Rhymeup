<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class Auth_Simpleauth extends CI_Driver {

	// Codeigniter instance
	public $CI;

	// Group ID for current user - 0 means guest
	protected $group_id = 0;
	
	public function __construct()
	{
		// Store reference to the Codeigniter super object
		$this->CI =& get_instance();

		// Load needed Codeigniter Goodness
		$this->CI->load->database();
		$this->CI->load->library('session');
        $this->CI->load->library('email');
		$this->CI->load->model('simpleauth_model');
		$this->CI->load->helper('cookie');
		$this->CI->load->helper('helper');

		// Store the group ID for easier reference
		$this->group_id = $this->CI->session->userdata('group_id');

		// Check for a rememberme me cookie
		$this->_check_remember_me();
	}


	/**
	 * Is Super Admin
	 *
	 * Is the currently logged in user a super administrator?
	 *
	 * @return bool
	 *
	 */	
	public function is_super_admin()
	{
		return (in_array($this->group_id, $this->_config['group.sadmin'])) ? TRUE : FALSE;
	}

	/**
	 * Is Admin
	 *
	 * Is the currently logged in user an administrator?
	 *
	 * @return bool
	 *
	 */
	public function is_admin()
	{
		return (in_array($this->group_id, $this->_config['group.admin']) OR in_array($this->group_id, $this->_config['group.sadmin'])) ? TRUE : FALSE;
	}

    /**
     * Is User
     *
     * Is the currently logged in user a standard user?
     *
     * @return bool
     *
     */
	public function is_user()
	{
		return (in_array($this->group_id, $this->_config['group.user'])) ? TRUE : FALSE;
	}

	/**
	 * Is Group
	 *
	 * Does the currently logged in user belong to a
	 * particular group?
	 *
	 * @param int $id
	 * @return bool - true if yes, false if no	 
	 *
	 */
	public function is_group($id)
	{
		return ($id == $this->group_id) ? TRUE : FALSE;
	}

	/**
	 * Logged In
	 *
	 * Will return TRUE or FALSE if the user if logged in
	 *
	 * @return bool (TRUE if logged in, FALSE if not logged in)
	 *
	 */
	public function logged_in()
	{
		return ($this->CI->session->userdata('user_id')) ? TRUE : FALSE;
	}

	/**
	 * User ID
	 *
	 * Returns user ID of currently logged in user
	 *
	 * @return mixed (user ID on success or false on failure)
	 *
	 */
	public function user_id()
	{
		return ($this->CI->session->userdata('user_id')) ? $this->CI->session->userdata('user_id') : FALSE;
	}

    /**
     * Group
     *
     * Return the current user group ID
     *
     * @return int
     */
	public function group()
	{
		return $this->group_id;
	}

	/**
	 * Login
	 *
	 * Logs a user in, you guessed it!
	 *
	 * @param $identity
	 * @param $password
	 * @return mixed (user ID on success or false on failure)
	 *
	 */
	public function login($identity, $password)
	{
        // If we have specified users are to login with a username
        if ($this->_config['login.identity'] == 'username')
        {
            // Get the user from the database by their username
            $user = $this->CI->simpleauth_model->get_user($identity);
        }
        elseif ($this->_config['login.identity'] == 'email')
        {
            // Get the user from the database by their email
            $user = $this->CI->simpleauth_model->get_user_by_email($identity);
        }
        else
        {
            $type = $this->_detect_identity($identity);
            if ($type == 'username')
            {
                // Get the user from the database by their username
                $user = $this->CI->simpleauth_model->get_user($identity);
            }
            else
            {
                // Get the user from the database by their email
                $user = $this->CI->simpleauth_model->get_user_by_email($identity);
            }
        }

		// The user was found
		if ($user)
		{
			// Compare the user and pass
			if ($this->CI->user_model->generate_password($password) == $user->row('password'))
			{
				$group_id   = $user->row('group_id');
				$group_name = $user->row('group_name');
				$group_slug = $user->row('group_slug');
				$user_id    = $user->row('id');
                $user_name  = $user->row('username');
				$email      = $user->row('email');

				$this->CI->session->set_userdata(array(
					'user_id'	 => $user_id,
					'group_id'	 => $group_id,
					'group_name' => $group_name,
					'group_slug' => $group_slug,
					'username'	 => $user_name,
					'email'      => $email
				));

				// Do we rememberme them?
				if ($this->CI->input->post('remember_me') == 'yes')
				{
					$this->_set_remember_me($user_id);
				}

				return $user_id;
			}
		}

		// Looks like the user doesn't exist
		return FALSE;
	}

	/**
	 * Logout
	 *
	 * OMG, logging out like it's 1999
	 *
	 * @return	void
	 */
	public function logout()
	{
		$user_id = $this->CI->session->userdata('user_id');

		$this->CI->session->sess_destroy();

		$this->CI->load->helper('cookie');
		delete_cookie('rememberme');

		$user_data = array(
			'user_id'     => $this->CI->session->userdata('user_id'),
			'remember_me' => ''
		);

		$this->CI->user_model->update_user($user_data);
	}

	/**
	 * Set remember Me
	 *
	 * Updates the remember me cookie and database information
	 *
	 * @param	string unique identifier
	 * @access  private
	 * @return	void
	 */
	private function _set_remember_me($user_id)
	{
		$this->CI->load->library('encrypt');

		$token = md5(uniqid(rand(), TRUE));
		$timeout = 60 * 60 * 24 * 7; // One week

		$remember_me = $this->CI->encrypt->encode($user_id.':'.$token.':'.(time() + $timeout));

		// Set the cookie and database
		$cookie = array(
			'name'		=> 'rememberme',
			'value'		=> $remember_me,
			'expire'	=> $timeout
		);

		set_cookie($cookie);
		$this->CI->user_model->update_user(array('id' => $user_id, 'remember_me' => $remember_me));
	}

	/**
	 * Check remember Me
	 *
	 * Checks if a user is logged in and remembered
	 *
	 * @access	private
	 * @return	bool
	 */
	private function _check_remember_me()
	{
		$this->CI->load->library('encrypt');

		// Is there a cookie to eat?
		if($cookie_data = get_cookie('rememberme'))
		{
			$user_id = '';
			$token = '';
			$timeout = '';

			$cookie_data = $this->CI->encrypt->decode($cookie_data);
			
			if (strpos($cookie_data, ':') !== FALSE)
			{
				$cookie_data = explode(':', $cookie_data);
				
				if (count($cookie_data) == 3)
				{
					list($user_id, $token, $timeout) = $cookie_data;
				}
			}

			// Cookie expired
			if ((int) $timeout < time())
			{
				return FALSE;
			}

			if ($data = $this->CI->user_model->get_user_by_id($user_id))
			{
				// Set session values
				$this->CI->session->set_userdata(array(
					'user_id'    => $user_id,
					'group_id'	 => $data->row('group_id'),
					'group_name' => $data->row('group_name'),
					'group_slug' => $data->row('group_slug'),
					'username'	 => $data->row('username')
				));

				$this->_set_rememberme_me($user_id);

				return TRUE;
			}

			delete_cookie('rememberme');
		}

		return FALSE;
	}

}