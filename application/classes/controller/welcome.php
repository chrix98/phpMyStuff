<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Filename:
 * Filetype:	class/controller/model/view/
 * Date:		$Date: 2011-12-08 01:27:18 +0000 (Thu, 08 Dec 2011) $
 * Author:		$Author: chris $
 * Revision:	$Rev: 10 $
 * SVN URL:		$Url:  $
 * ==============================================
 * Description:

 **/

class Controller_Welcome extends Controller_App {

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

	public $auth_required = false; // FALSE | string | array
	public $secure_actions = array('action_edit' => 'admin'); // array( action => role)

	public function action_index()
	{
		$id = $this->request->param('id');
		$bm = DebugHelper::func_open($id);

		$userAuth = Auth::instance();
		$user = $userAuth->get_user();

		if($userAuth->logged_in()){
			DebugHelper::ilog("user is logged in");
			$userAuthStatus = 'in';
			$userAuthLink = URL::current().'/user/logout';	//	to toggle login status
			$userAuthStatusLabel = __('Logged in');
		}else{
			DebugHelper::ilog("user is not logged in");
			$userAuthStatus = 'out';
			$userAuthLink = URL::current().'/user/login';	//	to toggle login status
			$userAuthStatusLabel = __('not logged in');
		}


		switch($userAuthStatus)
		{
			case 'out':
				$this->template->session_link = '<a href="'.$userAuthLink.'">'.$userAuthStatusLabel.'</a>';

				$this->template->content = __("You're not logged in");
			break;
			case 'in':
				$this->template->session_link = '<a href="'.$userAuthLink.'">'.$userAuthStatusLabel.'</a>';
				$this->template->content = __(
					'Welcome back :first_name :last_name',
					array(
						':first_name' 	=> $user->first_name,
						':last_name'	=> $user->last_name
					)
				);
			default:
			break;
		}

		DebugHelper::func_close($bm);
	}

} // End Welcome
