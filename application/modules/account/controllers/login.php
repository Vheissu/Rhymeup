<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Login extends MY_Controller {

	public function index()
	{
		if (! $this->input->post('username') OR ! $this->input->post('password'))
		{
			// Load the login template
			$this->parser->parse('account/login');
		}
	}
}