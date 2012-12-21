<?php defined('SYSPATH') or die('No direct access allowed.');

define("PROVIDER_FILE", 		1);
define("PROVIDER_GRAVATAR", 	2);

class Model_Avatar extends ORM {

	protected $_belongs_to = array(
	);

	protected $_providers = array(
		'file'			=>	1
		#'gravatar'		=>	2,
	);

	protected $_avatar_types = array(
		'AVATAR_USER'	=>	1,
		'AVATAR_GROUP'	=>	2,
		'AVATAR_SHARED'	=>	3,	//	unsure if and what for this one could be useful
	);

	protected $_avatartype;

	protected $_filetype;
	protected $_pertain_table;
	protected $_pertain_id;

	protected $_filemodel;

	protected $_fileconfig;

	public function __construct($id=null)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__, $id);
		$ret = false;

		parent::__construct($id);

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	/************************************
	* crud wrappers
	**/

	/**
	* wrapper for creating avatars.
	*
	* @param mixed $sAvType	the avatar type, i.e. AVATAR_USER, AVATAR_GROUP, AVATAR_SHARED
	* @param array $oAvatar	the avatar data , index being 'provider_id', provider_data
	*/
	public function create_avatar($sAvType, $oAvatar)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__,$oAvatar);
		$ret = false;

		$this->set_avatartype($sAvType);
		$this->avatar_create($oAvatar);

		switch($oAvatar['provider']) {
			case 'file':
				$ret = $this->add_file();
			break;
			case 'gravatar':
				$ret = 'method not implemented.';
				$this->add_gravatar($oAvatar);
				return false;
			break;
		}

		//	now updating avatar provider data (i.e. reference)
		$this->avatar_update(
			array(
				'provider_data'	=>	$ret,
			)
		);

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}


	public function get_avatar_url()
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__,null);
		$ret = false;

		DebugHelper::dlog(__METHOD__, __LINE__, "provider id: ", $this->provider_id);

		switch($this->provider_id) {
			case '1':
			case 1:
				$ret = Url::base()."avatar/get/".substr(md5($this->provider_data), 10,15);
			break;
			case '2':
			case 2:
				$ret = $this->provider_data;
			break;
		}

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	public function get_avatar_file($hash)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__, $hash);
		$ret = false;

		if(!$avatar = $this->find_by_hash($hash)) {
			DebugHelper::dlog(__METHOD__, __LINE__, "failed to find avatar by hash");

			DebugHelper::func_close(__METHOD__, __LINE__, $bm, $avatar);
			return $avatar;
		}

		switch($avatar->provider_id)
		{
			case '1':	//	file
				$ret = $this->get_file($avatar);
			break;
			case '2':
				$ret = $this->get_gravatar($avatar);
			break;
		}



		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}


	/************************************
	* cruds
	**/

	private function avatar_create($oAvatar)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__, $oAvatar);
		$ret = false;

		$this->owner_id 		= $this->_pertain_id;
		$this->owner_table 		= $this->_pertain_table;

		$this->created 			= gmdate("Y-m-d H:i:s");
		$this->provider_id 		= $this->providers($oAvatar['provider']);
		#$this->provider_data	= null;

		if(	$this->save() ) {
			DebugHelper::dlog(__METHOD__, __LINE__, "saved OK");
			$ret = $this->id;
		}else{
			DebugHelper::dlog(__METHOD__, __LINE__, "saved FAIL");
		}

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	private function avatar_update($oAvatar)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__,$oAvatar);
		$ret = false;
		$set = false;

		// todo 2 -o chris -c fixme : implement rules or some form of checking
		foreach ($oAvatar as $key => $value) {
			if(!empty($value)) {
				$this->$key = $value;
				$set = true;
			}
		}

		if($set){
			try
			{
				$this->save();
			}catch (ORM_Validation_Exception $e)
			{
				DebugHelper::dlog(__METHOD__, __LINE__, "Could not save avatar object: ", $e->errors('update'));
			}
		}
		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	/************************************
	* provider helpers
	**/

	private function add_file()
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__, null);
		$ret = false;

		$file = $_FILES['avatar_data_file'];
		DebugHelper::dlog(__METHOD__, __LINE__, "...processing file: ", $file);

		// check if there is an uploaded file
		if (Upload::valid($file))
		{
			$filename = uniqid().Inflector::humanize($file['name']);

			$path = Upload::save($file, 'avatars/'.$filename);
			DebugHelper::dlog(__METHOD__, __LINE__, "uploaded file path: ", $path);
			if ($path)
			{
				//	we only load the model if we really need it ...
				$this->_filemodel = ORM::factory('file');	//	needs to run before add_avatar()

				DebugHelper::dlog(__METHOD__, __LINE__, "file type: ", $this->_filetype);

				if($stored_file = $this->_filemodel->store_file($path, $this->_filetype)) {
					DebugHelper::dlog(__METHOD__, __LINE__, "stored file: ", $stored_file);

					//	file object definitions
					$this->_filemodel->filetype			= $this->_filemodel->filetypes($this->_filetype);
					$this->_filemodel->pertain_table 	= 'avatars';
					$this->_filemodel->pertain_id		= $this->id;
					$this->_filemodel->user_id			= Auth::instance()->get_user()->id;	//	as in "owner"

					$this->_filemodel->filename_local	= basename($stored_file);
					$this->_filemodel->filename_user	= str_replace(DOCROOT, "", $file['name']);

//					$this->_filemodel->created 		= gmdate("Y-m-d H:i:s");
//					$this->_filemodel->updated 		= gmdate("Y-m-d H:i:s");

					$this->_filemodel->size			= $file['size'];
					$this->_filemodel->mimetype		= $file['type'];

					//	storing file data in files object
					if(!$this->_filemodel->save()) {
						DebugHelper::wlog(__METHOD__, __LINE__, "failed to store avatar file");
						// todo 2 -o chris -c general : make this error interactive
					}else{
						$ret = $this->_filemodel->id;	//	this is the ID of the files object
						DebugHelper::dlog(__METHOD__, __LINE__, "file saved: ", $ret);
					}

				}else{
					DebugHelper::dlog(__METHOD__, __LINE__, "can not store file in new location");
				}

			}else{
				DebugHelper::dlog(__METHOD__, __LINE__, "moving avatar file to tmp location failed.", $path, $filename);
			}
		}

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	private function get_file($oAvatar)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__, $oAvatar);
		$ret = false;
		$this->_fileconfig = Kohana::$config->load('files');

		$file = ORM::factory('file', $oAvatar->provider_data);

		if($file->filename_local && $file->filename_user && $file->mimetype)
		{
			$rootdir = '';

			switch((int)substr($file->filetype,0,1)) {
				case 1:	//	UF	-	user file
					$rootdir .= $this->_fileconfig->get('dir_userfiles');
					$rootdir .= $this->_fileconfig->get('prefix_dir_user');
					$rootdir .= $oAvatar->owner_id;
					$rootdir .= "/";
				break;
				case 2: //	GF	-	group file
					$rootdir .= $this->_fileconfig->get('dir_groupfiles');
					$rootdir .= $this->_fileconfig->get('prefix_dir_group');
					$rootdir .= $file->pertain_id;
					$rootdir .= "/";
				break;
				case 3: // SF 	-	shared file
					$rootdir .= $this->_fileconfig->get('dir_sharedfiles');
					$rootdir .= $file->pertain_id;
					$rootdir .= "/";
				break;
			}

			switch((int)substr($file->filetype,1,1)) {
				case 1:	//	avatar
					$rootdir .= 'avatars/';
				break;
				case 2:	//	picture
					$rootdir .= 'pictures/';
				break;
				case 3:	//	photo
					$rootdir .= 'photos/';
				break;
				case 4:	//	music
					$rootdir .= 'music/';
				break;
				case 5:	//	video
					$rootdir .= 'videos/';
				break;
				case 6:	//	document
					$rootdir .= 'documents/';
				break;
			}

			if(!empty($rootdir)) {
				$ret = array(
					'provider_id'		=>	$oAvatar->provider_id,
					'provider_data'		=>	$oAvatar->provider_data,
					'filename_local'	=>	$rootdir.$file->filename_local,
					'filename_user'		=>	$file->filename_user,
					'options'			=>	array(
						'mime_type'			=>	$file->mimetype,
					)
				);
			}
		}

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	private function add_gravatar()
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__,null);
		$ret = false;

		DebugHelper::elog(__METHOD__, __LINE__, "not implemented yet");

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	/************************************
	* helpers
	**/

	public function set_avatartype($sAvType)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__,$sAvType);
		$ret = false;

		switch($this->avatar_types($sAvType))
		{
			case 1:	//	user
				$this->_filetype		= 'UF_AVATAR';
				$this->_avatartype		= 1;
				$this->_pertain_table	= 'users';
				$this->_pertain_id		= Auth::instance()->get_user();
				$ret = 1;
			break;
			case 2:
				$this->_filetype		= 'GF_AVATAR';
				$this->_avatartype		= 2;
				$this->_pertain_table	= 'groups';
				$this->_pertain_id		= null;	//	// todo 2 -o chris -c fixme: implement groups
				$ret = 2;
			break;
			case 3:								// todo 2 -o chris -c fixme : verify shared
				$this->_filetype		= 'SF_AVATAR';
				$this->_avatartype		= 3;
				$this->_pertain_table	= 'users';
				$this->_pertain_id		= $this->user_id;
				$ret = 3;
			break;
			case false:
				DebugHelper::dlog(__METHOD__, __LINE__, "avatar type is invalid.");
				$ret = false;
		}

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	public function avatar_types($which=null, $value=null)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__,$which);
		$ret = false;

		if(empty($which)){
			$ret = $this->_avatar_types;
		}elseif(isset($this->_avatar_types[$which])){
			$ret = $this->_avatar_types[$which];
		}elseif(!isset($this->_avatar_types[$which]) && !empty($which) && !empty($val)){
			$this->_avatar_types[$which] = $val;
			$ret = $this->_avatar_types[$which];
		}else{
			$ret = false;
		}

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	public function providers($which=null)
	{
		$bm = DebugHelper::func_open(__METHOD__, __LINE__,$which);
		$ret = false;

		$ret = isset($this->_providers[$which]) ? $this->_providers[$which] : $this->_providers;

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}

	public function find_by_hash($hash) {
		$bm = DebugHelper::func_open(__METHOD__, __LINE__,$hash);
		$ret = false;

		$results = $this->find_all()->as_array();
		DebugHelper::dlog(__METHOD__, __LINE__, "results: ", $results);

		foreach($results as $row=>$data) {
			if(substr(md5($data->id), 10, 15) == $hash) {
				$ret = $data;
				DebugHelper::dlog(__METHOD__, __LINE__, "we have a match: ", $ret);
				continue;
			}
		}

		DebugHelper::func_close(__METHOD__, __LINE__, $bm, $ret);
		return $ret;
	}
//	public function filetypes($which=null){
//		if(!isset($file_model))
//			$file_model = ORM::factory('file');
//	}
} // End Avatar Model