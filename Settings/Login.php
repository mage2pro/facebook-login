<?php
namespace Dfe\FacebookLogin\Settings;
/** @method static Login s() */
class Login extends \Df\Config\Settings {
	/**
	 * @override
	 * @see \Df\Config\Settings::prefix()
	 * @used-by \Df\Config\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_facebook/login';}
}