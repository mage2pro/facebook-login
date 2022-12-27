<?php
# 2016-11-25
/** @used-by \Df\Sso\Button::s() */
namespace Dfe\FacebookLogin\Settings;
class Button extends \Df\Sso\Settings\Button {
	/**
	 * 2016-11-25
	 * @see \Dfe\FacebookLogin\Source\Button\Size
	 * @used-by \Dfe\FacebookLogin\Button::loggedOut()
	 */
	function nativeSize():string {return $this->v();}
}