<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Filename:	file.php
 * Filetype:	model/
 * Date:	$Date:  $
 * Author:	$Author:  $
 * Revision:	$Rev:  $
 * SVN URL:	$Url:  $
 * ==============================================
 * Description:
 * | handles files and stores their information in the db.
 **/


class Model_File extends ORM
{

	/**
	* holds the user object of the currently logged in user
	*
	* @var mixed
	*/
	protected $user;

//	###################################################################
//	#### main storage root dirs #######################################


	/**
	* the root directory for user files - populated from config
	*
	* @var mixed
	*/
	protected $dir_userfiles;

	/**
	* the root directory for group files - populated from config
	*
	* @var mixed
	*/
	protected $dir_groupfiles;

	/**
	* the root directory for shared files - populated from config
	*
	* @var mixed
	*/
	protected $dir_sharedfiles;

//	###################################################################
//	#### upload dirs ##################################################


	/**
	* root directory for tmp uploads
	*
	* @var mixed
	*/
	protected $dir_uploads;

	/**
	* root directory for avatar uploads
	*
	* @var mixed
	*/
	protected $avatar_uploads;

	/**
	* root directory for picture uploads
	*
	* @var mixed
	*/
	protected $picture_uploads;

	/**
	* root directory for photo uploads
	*
	* @var mixed
	*/
	protected $photo_uploads;

	/**
	* root directory for music uploads
	*
	* @var mixed
	*/
	protected $music_uploads;

	/**
	* root directory for video uploads
	*
	* @var mixed
	*/
	protected $video_uploads;

	/**
	* root directory for document uploads
	*
	* @var mixed
	*/
	protected $document_uploads;

//	###################################################################
//	#### configs ######################################################

	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');


	/**
	* object for holding the configurations from files
	*
	* @var mixed
	*/
	protected $fileconfig;

	/**
	* definition of valid files-object types
	* i.e. User-, Group- or Shared- , avatar, picture, etc...
	* this is used in the filetype field of the files table
	* @var mixed
	*/
	protected $_filetypes = array(
		'UF_AVATAR'		=>	11,
		'UF_PICTURE'	=>	12,
		'UF_PHOTO'		=>	13,
		'UF_MUSIC'		=>	14,
		'UF_VIDEO'		=>	15,
		'UF_DOCUMENT'	=>	16,

		'GF_AVATAR'		=>	21,
		'GF_PICTURE'	=>	22,
		'GF_PHOTO'		=>	23,
		'GF_MUSIC'		=>	24,
		'GF_VIDEO'		=>	25,
		'GF_DOCUMENT'	=>	26,

		'SF_AVATAR'		=>	31,
		'SF_PICTURE'	=>	32,
		'SF_PHOTO'		=>	33,
		'SF_MUSIC'		=>	34,
		'SF_VIDEO'		=>	35,
		'SF_DOCUMENT'	=>	36,
	);

	/**
	* this is a list with the default directory and file names that are
	* allowed to be created in new User home dirs, just in case some
	* numpty puts other files into the User_default dir without knowing
	* the impact.
	*
	* @var mixed
	*/
	protected $valid_default_files = array(
		1	=>	'avatars',
		2	=>	'pictures',
		3	=>	'photos',
		4	=>	'music',
		5	=>	'videos',
		6	=>	'documents',
		99	=>	'index.php',
	);

	/**
	* constructor.
	* - verifies user auth status and redirects if necessary
	* - checks user home dirs and creates where necessary
	*
	* @param mixed $id
	* @return Model_File
	*/
	public function __construct($id=null) {

		$this->fileconfig = Kohana::$config->load('files');
		DebugHelper::dlog( "loaded config: ", $this->fileconfig);

		if(!Auth::instance()->logged_in()) {
			DebugHelper::dlog( "Anonymous access denied");
			Message::add('auth', 'Anonymous users can not upload files');
			Url::redirect(Url::base()."/auth/login");
		}else{

            $this->user = Auth::instance()->get_user();
            $this->check_user_dirs();
        }

		parent::__construct($id);
	}

	public function store_file($filename, $filetype)
	{
		$bm = DebugHelper::func_open($filename, $filetype);
		$ret = false;

		if(!$newAvPath = $this->path($filename, $filetype)) {
			DebugHelper::dlog( "new path failed - probably type invalid.");
		}else{
			DebugHelper::dlog( "newAvPath: ", $newAvPath);

			//	now moving the file to its new destination
			if(! rename($filename, $newAvPath)) {
				DebugHelper::dlog( "renaming file failed; ", $path, $newAvPath);
			}else{
				$ret = $newAvPath;
			}
		}

		DebugHelper::func_close( $bm, $ret);
		return $ret;
	}


