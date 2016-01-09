<?php
namespace Dfe\FacebookLogin\Block;
use Dfe\FacebookLogin\Settings;
class Init extends Template {
	/**
	 * @override
	 * @see Template::toHtml()
	 * @return string
	 */
	public function toHtml() {
		return
			Settings\Login::s()->enable() || Settings\Like::s()->enabledOnProductPage()
			? parent::toHtml()
			: ''
		;
	}
}
