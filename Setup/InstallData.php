<?php
namespace Dfe\FacebookLogin\Setup;
class InstallData extends \Df\Customer\External\Install\Data {
	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Customer\External\Install\Data::_install()
	 * @used-by \Df\Customer\External\Install\Data::install()
	 * @return void
	 */
	protected function _install() {
		$this->attribute(InstallSchema::F__PICTURE, 'User Profile Picture');
		$this->attribute(InstallSchema::F__LONG_LIVED_ACCESS_TOKEN, 'Long-lived Access Token');
	}

	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Customer\External\Install\Data::labelPrefix()
	 * @used-by \Df\Customer\External\Install\Data::attribute()
	 * @return string
	 */
	protected function labelPrefix() {return 'Facebook';}

	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Customer\External\Install\Data::schemaClass()
	 * @used-by \Df\Customer\External\Install\Data::schema()
	 * @return string
	 */
	protected function schemaClass() {return InstallSchema::class;}
}