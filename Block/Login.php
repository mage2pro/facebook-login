<?php
namespace Dfe\Facebook\Block;
class Login extends \Magento\Framework\View\Element\Html\Link {
	/**
	 * @override
	 * @see \Magento\Framework\View\Element\Html\Link::toHtml()
	 * @return string
	 */
	public function toHtml() {return !df_customer_logged_in() ? parent::toHtml() : '';}

	/**
	 * @override
	 * @see \Magento\Framework\View\Element\Html\Link::_construct()
	 * @return void
	 */
	protected function _construct() {
		parent::_construct();
		df_metadata('dfe_facebook_url_login', $this->getUrl('dfe-facebook'));
	}
}