	/**
	* checks existence and writability of user homedirs - user homedirs are
	* intended to be accessible from outside the web app to the user for
	* file copying/importing means
	*
	* 1) checks user home dir in config->dir_userfiles and creates if necessary
	* 2) checks for default dirs/and files by reading content from "user_default"
	* 		directory and copies those items if they're listed in
	* 		@var valid_default_files
	* 3) changes permissions to ALL WORLD on the directories so ftp/samba servers can write to it
	*
	*/
	private function check_user_dirs()
	{
		$bm = DebugHelper::func_open( null);
		$dir_userfiles = $this->fileconfig->get('dir_userfiles');
		$home_dir = $this->fileconfig->get('dir_userfiles')
                        .$this->fileconfig->get('prefix_dir_user')
                        .$this->user->id."/";

		if(!file_exists($home_dir)) {
			DebugHelper::dlog( "homedir doesn't exist ...");
			if(! mkdir($home_dir)) {
				DebugHelper::dlog( "failed to create homedir, check directory permissions");
				$ret = false;
			}else{
				DebugHelper::dlog( "homedir created OK");
			}
		}else{
			DebugHelper::dlog( "home dir exists OK");
		}

		if($udd = opendir($dir_userfiles."user_default")) {
			while ((($defaultfile = readdir($udd)) !== false)	) {
				if(in_array($defaultfile, array_values($this->valid_default_files))) {

					if(is_dir($dir_userfiles."user_default/".$defaultfile) && !is_dir($home_dir."/".$defaultfile)) {

						mkdir($home_dir."/".$defaultfile);
						chmod($home_dir."/".$defaultfile, 0777);
						copy(
							$dir_userfiles."user_default/".$defaultfile."/index.php",
							$home_dir."/".$defaultfile."/index.php" );

					}elseif(!file_exists($home_dir."/".$defaultfile)){

						if(
							copy($dir_userfiles."user_default/".$defaultfile,	$home_dir."/".$defaultfile)	)
						{
							DebugHelper::dlog(
								"default file :defaultfile copied ok to :homedir ",
									array('defaultfile'=>$defaultfile, 'homedir'=>$home_dir));
						}
					}
				}
			}
		}

		DebugHelper::func_close( $bm, true);
	}


	/**
	* given the name of the file and the @var type of the file, this computes
	* (based on config and user) and returns the full unix path for the
	* target file name
	*
	* @param string $filename
	* @param int $type
	* @return string $ret - the new path/filename of the file
	*/
	private function path($filename, $type)
	{
		$bm = DebugHelper::func_open( $filename, $type);
		$filename 	= basename($filename);
		$rootdir 	= '';

		if(!$this->filetype_valid($type)) {
			DebugHelper::wlog( "invalid file type: ", $type);
			return false;
		}else{
			$type = $this->filetypes($type);	//	comes in as text indicating array index
		}

		switch(substr($type,0,1)) {
			case 3: // SF
				$rootdir 	.= 	$this->fileconfig->get('dir_sharedfiles');
				//$rootdir 	.= 	$this->fileconfig->get('prefix_dir_user')."/";
			break;
			case 2:	//	GF
				$rootdir 	.= 	$this->fileconfig->get('dir_groupfiles');
				$rootdir 	.= 	$this->fileconfig->get('prefix_dir_group');
			break;
			case 1:	//	UF
			default:
				$rootdir 	.= 	$this->fileconfig->get('dir_userfiles');
				$rootdir 	.= 	$this->fileconfig->get('prefix_dir_user');
			break;
		}

		switch($type) {
			//	avatars
			case 11:
			case 21:
			case 31:
				$rootdir	.=	Auth::instance()->get_user()->id.'/';
				$rootdir	.=	'avatars/';
			break;
			//	pictures
			case 12:
			case 22:
			case 32:
				$rootdir	.=	Auth::instance()->get_user()->id.'/';
				$rootdir	.=	'pictures/';
			break;
			//	photos
			case 13:
			case 23:
			case 33:
				$rootdir	.=	Auth::instance()->get_user()->id.'/';
				$rootdir	.=	'photos/';
			break;
			//	music
			case 14:
			case 24:
			case 34:
				$rootdir	.=	Auth::instance()->get_user()->id.'/';
				$rootdir	.=	'music/';
			break;
			//	videos
			case 15:
			case 25:
			case 35:
				$rootdir	.=	Auth::instance()->get_user()->id.'/';
				$rootdir	.=	'videos/';
			break;
			//	documents
			case 16:
			case 26:
			case 36:
				$rootdir	.=	Auth::instance()->get_user()->id.'/';
				$rootdir	.=	'documents/';
			break;
		}

		$ret = $rootdir.$filename;

		DebugHelper::func_close( $bm, $ret);
		return $ret;
	}


	/**
	* returns the list of filetypes or a single filetype matching name of input
	*
	* @param mixed $which
	* @return mixed $ret
	*/
	public function filetypes($which=null)
	{
		$bm = DebugHelper::func_open( $which);

		$ret = isset($this->_filetypes[$which]) ? $this->_filetypes[$which] : $this->_filetypes;

		DebugHelper::func_close( $bm, $ret);
		return $ret;
	}


	/**
	* compares the specified type of file to the list of valid types
	* and returns true or false depending whether type exists in list
	*
	* @param mixed $type
	*/
	public function filetype_valid($type)
	{
		$bm = DebugHelper::func_open($type);

		if(!in_array($type, array_keys($this->_filetypes))) {
			DebugHelper::wlog( "disallowed file type: ", $type);
			$ret = false;
		}else{
			$ret = true;
		}

		DebugHelper::func_close( $bm, $ret);
		return $ret;
	}
}
?>
