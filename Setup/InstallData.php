<?php
namespace Dfe\FacebookLogin\Setup;
class InstallData extends \Df\Sso\Install\Data {
	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Install\Data::_install()
	 * @used-by \Df\Sso\Install\Data::install()
	 * @return void
	 */
	protected function _install() {
		$this->attribute(InstallSchema::F__PICTURE, 'User Profile Picture');
		$this->attribute(InstallSchema::F__LONG_LIVED_ACCESS_TOKEN, 'Long-lived Access Token');
	}

	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Install\Data::labelPrefix()
	 * @used-by \Df\Sso\Install\Data::attribute()
	 * @return string
	 */
	protected function labelPrefix() {return 'Facebook';}

	/**
	 * 2016-06-05
	 * @override
	 * @see \Df\Sso\Install\Data::schemaClass()
	 * @used-by \Df\Sso\Install\Data::schema()
	 * @return string
	 */
	protected function schemaClass() {return InstallSchema::class;}
}