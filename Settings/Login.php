<?php
namespace Dfe\FacebookLogin\Settings;
/** @method static Login s() */
class Login extends \Df\Core\Settings {
	/**
	 * @override
	 * @see \Df\Core\Settings::prefix()
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_facebook/login/';}
}