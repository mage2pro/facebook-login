<?php
namespace Dfe\FacebookLogin;
use Dfe\FacebookLogin\Settings as S;
class Button extends \Df\Sso\Button {
	/**
	 * 2016-11-23
	 * @override
	 * @see \Df\Sso\Button::loggedOut()
	 * @used-by \Df\Sso\Button::_toHtml()
	 * @return string
	 */
	protected function loggedOut() {
		/** @var string $domId */
		$domId = df_uid(4, 'dfe-facebook-login-');
		return
			df_x_magento_init(__CLASS__, 'main', [
				'domId' => $domId, 'redirect' => $this->getUrl(df_route(__CLASS__))
			])
			.df_tag('li', ['id' => $domId, 'style' => 'display:none'],
				df_tag('fb:login-button', [
					'onlogin' => 'dfeFacebookLogin()', 'scope' => 'public_profile,email'
				])
			)
			.\Df\Facebook\I::init()
		;
	}
}