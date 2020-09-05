<?php
namespace Dfe\FacebookLogin\Setup;
# 2016-06-05
/** @final Unable to use the PHP «final» keyword here because of the M2 code generation. */
class UpgradeData extends \Df\Sso\Upgrade\Data {
	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Upgrade\Data::_process()
	 * @used-by \Df\Framework\Upgrade::process()
	 */
	final protected function _process() {
		parent::_process();
		if ($this->isInitial()) {
			$this->att(UpgradeSchema::F__FULL_NAME, 'User Full Name');
			$this->att(UpgradeSchema::F__PICTURE, 'User Profile Picture');
			$this->att(UpgradeSchema::F__LONG_LIVED_ACCESS_TOKEN, 'Long-lived Access Token');
		}
	}

	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Upgrade\Data::labelPrefix()
	 * @used-by \Df\Sso\Upgrade\Data::attribute()
	 * @return string
	 */
	final protected function labelPrefix() {return 'Facebook';}
}