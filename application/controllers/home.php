<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Home extends MY_Controller {

	public function index()
	{
		$this->parser->parse('home/index', array('site' => array(
			'title' => 'RhymeUp'
		)));
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */