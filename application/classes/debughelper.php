<?php defined('SYSPATH') or die('No direct access allowed.');
/**
 * Filename:	debug.php
 * Filetype:	class/controller/model/view/
 * Date:	$Date:  $
 * Author:	$Author:  $
 * Revision:	$Rev:  $
 * SVN URL:	$Url:  $
 * ==============================================
 * Description:
 * |
 **/

class DebugHelper {

	public function __construct(){

	}

	public static function func_open()
	{
		if(self::enabled() === false) return;
		$xargs = func_get_args();
		$args = $xargs;

		$method = array_shift($args);
		$line	= array_shift($args);
		$parms	= $args;

		self::dlog($method, $line, "===================================================");
		self::dlog($method, $line, "========== entered".(!empty($parms)? " with args: " : ""), $parms);
		$benchmark = null;

		// Be sure to only profile if it's enabled
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$cat = preg_match("/([a-zA-Z0-9_-])::(.+)/", $method, $matches);
			$benchmark = Profiler::start($matches[1], $method);
		}

		return $benchmark;
	}

	public static function func_close()
	{
		$xargs = func_get_args();
		$args = $xargs;

		$method 	= array_shift($args);
		$line		= array_shift($args);
		$benchmark	= array_shift($args);
		$parms 		= $args;

		if(self::enabled() === false) return;
		// stop the benchmark
		if(!empty($benchmark))
			Profiler::stop($benchmark);

		self::dlog($method, $line, "========== finished".(null!==$parms? ", returning: " : ""), $parms);
	}

	/**
	* creates a DEBUG log entry.
	* @param string method the name of the calling method
	* @param integer line "the line number of the caller"
	* @param string logmessage
	* @parameter any dumpvals
	*/
	public static function dlog() {
		if(self::enabled() === false) return;

		$args = func_get_args();
		$newargs = array(LOG::DEBUG);
		self::logger(array_merge($newargs, $args));
	}

	/**
	* creates a ERROR log entry.
	* @param string method the name of the calling method
	* @param integer line "the line number of the caller"
	* @param string logmessage
	* @parameter any dumpvals
	*/
	public static function elog() {
		if(self::enabled() === false) return;

		$args = func_get_args();
		$newargs = array(LOG::ERROR);
		self::logger(array_merge($newargs, $args));
	}

	/**
	* creates a WARNING log entry.
	* @param string method the name of the calling method
	* @param integer line "the line number of the caller"
	* @param string logmessage
	* @parameter any dumpvals
	*/
	public static function wlog() {
		if(self::enabled() === false) return;

		$args = func_get_args();
		$newargs = array(LOG::WARNING);
		self::logger(array_merge($newargs, $args));
	}

	/**
	* creates a INFO log entry.
	* @param string method the name of the calling method
	* @param integer line "the line number of the caller"
	* @param string logmessage
	* @parameter any dumpvals
	*/
	public static function ilog() {
		if(self::enabled() === false) return;

		$args = func_get_args();
		$newargs = array(LOG::INFO);
		self::logger(array_merge($newargs, $args));
	}

	/**
	* takes an array as argument for creating a log entry.
	* array items must be in the following order:
	* 0: log level, i.e. debug, info, etc, @see Kohana::LOG documentation
	* 1: method, i.e. the name or reference of the caller
	* 2: line, i.e. the line number of the caller
	* 3: msg, i.e. the message to add to the log
	* []: mixed parms (any number of mixed parms to dump into the log entry)
	*
	*/
	private static function logger() {
		if(self::enabled() === false) return;

		$xargs = func_get_args();
		$args = $xargs[0];

		$level	= array_shift($args);
		$method	= array_shift($args);
		$line 	= array_shift($args);
		$msg	= array_shift($args);

		$dumps	= array();
		$parms	= null;

		if(self::threshold() <= $level)
			return false;

		if(count($args)>0) {
			foreach($args as $key=>$dumpvals) {
				$dumps[] = self::dumpvars($dumpvals);
			}
			$parms = implode("\n", $dumps)."\n";
		}

		LOG::instance()->add($level, $method.":>".$line.": ".$msg.$parms);
	}

	/**
	* takes a mixed argument and returns a 'var_export' of it
	*
	* @param mixed $parms
	*/
	private static function dumpvars($parms){
		if(!empty($parms))
//			$parms = var_export($parms,1);
			$parms = DEBUG::vars($parms);
		else
			$parms = null;

		return $parms;
	}

	private static function enabled() {
		return Kohana::$config->load('debug')->threshold > 0;
	}

	private static function threshold() {
		return Kohana::$config->load('debug')->threshold;
	}
}

?>
