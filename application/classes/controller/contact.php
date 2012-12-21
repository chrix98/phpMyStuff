<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Filename:    files.php
 * Filetype:	controller
 * Date:		$Date: 2011-12-08 01:27:18 +0000 (Thu, 08 Dec 2011) $
 * Author:		$Author: chris $
 * Revision:	$Rev: 10 $
 * SVN URL:		$Url:  $
 * ==============================================
 * Description:

 **/

class Controller_Contact extends Controller_App {

	/**
	* this is the object view object, within this controllers perspective its the name
	* of the object representing the view data. you can give it sub objects .
	* in the definition it gets assigned the string val of the views/*.php file,
	* it then acts as a storage object to assign the view data and loads the corresponding
	* view file for output.
	*
	* @var mixed
	*/
	public $template = 'template/default';

	public $log;

	/**
	 * Controls access for the whole controller, if not set to FALSE we will only allow user roles specified.
	 * See Controller_App for how this implemented.
	 * Can be set to a string or an array, for example array('login', 'admin') or 'login'
	 */
	public $auth_required = false;

	/** Controls access for separate actions
	 *
	 * See Controller_App for how this implemented.
	 *
	 * Examples:
	 * 'adminpanel' => 'admin' will only allow users with the role admin to access action_adminpanel
	 * 'moderatorpanel' => array('login', 'moderator') will only allow users with the roles login and moderator to access action_moderatorpanel
	 */
	public $secure_actions = array('action_edit' => 'admin'); // array( action => role)

	public function action_index()
	{
		$id = $this->request->param('id');
		$bm = DebugHelper::func_open(__METHOD__ , __LINE__, $id);

		$userAuth = Auth::instance();
		$user = $userAuth->get_user();

		//	this if/else is to generate info for the template top
		if($userAuth->logged_in()){
			DebugHelper::ilog(__METHOD__, __LINE__, "user is logged in");
			$userAuthStatus = 'in';
			$userAuthLink = URL::current().'/user/logout';	//	to toggle login status
			$userAuthStatusLabel = __('Logged in');

			$template__showContactDetailsPost = true;
			$template__showContactDetailsPhone = true;
		}else{
			DebugHelper::ilog(__METHOD__, __LINE__, "user is not logged in");
			$userAuthStatus = 'out';
			$userAuthLink = URL::current().'/user/login';	//	to toggle login status
			$userAuthStatusLabel = __('not logged in');

			$template__showContactDetailsPost = false;
			$template__showContactDetailsPhone = false;
			//$this->template->content = __("You're not logged in");
		}

		$template__session_link = '<a href="'.$userAuthLink.'">'.$userAuthStatusLabel.'</a>';

		$view = View::factory("template/default/contact");
		if(!empty($_POST)) {
			$msg = "<div class='confirm'>". __('Your message has been sent')."</div>";
			$view->set('msg', $msg);
		}else{

			$view->set('showform', true);
				//->set('users', $result)
				//->set('paging', $pagination)
				//->set('default_sort', $sort);
		}


		$view->set('session_link' , $template__session_link);
		$view->set('showContactDetailsPost', $template__showContactDetailsPost);
		$view->set('showContactDetailsPhone', $template__showContactDetailsPhone);
		
		$this->template->content = $view;

		DebugHelper::func_close(__METHOD__ , __LINE__ , $bm);
	}

} // End Welcome
