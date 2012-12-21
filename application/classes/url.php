<?php defined('SYSPATH') or die('No direct script access.');

class URL extends Kohana_URL {

	static function current()
	{
		return URL::base();
	}

	static function redirect($url)
	{
		DebugHelper::ilog(__METHOD__,__LINE__,'redirecting to:' ,$url);
		Request::current()->redirect($url);
	}
}
