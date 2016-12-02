<?php
namespace Dfe\FacebookLogin\Setup;
class InstallData extends \Df\Sso\Install\Data {
	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Install\Data::_process()
	 * @used-by \Df\Framework\Install::process()
	 * @return void
	 */
	protected function _process() {
		parent::_process();
		if ($this->isInitial()) {
			$this->attribute(InstallSchema::F__FULL_NAME, 'User Full Name');
			$this->attribute(InstallSchema::F__PICTURE, 'User Profile Picture');
			$this->attribute(
				InstallSchema::F__LONG_LIVED_ACCESS_TOKEN, 'Long-lived Access Token'
			);
		}
	}

	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Install\Data::labelPrefix()
	 * @used-by \Df\Sso\Install\Data::attribute()
	 * @return string
	 */
	protected function labelPrefix() {return 'Facebook';}
}