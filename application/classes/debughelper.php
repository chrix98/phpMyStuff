<?php
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

	public static function func_open($method, $line, $parms=null){
		self::ilog($method, $line, "===================================================");
		self::ilog($method, $line, "entered".(!empty($parms)? " with args: " : ""), $parms);

		// Be sure to only profile if it's enabled
		if (Kohana::$profiling === TRUE)
		{
			// Start a new benchmark
			$cat = preg_match("/([a-zA-Z0-9_-])::(.+)/", $method, $matches);
			$benchmark = Profiler::start($matches[1], $method);
		}

		return $benchmark;
	}

	public static function func_close($method, $line, $benchmark=null, $parms=null){
		// stop the benchmark
		if(!empty($benchmark))
			Profiler::stop($benchmark);

		self::ilog($method, $line, "finished".(!empty($parms)? ", returning: " : ""), $parms);
		self::ilog($method, $line,"===================================================");
	}

	public static function dlog($method, $line, $msg, $parms=null){
		LOG::instance()->add(LOG::DEBUG, $method.":>".$line.": ".$msg."\n".self::dumpvars($parms));
	}

	public static function elog($method, $line, $msg, $parms=null){
		LOG::instance()->add(LOG::ERROR, $method.":>".$line.": ".$msg."\n".self::dumpvars($parms));
	}

	public static function wlog($method, $line, $msg, $parms=null){
		LOG::instance()->add(LOG::WARNING, $method.":>".$line.": ".$msg."\n".self::dumpvars($parms));
	}

	public static function ilog($method, $line, $msg, $parms=null){
		LOG::instance()->add(LOG::INFO, $method.":>".$line.": ".$msg."\n".self::dumpvars($parms));
	}

	public static function dumpvars($parms){
		if(!empty($parms))
			$parms = var_export($parms,1);
		else
			$parms = null;

		return $parms;
	}
}

?>
