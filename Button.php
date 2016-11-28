<?php
namespace Dfe\FacebookLogin;
/** @method \Dfe\FacebookLogin\Settings\Button s() */
class Button extends \Df\Sso\Button\Js {
	/**
	 * 2016-11-26
	 * @override
	 * @see \Df\Sso\Button\Js::attributes()
	 * @used-by \Df\Sso\Button::loggedOut()
	 * @return array(string => string)
	 */
	final protected function attributes() {return parent::attributes() + ['style' => 'display:none'];}

	/**
	 * 2016-11-26
	 * Facebook uses the <fb:login-button> in its own example:
	 * https://developers.facebook.com/docs/facebook-login/web#example
	 * I also had used it before now.
	 * But the Plugin Configurator used a  HTML 5 markup instead,
	 * and I have switched to it from now:
	 * https://developers.facebook.com/docs/facebook-login/web/login-button#plugin-configurator
	 *
	 * All the settings descriptions below are taken from the official documentation:
	 * https://developers.facebook.com/docs/facebook-login/web/login-button#settings
	 * @override
	 * @see \Df\Sso\Button\Js::htmlN()
	 * @used-by \Df\Sso\Button::html()
	 * @return string
	 */
	final protected function htmlN() {return df_tag('div', [
		// 2016-11-25
		// I took this value from the Plugin Configurator.
		'class' => 'fb-login-button'
		// 2016-11-25
		// «If enabled, the button will change to a logout button when the user is logged in.»
		,'data-auto-logout-link' => 'false'
		// 2016-11-25
		// «Determines what audience will be selected by default,
		// when requesting write permissions.»
		,'data-default_audience' => 'friends'
		// 2016-11-25
		// «A JavaScript function to trigger when the login process is complete.»
		,'data-onlogin' => 'dfeFacebookLogin()'
		// 2016-11-25
		// «The maximum number of rows of profile photos in the Facepile
		// when show_faces is enabled.
		// The actual number of rows shown may be shorter
		// if there aren't enough faces to fill the number you specify.»
		,'data-max-rows' => 1
		// 2016-11-25
		// «The list of permissions to request during login.»
		,'data-scope' => 'public_profile,email'
		// 2016-11-25
		// «Determines whether a Facepile of logged-in friends is shown below the button.
		// When this is enabled, a logged-in user will only see the Facepile,
		// and no login or logout button.»
		,'data-show-faces' => 'false'
		// 2016-11-25
		// «Picks one of the size options for the button.»
		// Allowed values: «small», «medium», «large», «xlarge».
		,'data-size' => $this->s()->nativeSize()
	]);}

	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Sso\Button::loggedOut()
	 * @used-by \Df\Sso\Button::_toHtml()
	 * @return string
	 */
	final protected function loggedOut() {return parent::loggedOut() . \Df\Facebook\I::init();}
}