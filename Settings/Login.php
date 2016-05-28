<?php
namespace Dfe\FacebookLogin\Settings;
class Login extends \Df\Core\Settings {
	/** @return bool */
	public function enable() {return $this->b('enable');}

	/**
	 * @override
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_facebook/login/';}

	/** @return self */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}