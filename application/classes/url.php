<?php defined('SYSPATH') or die('No direct script access.');

class URL extends Kohana_URL {

	static function current()
	{
		return URL::base();
	}

	static function redirect($url)
	{
		header('Location : '.$url);
	}
}