<?php
namespace Dfe\FacebookLogin\Controller\Index;
use Df\Customer\External\ReturnT;
use Dfe\FacebookLogin\User;
use Dfe\FacebookLogin\Setup\InstallSchema;
use Magento\Customer\Model\Session;
/**
 * @method User c()
 */
class Index extends ReturnT {
	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\External\ReturnT::customerClass()
	 * @used-by \Df\Customer\External\ReturnT::c()
	 * @return string
	 */
	protected function customerClass() {return User::class;}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\External\ReturnT::customerIdFieldName()
	 * @used-by \Df\Customer\External\ReturnT::customer()
	 * @return string
	 */
	protected function customerIdFieldName() {return InstallSchema::F__TOKEN_FOR_BUSINESS;}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\External\ReturnT::customerIdFieldValue()
	 * @used-by \Df\Customer\External\ReturnT::customer()
	 * @return string
	 */
	protected function customerIdFieldValue() {return $this->c()->tokenForBusiness();}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\External\ReturnT::customerData()
	 * @used-by \Df\Customer\External\ReturnT::customer()
	 * @used-by \Df\Customer\External\ReturnT::register()
	 * @return array(string => mixed)
	 */
	protected function customerData() {return [
		InstallSchema::F__FULL_NAME => $this->c()->nameFull()
		, InstallSchema::F__LONG_LIVED_ACCESS_TOKEN => $this->c()->longLivedAccessToken()
		, InstallSchema::F__PICTURE => $this->c()->picture()
	];}

	/**
	 * 2016-06-04
	 * @override
	 * @see \Df\Customer\External\ReturnT::redirectUrl()
	 * @used-by \Df\Customer\External\ReturnT::execute()
	 * @return string
	 */
	protected function redirectUrl() {return df_request('url');}
}
