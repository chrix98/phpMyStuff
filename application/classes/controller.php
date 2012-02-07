<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller extends Kohana_Controller {

	public function before()
	{
//		$this->session = Session::instance();
//		$this->auth = Auth_ORM::instance();
//		$this->user = $this->auth->get_user();
	}

	public function action_change_language()
	{
		$lang = $this->request->param('lang');
		
		$valid_languages = Kohana::$config->load('languages.valid_languages');

		if(!in_array($lang, $valid_languages))	{
			Log::instance()->add(LOG_DEBUG, 'chosen language invalid: '. var_export($lang,1));
			$lang = Kohana::$config->load('languages.system_default_language');
		}else{
			Log::instance()->add(LOG_DEBUG, 'chosen language accepted.'. var_export($lang,1));
		}
		Cookie::delete('lang');
		Cookie::set('lang', $lang);
		I18n::lang($lang);

		$this->request->redirect($this->request->referrer());
	}

	public function after()
	{
		// Write the updated language file, if necessary
		I18n::write();
	}
}
