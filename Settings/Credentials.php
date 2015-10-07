<?php
namespace Dfe\Facebook\Settings;
class Credentials extends \Df\Core\Settings {
	/** @return string */
	public function appId() {return $this->v('app_id');}

	/** @return string */
	public function appSecret() {return $this->p('app_secret');}

	/**
	 * @override
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_facebook/credentials/';}

	/** @return \Dfe\Facebook\Settings\Credentials */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}