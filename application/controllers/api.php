<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Api extends CI_Controller {

	public function index()
	{
		$query  = $this->input->get('q');

		if ($query) 
		{
			$results = file_get_contents('http://gdata.youtube.com/feeds/api/videos?q='.str_replace(' ', '+', $query).'+-cover+-remix&orderby=relevance&start-index=11&max-results=6&v=2&category=Music&alt=json');
			$this->output->set_content_type('application/json');

			$results = json_decode($results, true);

			$decode = $results['feed'];

			$newJSON = '';
			foreach ($decode['entry'] AS $k => $v) {

				$newJSON[] = array(
					'thumbnail' => $v['media$group']['media$thumbnail'][2]['url'],
					'duration'  => $v['media$group']['media$content'][0]['duration'],
					'label'     => $v['media$group']['media$title']['$t'],
					'value'     => $v['media$group']['yt$videoid']['$t']
				);

                print_r($v['media$group']['media$content'][0]);
			}

			// Newly encoded results
			$results = json_encode($newJSON);

			// Send results to the browser / script
			$this->output->set_output($results);
		}
	}
}