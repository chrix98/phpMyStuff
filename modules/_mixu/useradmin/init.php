<?php defined('SYSPATH') or die('No direct access allowed.');

Route::set('user-default', 'user(/<action>(/<provider>))',
	array(
		'action' => '(provider|provider_return|associate|associate_return)',
		'provider' => '.+',
	))
	->defaults(array(
		'controller' => 'user',
		'action'     => 'index',
		'provider'   => NULL,
	));

Route::set('admin_user', 'admin_user(/<action>(/<id>))',
	array(
		'action' => '(edit|delete)',
	))
	->defaults(array(
		'controller' => 'admin_user',
		'action'     => 'index',
		'id'   => NULL,
	));

// Static file serving (CSS, JS, images)
Route::set('css', '<dir>(/<file>)', array('file' => '.+', 'dir' => '(css|img)'))
   ->defaults(array(
		'controller' => 'user',
		'action'     => 'media',
		'file'       => NULL,
		'dir'       => NULL,
	));
