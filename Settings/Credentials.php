<?php
namespace Dfe\FacebookLogin\Settings;
/** @method static Credentials s() */
class Credentials extends \Df\Facebook\Settings {
	/** @return string */
	public function appSecret() {return $this->p('app_secret');}
}