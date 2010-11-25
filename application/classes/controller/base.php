<?php defined('SYSPATH') or die('No direct script access.');
  
class Controller_Base extends Controller_Template {
 
	public $template = 'master_page';

	protected $auth_required = FALSE;

	public function before()
	{
		// Secure this controller
		$this->authenticate();
		
		// Detect mobile environment from HTTP HOST
		$this->request->is_mobile = !!strstr(URL::base(TRUE, TRUE), '//mobile.');

		// Set the master template
		$this->request->is_mobile AND $this->template .= '_mobile';

		parent::before();

		$this->template->title =
		$this->template->content = '';

		// Set the stylesheet and javascript paths
		$this->template->styles = Kohana::config('assets.' . ($this->request->is_mobile ? 'mobile' : 'default') . '.style');
		$this->template->scripts = Kohana::config('assets.' . ($this->request->is_mobile ? 'mobile' : 'default') . '.script');

		// If the media module is enabled then run the scripts through the compressors
		if (class_exists('Media')) 
		{
			$this->template->styles = Media::instance()->styles( $this->template->styles);
			$this->template->scripts = Media::instance()->scripts( $this->template->scripts);
		}
	}

	public function after()
	{
		// Ajax responses should only return the template content.
		// Exclude for mobile as jquery-mobile requires the entire page template 
		$ajax_response = (Request::$is_ajax AND !$this->request->is_mobile);

		if ($ajax_response OR $this->request !== Request::instance())
		{
			// Use the template content as the response
			$this->request->response = $this->template->content;
		} 
		else 
		{
			parent::after();

			// Add profiler information to template content
			$this->request->response = $this->profiler( $this->request->response );
		}
	}

	private function authenticate()
	{
		// If this page is secured and the user is not logged in, then redirect to the signin page
		if ( $this->auth_required !== FALSE AND Auth::instance()->logged_in($this->auth_required) === FALSE)
		{
			$this->request->redirect( URL::site( Route::get('auth', array('action' => 'signin'))));
		}
	}

	private function profiler($content)
	{
		// Load the profiler
		$profiler = Profiler::application();

		list($time, $memory) = array_values( $profiler['current'] );

		// Prep the data
		$data = array(
			'{memory_usage}' => Text::bytes($memory),
			'{execution_time}' => round($time, 3).'s',
			'{profiler}' => Kohana::$environment === Kohana::DEVELOPMENT ? View::factory('profiler/stats') : ''
		);

		// Replace the placeholders with data
		return strtr( (string) $content, $data);
	}

} // End Controller_Base
