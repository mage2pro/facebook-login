<?php
namespace Dfe\FacebookLogin\Settings;
/** @method static Credentials s() */
final class Credentials extends \Df\Facebook\Settings {
	/** @used-by \Dfe\FacebookLogin\Customer::req() */
	function appSecret():string {return $this->p('app_secret');}
}