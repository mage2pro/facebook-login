<?php
namespace Dfe\FacebookLogin\Settings;
/** @method static Login s() */
class Login extends \Df\Core\Settings {
	/** @return bool */
	public function enable() {return $this->b('enable');}

	/**
	 * @override
	 * @used-by \Df\Core\Settings::v()
	 * @return string
	 */
	protected function prefix() {return 'dfe_facebook/login/';}
}