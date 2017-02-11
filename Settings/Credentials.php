<?php
namespace Dfe\FacebookLogin\Settings;
/** @method static Credentials s() */
final class Credentials extends \Df\Facebook\Settings {
	/** @return string */
	function appSecret() {return $this->p('app_secret');}
}