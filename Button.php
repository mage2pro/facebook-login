<?php
namespace Dfe\FacebookLogin;
use Magento\Framework\View\Element\AbstractBlock;
class Button extends AbstractBlock {
	/**
	 * 2016-11-22
	 * @override
	 * @see AbstractBlock::_toHtml()
	 * @return string
	 */
	protected function _toHtml() {
		/** @var string $result */
		if (df_customer_logged_in()) {
			$result = '';
		}
		else {
			/** @var string $domId */
			$domId = df_uid(4, 'dfe-facebook-login-');
			$result =
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
		return $result;
	}
}