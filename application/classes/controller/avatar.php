<?php
/**
 * Filename:	avatar.php
 * Filetype:	controller
 * ==============================================
 * Description:

 **/

class Controller_Avatar extends Controller {

	public $auth_required = false; // FALSE | string | array

	public $secure_actions = array(
		'action_edit' => 'login',
	); // array( action => role)

	protected $_fileconfig;

	public function action_get()
	{
		$hash = $this->request->param('id');
		$bm = DebugHelper::func_open($hash);
		$ret = false;
		$avatar = ORM::factory('avatar');

		if(!$ret = $avatar->get_avatar_file($hash)) {
			DebugHelper::dlog("failed to find avatar file for hash: ", $hash);

		}else{
			switch($ret['provider_id'])
			{
				case '1':	//	file
					$this->response->send_file(
						$ret['filename_local'],
						$ret['filename_user'],
						array(
							'mime_type'	=> $ret['options']['mime_type'],
							'inline'	=> true,
						)
					);
				break;
				case '2':
					$this->request->redirect($ret['provider_data']);
					// todo 2 -o chris -c general : verify/test/fix this one
				break;
			}
		}


		DebugHelper::func_close($bm);
	}
}
