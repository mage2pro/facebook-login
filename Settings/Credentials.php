<?php
namespace Dfe\FacebookLogin\Settings;
class Credentials extends \Df\Facebook\Settings {
	/** @return string */
	public function appSecret() {return $this->p('app_secret');}

	/** @return $this */
	public static function s() {static $r; return $r ? $r : $r = df_o(__CLASS__);}
}