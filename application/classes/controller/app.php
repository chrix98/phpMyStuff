<?php defined('SYSPATH') or die('No direct script access.');

class Controller_App extends Useradmin_Controller_App {

	public $userAuthLink = null;
	public $userAuthStatus = null;
	public $userAuthStatusLabel = null;

	function user_verify($redirToLogin=false)
	{
		$args = func_get_args();
		$bm = DebugHelper::func_open($args);

		$userAuth = Auth::instance();
		$user = $userAuth->get_user();

		DebugHelper::dlog('Auth Required: ' ,$this->auth_required);

		if($userAuth->logged_in($this->auth_required))	{
			DebugHelper::ilog("user is logged in");
			$this->userAuthStatus 		= 'in';
			$this->userAuthLink 		= 'user/logout';	//	to toggle login status
			$this->userAuthStatusLabel 	= __('Logged in');
		}else{
			DebugHelper::ilog("user is not logged in");
			$this->userAuthStatus 		= 'out';
			$this->userAuthLink 		= 'user/login';	//	to toggle login status
			$this->userAuthStatusLabel 	= __('not logged in');
		}

		if($redirToLogin!==false && $this->userAuthStatus=='out')	{
			URL::redirect($this->userAuthLink);
		}
		DebugHelper::func_close($bm);
	}

	/**
	 *	this function only replicates what Useradmin_Controller_App::before and ::after would do if $auto_render was enabled
	 *	this is implemented to be able to not have the default template running but init() it manually for when the request is not ajax
	 */
	protected function auto_render_before()
	{
		$args = func_get_args();
		$bm = DebugHelper::func_open($args);

			// only load the template if the template has not been set..
			$this->template = View::factory($this->template);
			// Initialize empty values
			// Page title
			$this->template->title = '';
			// Page content
			$this->template->content = '';
			// Styles in header
			$this->template->styles = array();
			// Scripts in header
			$this->template->scripts = array();
			// ControllerName will contain the name of the Controller in the Template
			$this->template->controllerName = $this->request->controller();
			// ActionName will contain the name of the Action in the Template
			$this->template->actionName = $this->request->action();
				// next, it is expected that $this->template->content is set e.g. by rendering a view into it.
		DebugHelper::func_close($bm);
	}

	/**
	 *	this function only replicates what Useradmin_Controller_App::before and ::after would do if $auto_render was enabled
	 *	this is implemented to be able to not have the default template running but init() it manually for when the request is not ajax
	 */
	protected function auto_render_after()
	{
		$args = func_get_args();
		$bm = DebugHelper::func_open($args);
		/*
		 *	we dont really need this. the moment we use the auto_render_before we're kicking off the template so we may as
		 *	well leave the template (parent) after() function take care of it by (manyally) turning on auto_render.
		 *	since we're manually turning on auto_render during the cause of execution we can of course not do that and use
		 *	this function instead - take the below (copied from parent::after()) as an example
		$styles = array(
			'css/style.css' => 'screen'
		);
		$scripts = array();
		$this->template->styles = array_merge($this->template->styles, $styles);
		$this->template->scripts = array_merge($this->template->scripts, $scripts);

		// Display profile if its enabled and request by query profile
		$this->template->profile = (isset($_REQUEST['profile']) && Kohana::$profiling)?"<div id=\"kohana-profiler\">".View::factory('profiler/stats')."</div>":"";

		// Assign the template as the request response and render it
		$this->response->body($this->template);
		 */
		DebugHelper::func_close($bm);
	}

	protected function ajax_response($response)
	{
		//$args = func_get_args();
		$bm = DebugHelper::func_open($response);

		$response = json_encode($response);

		//	TODO -c RETHINK -p MEDIUM	: lets rethink where utf8 encoding should be done in ajax requests if at all
		$response = utf8_encode($response);

		// Assign the template as the request response and render it
		$this->response->body($response);
		DebugHelper::func_close($bm);
	}
}
