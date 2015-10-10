<?php
namespace Dfe\Facebook\Block;
use Dfe\Facebook\Settings;
class Init extends Template {
	/**
	 * @override
	 * @see Template::toHtml()
	 * @return string
	 */
	public function toHtml() {
		return
			Settings\Login::s()->enabled() || Settings\Like::s()->enabledOnProductPage()
			? parent::toHtml()
			: ''
		;
	}
}
