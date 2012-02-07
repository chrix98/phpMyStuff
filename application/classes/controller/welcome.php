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
		LOG::instance()->add(LOG::DEBUG, 'entered with : id: '.var_export($id,1));

		$xtc = $this->request->param('xtc');
		LOG::instance()->add(LOG::DEBUG, 'entered with : xtc: '.var_export($xtc,1));

		$this->log = LOG::instance();

		$this->log->add(LOG::DEBUG, __METHOD__.": entered with args: ");
		$this->log->add(LOG::INFO, __METHOD__.": info entry: ");

		//Message::add('info', 'hello, world!');

		$userAuth = Auth::instance();
		$user = $userAuth->get_user();
		$userDataModel = Model_Auth_User::factory('User', $user);
		$userData = $userDataModel->as_array();

		$userAuthStatus = $userAuth->logged_in() ? 'out' : 'in';
		$userAuthStatusLabel = "Log".$userAuthStatus;	// todo 2 -o chris -c msgbases: fix up


		switch($userAuthStatus)
		{
			case 'in':
				$this->template->session_link = '<a href="'.URL::current().'/user/log'.$userAuthStatus.'">'.$userAuthStatusLabel.'</a>';

				$this->template->content = __("You're not logged in");
			break;
			case 'out':
				$this->template->session_link = '<a href="'.URL::current().'/user/log'.$userAuthStatus.'">'.$userAuthStatusLabel.'</a>';
				$this->template->content = __('Welcome back :first_name :last_name', array(':first_name' => $userData['first_name'], ':last_name'=>$userData['last_name']));
			default:
			break;
		}

	}

} // End Welcome
