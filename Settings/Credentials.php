<?php
namespace Dfe\FacebookLogin\Settings;
/** @method static Credentials s() */
final class Credentials extends \Dfe\Facebook\Settings {
	/**
	 * @used-by \Dfe\FacebookLogin\Customer::req()
	 * @used-by \Dfe\FacebookLogin\Customer::longLivedAccessToken()
	 */
	function appSecret():string {return $this->p('app_secret');}
}