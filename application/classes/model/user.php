<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_User extends Useradmin_Model_User {


	public function rules()
	{
		return parent::rules() + array(
			'username'	=> array(
				array('min_length', array(':value', 1))
			),
			'first_name' => array(
				array('not_empty')
			),
			'last_name' => array(
				array('not_empty')
			)
		);
	}

	public function filters()
	{
		return parent::filters() + array(
			'avatar_id' => array(
				array(array($this,'upload_avatar'))
			)
		);
	}

	/**
	 * Labels for fields in this model
	 *
	 * @return array Labels
	 */
	public function labels()
	{
		return parent::labels() + array(
			'first_name'	=> __('first name'),
			'last_name'		=> __('last name'),
			//'avatar_id'		=> 'avatar?',
		);
	}


	public function upload_avatar($avatarInput)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__, $avatarInput);
		$ret = false;

		//	this input is an array and somehow the index names get foobared
		foreach($avatarInput as $k=>$v)
			$newAvatarInput[str_replace("\'", '', $k)] = $v;

		$avatarInput = $newAvatarInput;


		$avatar = ORM::factory('avatar');
		$ret= $avatar->create_avatar('AVATAR_USER', $avatarInput);

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

//	public function get_by_field($field,$value)
//	{
//		$object = $this->where($field,'=',$value)->find();
//	}
}