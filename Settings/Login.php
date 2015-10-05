<?php
namespace Dfe\Facebook\Settings;
class Login extends \Df\Core\Settings {
	/** @return bool */
	public function enabled() {return !!$this->v('enable');}

	/**
	 * @override
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_facebook/login/';}

	/** @return \Dfe\Facebook\Settings\Login */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}