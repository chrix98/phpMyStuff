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

class Controller_Files extends Controller_App {

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
	public $auth_required = 'login';

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
		$bm = DebugHelper::func_open($id);

		$userAuth = Auth::instance();
		$user = $userAuth->get_user();

		//	this if/else is to generate info for the template top
		if($userAuth->logged_in()){
			DebugHelper::ilog("user is logged in");
			$userAuthStatus = 'in';
			$userAuthLink = URL::current().'/user/logout';	//	to toggle login status
			$userAuthStatusLabel = __('Logged in');

			$this->template->session_link = '<a href="'.$userAuthLink.'">'.$userAuthStatusLabel.'</a>';
			$this->template->content = __(
				'Welcome back :first_name :last_name',
				array(
					':first_name' 	=> $user->first_name,
					':last_name'	=> $user->last_name
				)
			);
		}else{
			DebugHelper::ilog("user is not logged in");
			$userAuthStatus = 'out';
			$userAuthLink = URL::current().'/user/login';	//	to toggle login status
			$userAuthStatusLabel = __('not logged in');

			$this->template->session_link = '<a href="'.$userAuthLink.'">'.$userAuthStatusLabel.'</a>';
			$this->template->content = __("You're not logged in");
		}


		// set the template title (see Controller_App for implementation)
		$this->template->title = __('Files');
		// create a user
//		$files = ORM::factory('file');
		// This is an example of how to use Kohana pagination
		// Get the total count for the pagination
//		$total = $files->count_all();
		$total = 0;
		DebugHelper::iLog("total files number: ", $total);

		// Create a paginator
		$pagination = new Pagination(array(
			'total_items' => $total,
			'items_per_page' => 10,  // set this to 30 or 15 for the real thing, now just for testing purposes...
			'auto_hide' => true,
			'view' => 'pagination/file'
		));
		// Get the items for the query
		$sort = isset($_GET['sort']) ? $_GET['sort'] : 'created'; // set default sorting direction here
		$dir = isset($_GET['dir']) ? 'DESC' : 'ASC';
/*
		$result = $files->limit($pagination->items_per_page)
			->offset($pagination->offset)
			->order_by($sort, $dir)
			->find_all();
*/
		$result = array();
		// render view
		// pass the paginator, result and default sorting direction
		$this->template->content = View::factory('files/index')
			->set('files', $result)
			->set('paging', $pagination)
			->set('default_sort', $sort);

		DebugHelper::func_close($bm);
	}

} // End Welcome
