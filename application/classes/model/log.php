<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Filename:	log.php
 * Filetype:	model/
 * Date:	$Date:  $
 * Author:	$Author:  $
 * Revision:	$Rev:  $
 * SVN URL:	$Url:  $
 * ==============================================
 * Description:
 * | handles files and stores their information in the db.
 **/


class Model_Log extends ORM
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

//	###################################################################
//	#### configs ######################################################

	protected $_created_column = array('column' => 'created', 'format' => 'Y-m-d H:i:s');
	protected $_updated_column = array('column' => 'updated', 'format' => 'Y-m-d H:i:s');


/*	// Log message levels - Windows users see PHP Bug #18090
*	const EMERGENCY = LOG_EMERG;    // 0
*	const ALERT     = LOG_ALERT;    // 1
*	const CRITICAL  = LOG_CRIT;     // 2
*	const ERROR     = LOG_ERR;      // 3
*	const WARNING   = LOG_WARNING;  // 4
*	const NOTICE    = LOG_NOTICE;   // 5
*	const INFO      = LOG_INFO;     // 6
*	const DEBUG     = LOG_DEBUG;    // 7
*	const STRACE    = 8;
*/

	/**
	* constructor.
	* - verifies user auth status and redirects if necessary
	* - checks user home dirs and creates where necessary
	*
	* @param mixed $id
	* @return Model_File
	*/
	public function __construct($id=null)
	{
		parent::__construct($id);
	}

}
