<?php defined('SYSPATH') or die('No direct script access.');
/**
 * @auth controller
 */
class Controller_Auth_Auth extends Controller_Base {

	public function action_index()
	{
		Request::instance()->redirect('');
	}
 
	public function action_signin()
	{
		// Redirect if user is logged in
		Auth::instance()->logged_in() AND Request::instance()->redirect('');		

		$this->template->title = __('Sign in');
		$this->template->content = View::factory('page/auth/signin' . ($this->request->is_mobile ? '_mobile' : ''))
			->bind('errors', $errors);

		// If successfull login then redirect to home page
		ORM::factory('user')->login($_POST) AND Request::instance()->redirect('');

		$errors = $_POST->errors('signin');

		$_POST = $_POST->as_array();
	}
	
	public function action_signup()
	{
		// Redirect if user is logged in
		Auth::instance()->logged_in() AND Request::instance()->redirect('');		

		$this->template->title = __('Sign up'); 
		$this->template->content = View::factory('page/auth/signup')
			->bind('errors', $errors);

		// If successful signup then redirect to login page
		ORM::factory('user')->signup($_POST) AND Request::instance()->redirect('');			
	 
		$errors = $_POST->errors('signup');

		$_POST = $_POST->as_array();
	}

	public function action_profile()
	{
		// Redirect if user is logged in
		!Auth::instance()->logged_in() AND Request::instance()->redirect('auth/signin');
		
		$this->template->title = __('Profile');
		$this->template->content = View::factory('page/auth/profile')
			->bind('errors', $errors);

		// Update logged in user details, if successfull then redirect to profile page
		Auth::instance()->get_user()->update($_POST) AND Request::instance()->redirect('auth/profile');

		$errors = $_POST->errors('profile');
	}

	public function action_reset_password()
	{
		$this->template->title = __('Reset password');
		$this->template->content = View::factory('page/auth/reset_password')
			->bind('errors', $errors)
			->bind('message_sent', $message_sent);

		// Try send reset passwork link in email
		if ( ORM::factory('user')->reset_password($_POST))
		{
			// Store the result in session
			Session::instance()->set('message_sent', TRUE);

			// Redirect user to prevent refresh on POST request
			$this->request->redirect(URL::site($this->request->uri(array('action' => 'reset_password'))));
		}

		// Get and delete the message_sent status from session
		$message_sent = Session::instance()->get('message_sent', FALSE) AND Session::instance()->delete('message_sent');

		$errors = $_POST->errors('reset_password');
	}

	public function action_confirm_reset_password()
	{
		$this->template->title = __('Reset password');
		$this->template->content = View::factory('page/auth/confirm_reset_password')
			->set('token', @$_REQUEST['auth_token'])
			->bind('errors', $errors);
		
		$id = (int) Arr::get($_REQUEST, 'id');

		$token = (string) Arr::get($_REQUEST, 'auth_token');

		ORM::factory('user', $id)->find()->confirm_reset_password($_POST, $token);

		$errors = $_POST->errors('confirm_reset_password');
	}

	public function action_signout()
	{
		Auth::instance()->logout();

		Request::instance()->redirect('');		
	}

	public function action_service()
	{
		$this->template->title = __('Auth service');

		$this->template->content = View::factory('page/auth/service_mobile');
	}

} // End Controller_Auth_Auth