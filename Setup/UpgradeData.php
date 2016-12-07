<?php
namespace Dfe\FacebookLogin\Setup;
class UpgradeData extends \Df\Sso\Upgrade\Data {
	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Upgrade\Data::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 * @return void
	 */
	protected function _process() {
		parent::_process();
		if ($this->isInitial()) {
			$this->attribute(UpgradeSchema::F__FULL_NAME, 'User Full Name');
			$this->attribute(UpgradeSchema::F__PICTURE, 'User Profile Picture');
			$this->attribute(UpgradeSchema::F__LONG_LIVED_ACCESS_TOKEN, 'Long-lived Access Token');
		}
	}

	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Upgrade\Data::labelPrefix()
	 * @used-by \Df\Sso\Upgrade\Data::attribute()
	 * @return string
	 */
	protected function labelPrefix() {return 'Facebook';}
}